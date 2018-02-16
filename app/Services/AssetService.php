<?php


namespace App\Services;


use App\Classes\Output\Percent;
use App\Classes\Output\Price;
use App\Entities\Asset;
use App\Facades\CurrencyService;
use App\Facades\DataService;
use Carbon\Carbon;

class AssetService
{

    /**
     * Return the asset's delta as a percentage.
     *
     * @param Asset $asset
     * @return Percent
     */
    public function returnPercent(Asset $asset, $date = null)
    {
        $delta = $this->returnAbsolute($asset, $date);

        return new Percent($date, $delta->value / $asset->settled($date));
    }


    /**
     * Return the asset's delta between current value and invested amount in portfolio currency.
     *
     * @param Asset $asset
     * @param string $date
     *
     * @return Price
     */
    //todo: shoulc receive $count to calculate return for some days
    public function returnAbsolute(Asset $asset, $date = null)
    {
        $delta = $this->valueAt($asset, $date)->value - $asset->settled($date);
        return new Price($date, $delta, $asset->currency);
    }


    /**
     * Calculates the Asset's value based on latest available price data.
     *
     * @param Asset $asset
     * @param string|null $exchange
     *
     * @return Price
     */
    public function value(Asset $asset, $exchange = null)
    {
        return $this->valueAt($asset, now(), $exchange);
    }

    /**
     * Calculates the Asset's value for a specified date.
     *
     * @param Asset $asset
     * @param string $date
     * @param string|null $exchange
     *
     * @return Price
     */
    public function valueAt(Asset $asset, $date, $exchange = null)
    {
        return $this->priceAt($asset, $date, $exchange)->multiply($asset->numberAt($date));
    }


    /**
     * Return the asset's price at a specified date.
     *
     * @param Asset $asset
     * @param string $date
     * @param string|null $exchange
     *
     * @return Price
     */
    public function priceAt(Asset $asset, $date, $exchange = null)
    {
        $price = DataService::priceAt($asset->positionable, $date, $exchange);

        return $price->multiply($this->getFxRate($asset, $date));
    }


    /**
     * @param Asset $asset
     * @param $date
     * @return int
     */
    private function getFxRate(Asset $asset, $date)
    {
        return $asset->hasForeignCurrency()
            ? CurrencyService::priceAt($asset->currency, $asset->portfolio->currency, $date)->value
            : 1;
    }


    /**
     * Return the risk to value ratio.
     *
     * @param Asset $asset
     * @return Percent
     */
    public function riskToValueRatio(Asset $asset)
    {
        $risk = $this->risk($asset);
        $value = $this->value($asset);

        return new Percent($risk->date, $risk->value / $value->value);
    }


    /**
     * Return the Asset's price.
     *
     * @param Asset $asset
     * @param string|null $exchange
     *
     * @return Price
     */
    public function price(Asset $asset, $exchange = null)
    {
        return $this->priceAt($asset->positionable, now(), $exchange);
    }

}
