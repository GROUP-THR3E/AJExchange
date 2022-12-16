<?php

namespace GroupThr3e\AJExchange\Util;

class DbUtils
{
    /**
     * Prepares an IN condition for use with pasing varialbes with PDO
     * @param string $column the column to run the condition on
     * @param array $array the array to check
     * @param array $sqlParams an array to store SQL parameters for use with PDO, passed by reference
     * @return string the resulting SQL condition string
     */
    public static function prepareInString(string $column, array $array, array &$sqlParams): string
    {
        // Prepares string for IN condition
        $inQuery = "$column IN (";
        for ($i = 0; $i < sizeof($array); $i++) {
            $inQuery .= ":$column$i,";
            $sqlParams["tag$i"] = $array[$i];
        }
        $inQuery = rtrim($inQuery, ',');
        $inQuery .= ')';
        return $inQuery;
    }
}