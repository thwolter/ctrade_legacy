<?php

namespace App\Http\Controllers;

use App\Library\FinanceRepository;
use Illuminate\Http\Request;
use App\Library\StockRepository;

class InstrumentController extends Controller
{
    protected $instrument;
    protected $blade;


    public function __construct($type) {

        $this->instrument = $this->set($type);
    }


    public static function make($type) {

        return new InstrumentController($type);
    }


    private function set($type) {

        switch ($type) {

            case "S":
                $this->instrument = new StockRepository;
                $this->blade = 'stock';
                break;

            case "E":
                $this->instrument = new ETF;
        }
    }

    public function show($symbol) {

        $summary = $this->instrument->summary($symbol);

        return view('instrument.'.$this->blade, compact['summary']);
    }

}
