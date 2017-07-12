<?php

namespace App\Http\Controllers;

use App\Entities\Transaction;
use App\Http\Requests\PositionStore;
use App\Http\Requests\PositionUpdate;
use App\Repositories\FinancialMapping;
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        return view('positions.index', compact('portfolio'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param PositionStore|Request $request
     * @return array
     */
    public function store(PositionStore $request)
    {
        $amount = $request->amount;
        $instrument = resolve($request->type)->find($request->id);

        $portfolio = Portfolio::find($request->pid);
        $position = $portfolio->makePosition($instrument);
        $portfolio->buy($position->id, $amount);

        Transaction::buy($portfolio, new \DateTime(), $position, $amount);

        return ['redirect' => route('positions.index', $request->pid)];

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     *
     */
    public function show($pid, $id)
    {
        $position = Position::find($id);
        $portfolio = $position->portfolio;

        return view('positions.show', compact('portfolio', 'position'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param PositionUpdate $request
     * @return array
     */
    public function update(PositionUpdate $request)
    {
        $amount = $request->amount;
        $id = $request->id;

        $position = Position::find($id);
        $portfolio = $position->portfolio;

        switch ($request->transaction) {
            case 'buy':
                $portfolio->buy($id, $amount);
                Transaction::buy($portfolio, new \DateTime(), $position, $amount);
                break;
            case 'sell':
                $portfolio->sell($id, $amount);
                Transaction::sell($portfolio, new \DateTime(), $position, $amount);
                break;
        }

        return ['redirect' => route('positions.index', $portfolio->id)];
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

    public function fetch(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $position = Position::find($request->id);

        $item = $position->positionable->toReadableArray();
        $price = $position->price();
        $amount = $position->amount;
        $cash = $position->portfolio->cash();

        return compact('item', 'price', 'amount', 'cash');

    }
}
