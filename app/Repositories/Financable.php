<?php

namespace App\Repositories;

use App\Contracts\DataServiceInterface;
use App\Entities\Datasource;
use App\Repositories\Exceptions\FinancialException;

trait Financable
{

    protected $financialInstance;


    public function financial()
    {
        if (!isset($financialInstance)) {

            $datasource = $this->datasources->first();
            $this->financialInstance = app(DataServiceInterface::class, [$datasource]);
        }
        return $this->financialInstance;
    }


    public function __call($name, $arguments)
    {
        if (method_exists(DataServiceInterface::class, $name)) {
            return call_user_func_array([$this->financial(), $name], $arguments);
        }

        return parent::__call($name, $arguments);
    }
}