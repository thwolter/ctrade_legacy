<?php


namespace App\Repositories\Yahoo;

use Carbon\Carbon;


class StockFinancial extends BaseFinancial
{

  
    private function getValue($label, $symbol) {

        return $this->getData('getQuotes', $symbol)['query']['results']['quote'][$label];
    }



    public function price($symbol)
    {
        return $this->getValue('LastTradePriceOnly', $symbol);
    }



    public function name($symbol) {

        return $this->getValue('Name', $symbol);
    }



    public function currency($symbol) {

        return $this->getValue('Currency', $symbol);
    }



    public function type($symbol = null)
    {
        return 'Stock';
    }
    
    
    public function history($symbol, $from = null, $to = null)
    {
        $to = (is_null($to)) ? Carbon::today() : $to;
        $from = (is_null($from)) ? Carbon::today()->addDay(-250) : $from;
        
        $data = $this->client->getHistoricalData($symbol, $from, $to);
        $json = json_encode($data['query']['results']['quote'], JSON_NUMERIC_CHECK);
        
        return $json;
    }
}