<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class MixedPack_Solution_final extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        return MultiplePack_Solution_final::fillPack($items, $pack, $fitPackVolume);
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        return;
    }
    
}
?>