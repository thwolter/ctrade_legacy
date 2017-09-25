<?php


namespace App\Repositories;

use App\Contracts\DataServiceInterface;
use App\Facades\Datasource;
use Clockwork\DataSource\DataSourceInterface;


class CurrencyRepository
{

    protected $origin;
    protected $target;

    protected $baseCurrency = 'EUR';

    
   
    public function __construct($origin, $target)
    {
        $this->origin = $origin;
        $this->target = $target;
    }


    public function price()
    {
        $history = $this->getHistory();
        return [key($history) => head($history)];
    }


    public function history()
    {
        return $this->getHistory();
    }


    private function getHistory()
    {
        if ($this->origin == $this->target):
            return array_pad([], $this->parameter['limit'], 1);

        elseif ($this->origin == $this->baseCurrency): 
            return $this->direct()->history();
        
        elseif ($this->target == $this->baseCurrency): 
            return $this->inverse($this->oblique($this->origin)->history());
        
        else:
            return $this->divide(
                $this->oblique($this->origin)->history(),
                $this->oblique($this->target)->history()
            );
        
        endif;
    }


    private function direct()
    {
        $datasource = Datasource::withDataset($this->origin . $this->target)->first();
        return app(DataServiceInterface::class, [$datasource]);
    }


    private function oblique($currency)
    {
        $datasource = Datasource::withDataset($this->baseCurrency . $currency)->first();
        return app(DataSourceInterface::class, [$datasource]);
    }


    protected function inverse($x)
    {
        return $this->divide(array_pad([], count($x), 1), $x);
    }


    public function divide($x, $y)
    {
        $n = $this->checkLengths($x, $y);
        $quotients = [];
        $values_x = array_values($x);
        $values_y = array_values($y);


        for ($i = 0; $i < $n; $i++) {
                $quotients[$i] = $values_x[$i] / $values_y[$i];
        }
        return array_combine(array_keys($y), $quotients);
    }


    public function checkLengths($x, $y)
    {
        $n = count($x);
        if ($n != count($y))
            throw new \Exception('Lengths of arrays are not equal');

        return $n;
    }

    public function label()
    {
        return $this->origin.$this->target;
    }

    public function allDataHistory($attributes)
    {
        // TODO: Implement allDataHistory() method.
    }
}