<?php


namespace App\Presenters;


use Carbon\Carbon;

class Stock extends Presenter
{

    public function priceDate()
    {
        return Carbon::parse(key($this->entity->price()))
            ->formatLocalized('%d.%m.%Y');
    }

    public function isin()
    {
        return $this->entity->isin;
    }

    public function price()
    {
        return $this->formatPrice($this->entity->price(), $this->entity->currency->code);
    }

}