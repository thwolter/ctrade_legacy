<?php


namespace App\Classes\Limits;


use App\Classes\Output\Percent;
use App\Classes\Output\Price;
use App\Facades\MetricService\PortfolioMetricService;

class RelativeLimit extends AbstractLimit
{

    public function utilisation()
    {
        $risk = PortfolioMetricService::risk($this->limit->portfolio);

        return new Percent($this->limit->date, $risk / $this->limit->value);
    }


    public function value()
    {
        return new Price($this->limit->date, $this->limit->value, $this->limit->portfolio->currency->code);
    }
}