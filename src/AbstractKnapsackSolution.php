<?php
abstract class AbstractKnapsackSolution {
    private function __construct() {
        //
    }

    abstract public static function ZeroOnePack(array $items, KnapsackPack $pack, bool $fitPackVolume = false);

    static function kp_max($a, $b) {
        if (is_null($a) && is_null($b)) {
            return null;
        } else if (is_null($a)) {
            return $b;
        } else if (is_null($b)) {
            return $a;
        } else {
            return max($a, $b);
        }
    }

    static function kp_max_tracing($a, $b, &$which, $i, $j) {
        if (is_null($a) && is_null($b)) {
            return null;
        } else if (is_null($a)) {
            $which = $j;
            return $b;
        } else if (is_null($b)) {
            $which = $i;
            return $a;
        } else {
            if ($b == max($a, $b)) $which = $j;
            else $which = $i;
            return max($a, $b);
        }
    }

    static function run($items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $time_start = microtime(true);

        $res = static::ZeroOnePack($items, $pack, $fitPackVolume);
        $res["Time Consumed (ms)"] = microtime(true) - $time_start;

        print_r($res);
        //echo "\nTime consumed : $timediff ms.\n\n";
    }
}
?>