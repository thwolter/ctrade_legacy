<?php

namespace App\Entities;

use App\Entities\Traits\UuidModel;
use App\Facades\TimeSeries;
use App\Presenters\Presentable;
use App\Repositories\CurrencyRepository;
use App\Settings\PortfolioSettings;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;


/**
 * App\Entities\Portfolio
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property int|null $category_id
 * @property float $cash
 * @property int $currency_id
 * @property array $settings
 * @property int $public
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Category|null $category
 * @property-read \App\Entities\Currency $currency
 * @property-read mixed $category_name
 * @property-read mixed $image_url
 * @property-read \App\Entities\PortfolioImage $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Keyfigure[] $keyFigures
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Limit[] $limits
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Position[] $positions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Transaction[] $transactions
 * @property-read \App\Entities\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereCash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Portfolio whereUserId($value)
 * @mixin \Eloquent
 */
class Portfolio extends Model
{
    use Presentable, UuidModel, Sluggable, SluggableScopeHelpers, SoftDeletes, CascadeSoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $presenter = \App\Presenters\Portfolio::class;

    protected $fillable = [
        'name',
        'description',
        'settings',
        'img_url'
    ];

    protected $casts = [
        'settings' => 'json'
    ];

    protected $hidden = ['id'];

    protected $cascadeDeletes = [
        'positions',
        'transactions',
        'category',
        'keyFigures',
        'limits',
        'image'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'];

    public $imagesPath = 'public/images';

    protected $transaction;


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->hasOne(PortfolioImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function keyFigures()
    {
        return $this->hasMany(Keyfigure::class);
    }

    public function limits()
    {
        return $this->hasMany(Limit::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function positions()
    {
        return $this->hasManyThrough(Position::class, Asset::class,
            'portfolio_id', 'asset_id');
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function settings($key = null)
    {
        $settings = new PortfolioSettings($this);
        return $key ? $settings->get($key) : $settings;
    }

    public function cash($date = null)
    {
        return $this->payments()
            ->where('executed_at', '<=', Carbon::parse($date)->endOfDay())
            ->sum('amount');
    }

    public function cashFlow($from, $to)
    {
        return $this->payments()->whereBetween('executed_at', [$from, $to])->sum('amount');
    }

    public function totalOfType($type = null)
    {
        $assets = $type ? $this->assets()->ofType($type) : $this->assets();
        $sum = 0;
        foreach($assets->get() as $asset)
        {
            $sum += $asset->value();
        }
        return $sum;
    }

    public function total()
    {
        return $this->totalOfType(null) + $this->cash();
    }


    public function setCurrency($code)
    {
        $this->currency()->associate(Currency::firstOrCreate(['code' => $code]));
    }

    public function saveKeyFigure($key, $value, $date)
    {
        $keyFigure = Keyfigure::make($key, $value, $date);
        $this->keyFigures()->save($keyFigure);

        return $this;
    }


    /**
     * A buy transaction for position with a given id.
     *
     * @param array $attributes
     * @return Portfolio
     */
    public function storeTrade($attributes)
    {
        $position = Position::make([
            'amount' => $attributes['amount'],
            'price' => $attributes['price'],
            'executed_at' => $attributes['executed']
        ]);

        $this->assets()->firstOrCreate([
            'positionable_type' => $attributes['instrumentType'],
            'positionable_id' => $attributes['instrumentId']
        ])->obtain($position);

        $this
            ->payTrade($attributes, $position)
            ->payFees($attributes, $position);

        return $this;
    }


    /**
     * Deposit an amount of cash.
     *
     * @param array $attributes
     * @return $this
     */
    public function deposit($attributes)
    {
        $this->payments()->create([
            'type' => 'deposit',
            'amount' => $attributes['amount'],
            'executed_at' => $attributes['date']
        ]);

        return $this;
    }

    /**
     * Withdraw an amount of cash.
     *
     * @param array $attributes
     * @return $this
     */
    public function withdraw($attributes)
    {
        $this->payments()->create([
            'type' => 'withdraw',
            'amount' => -$attributes['amount'],
            'executed_at' => $attributes['executed']
        ]);

        return $this;
    }

    /**
     * Persist the payment for a trade.
     *
     * @param $attributes
     * @param $position
     * @return $this
     */
    private function payTrade($attributes, $position)
    {
        $this->payments()->create([
            'type' => $attributes['transaction'],
            'amount' => -$attributes['price'] * $attributes['amount'],
            'executed_at' => $attributes['executed']
        ])->position()->associate($position)->save();

        return $this;
    }

    /**
     * Deduct fees from portfolio cash.
     *
     * @param array $attributes
     * @return Portfolio
     */
    public function payFees($attributes, $position = null)
    {
        $this->payments()->create([
            'type' => 'fees',
            'amount' => -$attributes['fees'],
            'executed_at' => $attributes['executed']
        ])->position()->associate($position)->save();

        return $this;
    }



    /* --------------------------------------------
    * Functions for portfolio images
    * --------------------------------------------
    */

    public function addImage(UploadedFile $file)
    {
        $image = PortfolioImage::fromForm($file);
        $file->storeAs($this->imagesPath . '', $image->path);

        return $this->image()->save($image);
    }

    public function updateImage(UploadedFile $file)
    {
        $image = PortfolioImage::fromForm($file);

        \Storage::delete($this->imagesPath . $this->image->path);

        $file->storeAs($this->imagesPath, $image->path);
        $this->image->path = $image->path;

        $this->image->update();

        return $this;
    }

    public function deleteImage()
    {
        \Storage::delete('public/images/' . $this->image->path);

    }


    /**
     * Return the keyFigures of chosen type. If not exists in database it will be craated.
     *
     * @param string $type
     * @return Keyfigure
     */
    public function keyFigure($type)
    {
        $keyFigure = $this->keyfigures()->ofType($type)->first();

        if (!$keyFigure) {
            $keyFigure = new Keyfigure();
            $keyFigure->type()->associate(KeyfigureType::firstOrCreate(['code' => $type]));
            $this->keyFigures()->save($keyFigure);
        }

        return $keyFigure;
    }

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function latestTransactionDate()
    {
        $payment = $this->payments()->latestExecuted();
        $position = $this->positions()->latestExecuted()->first();

        return max(optional($payment)->executed_at, optional($position)->executed_at);
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Ensure that a slug is unique for a given user. Different users may have the same slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $attribute
     * @param array $config
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUniqueSlugConstraints(Builder $query, Model $model, $attribute, $config, $slug)
    {
        $user = $model->user;

        return $query->where('user_id', $user->getKey());
    }



    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getCategoryNameAttribute()
    {
        $default = $this->category;
        return (!is_null($default)) ? $default->name : 'keine Kategorie';
    }

    public function getImageUrlAttribute()
    {
        $file = $this->image;
        return (!is_null($file)) ? 'images/' . $file->path : null;
    }



    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
