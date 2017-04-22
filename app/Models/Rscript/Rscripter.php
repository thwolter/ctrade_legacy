<?php


namespace App\Models\Rscript;


use Illuminate\Support\Facades\Storage;
use App\Models\Exceptions\RscriptException;

abstract class Rscripter
{
    protected $entity;
    protected $path;
    protected $rapi;
    protected $rbase;


    /**
     * Constructor to set the entity (e.g. Portfolio) and
     * to define required path's to be handed over to Rscript
     *
     * Rscripter constructor.
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;

        $this->rapi = base_path().'/rscripts/rapi.R';
        $this->path = storage_path().'/app/';
        $this->rbase = base_path(). '/rscripts';
    }


    /**
     * Saves the portfolio as json file to the file system
     *
     * @return string with name of the json file
     */
    public function saveJSON()
    {
        $filename = 'tmp/'.uniqid() . '.json';
        Storage::disk('local')->put($filename, json_encode($this->entity->toArray()));

        return $filename;
    }

    /**
     * Transforms array with parameters into a string to be used within exec call of Rscript
     *
     * @param array $args representing named parameters
     * @return string with Rscript arguments
     */
    public function argsImplode($args) {

        $s = null;
        foreach ($args as $key => $value)
        {
            $s = $s."--{$key}={$value} ";
        }
        return $s;
    }


    /**
     * Calls Rscript with an array of arguments to be provided;
     * The functions uses the file system to transfer both portfolio data and results
     *
     * @param array $args representing required arguments for Rscript
     * @return array with result from Rscript
     */
    public function callRscript($args)
    {
        $filename = $this->saveJSON();
        $resfile = 'tmp/'.uniqid() . '.json';

        $callString = sprintf("Rscript --vanilla %s -b %s -f %s -o %s %s",
            $this->rapi, $this->rbase, $this->path.$filename, $this->path.$resfile, $this->argsImplode($args));

        exec($callString);

        $array = json_decode(Storage::read($resfile), true);

        Storage::delete($filename);
        Storage::delete($resfile);

        return $array;
    }


}