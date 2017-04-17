<?php

namespace Tests\Unit;

use App\Repositories\Yahoo\Exceptions\InvalidInstrumentType;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Repositories\FinancialRepository;


class FinancialRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $stock;
    protected $fx;

    public function setUp()
    {
        parent::setUp();

        $this->stock = new FinancialRepository('Stock',['symbol' => 'ALV.DE']);
        $this->fx = new FinancialRepository('Currency',['symbol' => 'EURUSD']);
    }

    public function test_make_stock_FinancialRepository()
    {
        $stock = FinancialRepository::make('Stock',['symbol' => 'ALV.DE']);
        $this->assertInstanceOf('App\Repositories\FinancialRepository', $stock);
    }

    public function test_make_currency_FinancialRepository()
    {
        $repo = FinancialRepository::make('Currency',['symbol' => 'EURUSD']);
        $this->assertInstanceOf('App\Repositories\FinancialRepository', $repo);
        $this->assertEquals('EUR/USD', $repo->name);
    }


    public function test_stock_price_is_positive_number()
    {
        $this->assertGreaterThan(0, $this->stock->price);
    }

    public function test_stock_name_starts_with_Allianz()
    {

        $this->assertStringStartsWith('ALLIANZ', $this->stock->name);
    }

    public function test_fx_price_is_positive_number()
    {
        $this->assertGreaterThan(0, $this->fx->price);
    }

    public function test_fx_name_starts_is_EURUSD()
    {
        $this->assertStringStartsWith('EUR/USD', $this->fx->name);
    }



    public function test_fx_EURUSD_currency_is_EUR()
    {
        $this->assertEquals('EUR', $this->fx->currency);
    }


    public function test_an_exceptions_is_thrown_for_incorrect_instrument_type()
    {
        new FinancialRepository('z', []);

        $this->expectExceptionCode(InvalidInstrumentType::class);
    }



}
