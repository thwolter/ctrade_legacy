<?php

namespace Tests\Feature\Services;

use App\Classes\DataProvider\QuandlPriceData;
use App\Classes\Output\Price;
use App\Classes\TimeSeries;
use App\Contracts\DataServiceInterface;
use App\Entities\CcyPair;
use App\Entities\Datasource;
use App\Entities\Stock;
use App\Facades\DataService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\FakeHistoryTrait;


class DataServiceTest extends TestCase
{
    use RefreshDatabase;
    use FakeHistoryTrait;


    public function test_receive_stock_datasource()
    {
        $stock = $this->getFakeStock();

        $this->assertEquals($stock->datasources->first()->id,
            DataService::getDatasource($stock, null)->id
        );

        $this->assertEquals(get_class($stock->datasources->first()),
            get_class(DataService::getDatasource($stock, null))
        );
    }


    public function test_receive_the_ccyPair_datasource()
    {
        $ccyPair = $this->getFakeCcyPair();

        $this->assertEquals($ccyPair->datasources->first()->id,
            DataService::getDatasource($ccyPair, null)->id
        );

        $this->assertEquals(get_class($ccyPair->datasources->first()),
            get_class(DataService::getDatasource($ccyPair, null))
        );
    }


    public function x_test_history_returns_a_stock_timeseries()
    {

        $quandl = $this->getMockForAbstractClass(DataServiceInterface::class);
        $quandl->method('history')->willReturn('hi');


        $history = DataService::history($this->getFakeStock());

        $this->assertEquals(TimeSeries::class, get_class($history));
        $this->assertGreaterThan(1, $history->count());
    }


    public function x_test_history_returns_a_ccyPair_timeseries()
    {
        $history = DataService::history(CcyPair::find(1)->first());

        $this->assertEquals(TimeSeries::class, get_class($history));
        $this->assertGreaterThan(1, $history->count());
    }


    public function x_test_price_returns_the_stock_price()
    {
        $price = DataService::price(Stock::find(1)->first());

        $this->assertEquals(Price::class, get_class($price));
        $this->assertGreaterThan(0, $price->getValue());
    }


    public function x_test_price_returns_the_ccyPair_price()
    {
        $price = DataService::price(CcyPair::find(1)->first());

        $this->assertEquals(Price::class, get_class($price));
        $this->assertGreaterThan(0, $price->getValue());
    }


    private function getFakeStock()
    {
        $stock = factory(Stock::class)->create();
        factory(Datasource::class)->states('Quandl')->create()->assign($stock);

        return $stock;
    }

    private function getFakeCcyPair()
    {
        $ccyPair = factory(CcyPair::class)->create();
        factory(Datasource::class)->create()->assign($ccyPair);

        return $ccyPair;
    }
}