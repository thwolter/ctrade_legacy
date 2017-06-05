<?php

namespace App\Presenters;


class Position extends Presenter
{

    public function total($currencyCode = null)
    {
        if (is_null($currencyCode)) {
            return $this->priceFormat($this->entity->total(), $this->entity->currencyCode());
        } else {
            return $this->priceFormat($this->entity->total($currencyCode), $currencyCode);
        }
    }
}