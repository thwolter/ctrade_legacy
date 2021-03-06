<?php

use Illuminate\Database\Seeder;
use App\Entities\Portfolio;
use App\Entities\Category;
use App\Entities\User;
use App\Entities\Currency;
use App\Entities\PortfolioImage;
use App\Entities\Stock;
use App\Facades\Datasource;
use Illuminate\Support\Facades\Log;

class PortfolioSeeder extends Seeder
{

    /**
     * path of source images relative to the resources path
     * 
     * @var string
     */
    protected $imgSource = 'assets/img/examples/';
    
    
    /**
     * target path relative to the storage path to copy images
     * 
     * @var string
     */
    protected $imgTarget = 'public/images/';


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereName('examples')->first();

        $portfolio = $this->savePortfolio($user, [
            'name' => 'Dax Werte',
            'cash' => 1000,
            'img' => 'green-energy.jpg',
            'description' => 'Das Portfolio enthält 10 Werte aus dem Deutschen Aktienindex',
            'category' => 'Dax',
            'currency' => 'EUR'
        ]);
        
        $this->assignStocks($portfolio, [
            ['SSE/5GN', 10], 
            ['SSE/YB1',5]
        ]);
       
       
        $portfolio = $this->savePortfolio($user, [
            'name' => 'Andere Werte',
            'cash' => 1000,
            'img' => 'car-fuel.jpg',
            'description' => 'Das Portfolio enthält 10 Werte aus dem Deutschen Aktienindex',
            'category' => 'Dax',
            'currency' => 'EUR'
        ]);

        $portfolio = $this->savePortfolio($user, [
            'name' => 'Und noch mehr',
            'cash' => 1000,
            'img' => 'laptop.jpg',
            'description' => 'Das Portfolio enthält 10 Werte aus dem Deutschen Aktienindex',
            'category' => 'Dax',
            'currency' => 'EUR'
        ]);
    }


    public function savePortfolio($user, $parm)
    {
        $portfolio = new Portfolio([
            'name' => $parm['name'],
            'cash' => $parm['cash'],
            'description' => $parm['description']
        ]);

        $category = Category::firstOrCreate(['name' => $parm['category']]);
        $portfolio->category()->associate($category);

        $currency = Currency::firstOrCreate(['code' => $parm['currency']]);
        $portfolio->currency()->associate($currency);

        $user->portfolios()->save($portfolio);

        $this->saveImage($portfolio, $parm['img']);
        
        return $portfolio;
    }



    /**
     * stores the image in the public storage directory assigns it to the portfolio
     * 
     * @param Portfolio $portfolio
     * @param string $img the image name
     * 
     * @return bool
     */ 
    private function saveImage($portfolio, $img)
    {
        $source = resource_path($this->imgSource . $img);
        $target = storage_path('app/'.$this->imgTarget.$img);
        
        Storage::makeDirectory($this->imgTarget);
        
        if (!File::copy($source, $target))
        {
            die("couldn't copy {$source}");
        }

        $image = new PortfolioImage(['path' => $img]);
        return $portfolio->image()->save($image);
    }
    
    
    private function assignStocks($portfolio, $stocks)
    {
        foreach ($stocks as $stock)
        {
            try {
                $id = Datasource::withDatasetOrFail($stock[0])->first()->id;
            } catch (\Exception $e) {
                echo "-- {$e->getMessage()}\n";
                Log::error('PortfolioSeeder could not assign stock: '.$e->getMessage());
                $id = null;
            }
            
            if (!is_null($id)) {
     
                $position = $portfolio->makePosition(Stock::find($id));
                $portfolio = Portfolio::buy($position->id, $stock[1]);
            }
        }
    }
}
