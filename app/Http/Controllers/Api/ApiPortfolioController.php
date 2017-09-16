<?php

namespace App\Http\Controllers\Api;

use App\Entities\LimitType;
use App\Entities\Portfolio;
use App\Repositories\CurrencyRepository;
use App\Repositories\LimitRepository;
use App\Repositories\RiskRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiPortfolioController extends ApiBaseController
{

    /**
     * Returns the positions list with price, total and share for portfolio with given id.
     *
     * @param Request $request
     * @return string
     */
    public function assets(Request $request)
    {
        $portfolio = $this->getPortfolio($request);

        $items = [];
        foreach ($portfolio->assets as $asset) {
            $price = $asset->price();
            $array = $asset->toArray();

            $items[] = array_merge($array, [
                'price' => head($price),
                'total' => head($price) * $array['amount'],
                'date' => key($price),
                'currency' => $asset->currency->code
            ]);
        }

        $collection = collect($items)->sortByDesc('total');
        $total = $collection->sum('total');

        $assets = $collection->toArray();

        foreach ($assets as &$record) {
            $record['share'] = $record['total'] / $total;
        }

        return collect(['assets' => $assets, 'total' => $total]);
    }


    /**
     * Returns the time series of risk for a given portfolio and confidence level
     * from database. Confidence levels can be 0.95, 0.975, or 0.99.
     *
     * @param Request $request
     * @return array
     */
    public function risk(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:portfolios,id',
            'conf' => 'required|numeric'
        ]);

        $values = $this->getPortfolio($request)->keyFigure('risk')->values;

        $result = [];
        for ($i = 0; $i < count($values); $i++) {
            $result[array_keys($values)[$i]] = array_get($values[array_keys($values)[$i]], $request->conf);
        }

        return $result;
    }


    /**
     * Returns the historic values of a given portfolio from database.
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function value(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:portfolios,id',
        ]);

        return collect([
            'values' => $this->getPortfolio($request)->keyFigure('value')->values,
            'risk' => $this->getPortfolio($request)->keyFigure('risk')->values
        ]);
    }


    public function contribution(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:portfolios,id',
            'date' => 'required|date',
            'conf' => 'required|numeric'
        ]);

        $contribution = $this->getPortfolio($request)->keyFigure('contribution')->values;

        return array_get(array_get($contribution, $request->date), $request->conf);
    }

    public function limits(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:portfolios,id',
            'date' => 'required|date',
            'count' => 'sometimes|required|numeric'
        ]);

        $limits = new LimitRepository($this->getPortfolio($request));
        return $limits->limitHistory($request->type, $request->date, $request->count);
    }

    public function utilisation(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:portfolios,id',
        ]);

        $portfolio = $this->getPortfolio($request);

        $limits = new LimitRepository($portfolio);
        $result = $limits->utilisation();

        return $result;
    }

    public function keyFigures(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:portfolios,id',
            'date' => 'required|date',
            'conf' => 'required|numeric',
            'count' => 'required|numeric'
        ]);

        $portfolio = $this->getPortfolio($request);

        $values = $portfolio->keyFigure('value')->values;
        $risks = $portfolio->keyFigure('risk')->values;

        return collect([
            'values' => $values,
            'risk' => $this->valueAfterRisk($values, $risks, '0.99'),
            'limit' => $this->limitTimeSeries($values, 4000),
        ]);
    }

    /**
     * Return the history of values after deduction of risk for a given confidence level.
     *
     * @param array $values
     * @param array $risks
     * @return array
     */
    public function valueAfterRisk($values, $risks, $conf)
    {
        $risk = array_combine(array_keys($risks), array_column($risks, $conf));

        $valueAfterRisk = [];
        foreach ($values as $key => $value) {
            $valueAfterRisk[$key] = $value - array_get($risk, $key);
        }
        return $valueAfterRisk;
    }

    /**
     * Return the history of values after deduction of risk for a given confidence level.
     *
     * @param array $values
     * @param float $limit
     * @return array
     */
    public function limitTimeSeries($values, $limit)
    {
        //todo: perhaps better to use array_pad
        $timeSeries = [];
        foreach ($values as $key => $value) {
            $timeSeries[$key] = $limit;
        }
        return $timeSeries;
    }

}
