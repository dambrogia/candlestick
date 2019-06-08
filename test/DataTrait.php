<?php

namespace Dambrogia\CandlestickTest;

trait DataTrait
{
    public function getDataArray()
    {
        $json = file_get_contents(__DIR__ . '/data.json');
        return json_decode($json, true);
    }

    public function getDataArrayToUpper()
    {
        $json = file_get_contents(__DIR__ . '/data.json');
        return json_decode(strtoupper($json), true);
    }
}
