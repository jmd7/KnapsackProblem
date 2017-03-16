<?php
namespace rg4\knapsack;

require_once 'Autoloader.php';

abstract class AbstractKnapsackSolution {
    private function __construct() {
        //
    }

    abstract public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false);

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

    static function sortItems(&$items, $key = "Cost") {
        if (!is_array($items) || empty($items) || !$items[0] instanceof KnapsackItem) return;
        $method_arr = ["Cost", "Value", "Count", "Name"];
        if (!in_array($key, $method_arr)) return;

        $method = "get" . $key;
        for ($i = 0; $i < count($items); $i++) {
            for ($j = $i+1; $j < count($items); $j++) {
                if ($items[$j]->$method() < $items[$i]->$method()) {
                    $tmp = $items[$i];
                    $items[$i] = $items[$j];
                    $items[$j] = $tmp;
                }
            }
        }
    }

    static function convertTo01Pack(&$items) {
        if (!is_array($items) || empty($items) || !$items[0] instanceof KnapsackItem) return;

        $res_items = array();
        foreach ($items as $item) {
            for ($i = 0; $i < $item->getCount(); $i++)
                $res_items[] = new KnapsackItem($item->getName(), $item->getCost(), $item->getValue(), 1);
        }

        $items = $res_items;
    }

    static function run($items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $time_start = microtime(true);

        $res = static::fillPack($items, $pack, $fitPackVolume);
        $res["Time Consumed (ms)"] = microtime(true) - $time_start;

        print_r($res);
        //echo "\nTime consumed : $timediff ms.\n\n";
    }
}
?>