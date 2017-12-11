<?php

/**
 * @purpose
 *
 * Provides CRUD for
 * portfolio with name and currency
 *
 */

namespace App\Http\Controllers;

use App\Entities\Category;
use App\Entities\Currency;
use App\Entities\PortfolioImage;
use App\Entities\Transaction;
use App\Http\Requests\CreatePortfolio;
use App\Http\Requests\DeletePortfolio;
use App\Http\Requests\PayRequest;
use App\Http\Requests\UpdatePortfolio;
use App\Repositories\Contracts\PortfolioInterface;
use App\Repositories\LimitRepository;
use App\Repositories\PortfolioRepository;
use App\Settings\InitialSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Entities\Portfolio;
use App\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class PortfoliosController extends Controller
{

    protected $repo;


    public function __construct(PortfolioInterface $repo)
    {
        $this->middleware('auth');
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $portfolios = User::findOrFail(auth()->id())->portfolios;

        return view('portfolios.index', compact('portfolios'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::getEnumValuesAsAssociativeArray('code');

        return view('portfolios.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreatePortfolio $request
     * @return array
     */
    public function store(CreatePortfolio $request)
    {
        $portfolio = $this->repo->createPortfolio(auth()->user(), $request->all());

        return redirect()->route('portfolios.show', [$portfolio->slug]);
    }


    /**
     * Display the specified resource.
     *
     * @param $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {
        return view('portfolios.show', compact('portfolio'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Portfolio $portfolio)
    {
        $limit = new LimitRepository($portfolio);

        setActiveTab($request, 'portfolio');

        return view('portfolios.edit', compact('portfolio', 'limit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePortfolio|Request $request
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePortfolio $request, Portfolio $portfolio)
    {
        $portfolio->update($request->only('name', 'description'));

        $portfolio->settings()->merge($request->all());

        return redirect(route('portfolios.edit', $portfolio->slug))
            ->with('success', trans('portfolio.setup.changed'))
            ->with('active_tab', $request->get('active_tab'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $slug)
    {
        if (!$request->confirmed) {
            return redirect(route('portfolios.edit', $slug))
                ->with('delete', 'confirm')
                ->with('danger', 'Bitte bestätigen, dass das Portfolio unwiderruflich gelöscht werden soll.')
                ->with('active_tab', 'portfolio');
        }
        else {
            $portfolio = auth()->user()->portfolios()->whereSlug($slug)->first();
            $portfolio->delete($portfolio->id);
            return redirect(route('portfolios.index'));
        }
    }

    public function addImage(Request $request, $id)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpg,jpeg,png,bmp'
        ]);

        $file = $request->file('file');
        $portfolio = Portfolio::find($id);

        if (is_null($portfolio->image))
            $portfolio->addImage($file);

        else
            $portfolio->updateImage($file);

    }

}

