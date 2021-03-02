<?php

namespace ArsoftModules\NotaGenerator\Controllers;

use Illuminate\Support\Facades\DB;

class NotaGenerator {

    private $result;

    public function __construct()
    {
        $this->result = "";
    }

    public function getResult()
    {
        return $this->result;
    }

    /**
     * generate nota
     * 
     * @param $counterLength digits length of counter, ex : 001 -> 3 digits, 00001 -> 5 digits
     * @param $date (nullable) format: Y/m/d
     */
    public function generate(
        string $tableName,
        string $columnName,
        int $counterLength,
        ?string $date
    )
    {
        if (is_null($date)) {
            $date = date('Y/m/d');
        }

        $startCounter = ($counterLength + 11) * -1;
        $lastNota = DB::table($tableName)
            ->whereRaw('substr(' . $columnName . ', ' . $startCounter . ',10) = ' . $date)
            ->get()
            ->last();
        
        if (!is_null($lastNota)) {
            $lastNota = $lastNota->$columnName;
            $lastNumber = (int) substr($lastNota, ($counterLength * -1));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $this->result = $date . str_pad($newNumber, $counterLength, '0', STR_PAD_LEFT);

        return $this;
    }

    public function addPrefix(string $prefix, string $separator)
    {
        $this->result = $prefix . $separator . $this->result;
        return $this;
    }
}