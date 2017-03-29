<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class MultiplePack_Solution_03 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        self::convertTo01Pack($items, $pack->getVolume(), "Cost");
        return ZeroOnePack_Solution_final::fillPack($items, $pack, $fitPackVolume);
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        return;
    }
}
?>