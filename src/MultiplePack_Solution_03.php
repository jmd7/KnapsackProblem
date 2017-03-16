<?php
namespace rg4\knapsack;

require_once 'Autoloader.php';

class MultiplePack_Solution_03 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        self::convertTo01Pack($items);
        return ZeroOnePack_Solution_final::fillPack($items, $pack, $fitPackVolume);
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, $reserve = null) {
        return;
    }
}

// $items[] = new KnapsackItem("栗子", 4, 4500, 5);
$items[] = new KnapsackItem("苹果", 5, 5700, 7);
// $items[] = new KnapsackItem("橘子", 2, 2300, 7);
$items[] = new KnapsackItem("草莓", 3, 1200, 5);
// $items[] = new KnapsackItem("甜瓜", 6, 5600, 2);

$pack = new KnapsackPack("背包", 29);

// //$items[] = new KnapsackItem("栗子", 4, 4500, INFINITE);
// $items[] = new KnapsackItem("苹果", 5, 5700, 5);
// $items[] = new KnapsackItem("橘子", 2, 2270, 5);
// //$items[] = new KnapsackItem("草莓", 1, 1100, INFINITE);
// //$items[] = new KnapsackItem("甜瓜", 6, 5600, INFINITE);

// $pack = new KnapsackPack("背包", 14);

MultiplePack_Solution_03::run($items, $pack, false);
MultiplePack_Solution_03::run($items, $pack, true);
?>