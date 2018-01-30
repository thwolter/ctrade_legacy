<?php


namespace App\Classes\Limits;

use App\Classes\Output\Percent;
use App\Classes\Output\Price;
use App\Entities\Limit;
use App\Facades\PortfolioService;

abstract class AbstractLimit
{
    protected $limit;
    protected $metrics;


    public function __construct(Limit $limit)
    {
        $this->limit = $limit;
    }

    /**
     * Return the limit utilisation.
     *
     * @return Percent
     */
    abstract public function utilisation();

    /**
     * Return the limit value.
     *
     * @return Price|Percent
     */
    abstract public function value();

    abstract public function title();

}