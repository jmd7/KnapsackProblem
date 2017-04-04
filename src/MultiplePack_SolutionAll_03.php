<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class MultiplePack_SolutionAll_03 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        self::convertTo01Pack($items, $pack->getVolume(), "Cost");
        return ZeroOnePack_SolutionAll_final::fillPack($items, $pack, $fitPackVolume);
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        return;
    }
}
?>