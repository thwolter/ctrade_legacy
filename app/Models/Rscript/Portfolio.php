<?php


namespace App\Models\Rscript;


use App\Entities\Currency;
use App\Models\QuantModel;
use Illuminate\Support\Facades\Storage;
use Khill\Lavacharts\Lavacharts;



class Portfolio extends Rscripter
{


    /**
     * @param int $period number of days scaled by sqrt
     * @param double $conf the VaR confidence level
     *
     * @return array $res with calculated risk results
     */
    public function risk($period, $conf)
    {
        $this->storeHistoryFiles();

        return $this->callRscript(['task' => 'risk', 'conf' => $conf]);

    }
    
    
    public function valueHistory($period)
    {
        $this->storeHistoryFiles();
        
        return $this->callRscript($this->tmpDir,
            ['task' => 'valueHistory', 'period' => $period]);
    }


    public function summary()
    {
        $this->storeHistoryFiles();

        return $this->callRscript($this->tmpDir,
            ['task' => 'summary', 'period' => 60, 'conf' => 0.95]);
    }
    
    

    public function storeHistoryFiles()
    {
        $positions = $this->entity->positions;

        foreach ($positions as $position) {

            $this->storePositionHistory($position);
            $this->storeCurrencyHistory($this->entity->currencyCode(), $position->currencyCode());
        }
    }


    protected function storePositionHistory($position)
    {
        $type = $position->positionable_type;
        $id = $position->positionable_id;
        $filename = $this->path("{$type}-{$id}.json");

        if (! file_exists($filename)) {

            Storage::disk('local')->put($filename, json_encode($position->history()));
        }
    }


    protected function storeCurrencyHistory($origin, $target)
    {
        $filename = $this->path("{$origin}.{$target}.json");

        if (! file_exists($filename)) {

            $json = json_encode(QuantModel::ccyHistory($origin, $target));

            Storage::disk('local')->put($filename, $json);
        }
    }


}