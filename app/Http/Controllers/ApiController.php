<?php

namespace App\Http\Controllers;

use App\Facades\Helpers;
use App\Entities\Stock;
use App\Http\Requests\SearchRequest;
use App\Models\Exceptions\RscriptException;
use App\Models\Rscript;
use App\Repositories\CurrencyRepository;
use Illuminate\Http\Request;
use App\Entities\Portfolio;

class ApiController extends Controller
{

    /**
     * Receive search results from entities.
     *
     * @param SearchRequest $request
     * @return string
     */
    public function search(SearchRequest $request)
    {
        return json_encode(Stock::search($request->get('query'))->get());
    }


    /**
     * receive metadata and price history for an stock with given id.
     *
     * @param Request $request
     * @return string
     */
    public function lookup(Request $request)
    {
        $stock = Stock::find($request->id);

        $prices = [
            ['exchange' => 'Stuttgart', 'price' => $stock->price()],
            // define further exchanges
        ];

        return json_encode([
            'item' => $stock->toReadableArray(),
            'prices' => $prices,
            'history' => $stock->history()
        ]);
    }


    /**
     * Returns the positions list with price, total and share for portfolio with given id.
     *
     * @param Request $request
     * @return string
     */
    public function positions(Request $request)
    {
        $portfolio = $this->getPortfolio($request);

        $items = [];
        foreach ($portfolio->positions as $position) {
            $price = $position->price();
            $array = $position->toArray();

            $items[] = array_merge($array, [
                'price' => head($price),
                'total' => head($price) * $array['amount'],
                'date' => key($price),
                'currency' => $position->currencyCode()
            ]);
        }

        $collection = collect($items)->sortByDesc('total');
        $total = $collection->sum('total');

        $positions = $collection->toArray();

        foreach ($positions as &$record) {
            $record['share'] = $record['total'] / $total;
        }

        return collect(['positions' => $positions, 'total' => $total]);
    }


    /**
     * Provides a collection of histories for the portfolio's risk factors.
     * Request requires the portfolio $id.
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function histories(Request $request)
    {
        $portfolio = $this->getPortfolio($request);

        $days = $this->getWeekDaysSeries($request);

        $result = [];

        foreach ($portfolio->positions as $position) {

            $key = $position->positionable_type . '_' . $position->positionable_id;
            $result[$key] = $position->history($days);

            $origin = $portfolio->currencyCode();
            $target = $position->currencyCode();

            if ($origin != $target)
                $result[$origin . $target] = (new CurrencyRepository($origin, $target))->history($days);
        }

        return collect($result);
    }

    /**
     * Provides the portfolio data including positions details.
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function portfolio(Request $request)
    {
        //Todo: get portfolio for a specified day in the past based on transactions analysis
        return collect($this->getPortfolio($request)->toArray());
    }


    /**
     * Calculate the risk for given portfolio.
     * e.g. api/portfolio/risk?id=1&conf=0.95&from=2016-06-30&to=2017-06-30
     *
     * @param Request $request
     * @return string
     */
    public function portfolioRisk(Request $request)
    {
        /*$this->validate($request, [
            'id' => 'required|exists:portfolios,id',
            'conf' => 'required|between:0,1'
        ]);*/

        $rscript = new Rscript($this->getPortfolio($request));
        $risk = $rscript->portfolioRisk($request->conf, $request->date, $request->count);
        return $risk;
    }


    // ---------------------------------------------
    // private functions
    // ---------------------------------------------


    /**
     * Returns the portfolio for a requested id.
     *
     * @param Request $request
     * @return Portfolio
     */
    private function getPortfolio(Request $request)
    {
        $portfolio = Portfolio::findOrFail($request->get('id'));
        return $portfolio;
    }

    /**
     * Return all week days either within a period or as number up to set date.
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function getWeekDaysSeries(Request $request): array
    {
        if (isset($request->date) && isset($request->count)) {
            $days = Helpers::allWeekDays($request->date, $request->count);
        }
        elseif (isset($request->from) && isset($request->to)) {
            $days = Helpers::allWeekDaysBetween($request->from, $request->to);
        }
        else {
            throw new \Exception("Parameter ['date' and 'count'] or ['from' and 'to'] must be set.");
        }

        return $days;
    }
}