<?php

namespace App\Services\RiskService;


use App\Entities\Asset;
use MathPHP\Statistics\Descriptive;

interface RiskInterface
{
    public function assetDelta(Asset $asset, $parameter);

    public function assetVaR(Asset $asset, $parameter);

    public function instrumentDelta(Asset $asset, $parameter);

    public function instrumentVaR(Asset $asset, $parameter);

}