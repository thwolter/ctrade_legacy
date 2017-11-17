<?php

namespace App\Http\Controllers;

use App\Entities\Stock;
use App\Http\Requests\TradeRequest;
use Illuminate\Http\Request;
use App\Entities\Portfolio;
use App\Entities\Position;



class PositionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param string $slug
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Portfolio $portfolio)
    {
        return view('positions.index', array_merge(['portfolio' => $portfolio], $request->all()));
    }

    /**
     * Create a new resource based on given entity and instrument.
     *
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function search(Portfolio $portfolio)
    {
        return view('positions.search', compact('portfolio'));
    }


    public function show(Portfolio $portfolio, $entity, $slug)
    {
        $instrument = resolve('App\\Entities\\'.ucfirst($entity));

        return view('positions.show_'.strtolower($entity), [
            'portfolio' => $portfolio,
            'stock' => $instrument::findBySlug($slug),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param TradeRequest|Request $request
     * @return array
     */
    public function store(TradeRequest $request)
    {
        $portfolio = Portfolio::find($request->portfolioId)->storeTrade($request->all());

        return ['redirect' => route('positions.index', $portfolio->slug)];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $position = Position::find($id)->delete();

        return redirect(route('positions.index', $position->portfolio->id));
    }
}
