<?php
namespace rg4\knapsack;

require_once 'Autoloader.php';

class ZeroOnePack_Solution_01 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $N+1, array_fill(0, $V+1, null));
            for ($i = 0; $i <= $N; $i++) $f[$i][0] = 0;
        } else $f = array_fill(0, $N+1, array_fill(0, $V+1, 0));
        $g = array_fill(0, $V+1, -1);

        for ($i = 1; $i <= $N; $i++) {
            self::fillItem($items[$i-1], $i, $V, $f, $g, $loop_count);
        }
        // print_r($f); print_r($g);

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();
        $V_real = $V;
        while ($f[$N][$V_real] == $f[$N][$V_real -1]) $V_real--;
        for ($i = $V_real; $i > 0 && $g[$i] >= 0; $i = $i - $items[$g[$i]-1]->getCost()) {
            $res["Items of best solution"][] = sprintf("%s", $items[$g[$i]-1]);
            // echo $items[$g[$i]]->getName()."\n";
        }
        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, $reserve = null) {
        for ($v = $item->getCost(); $v <= $V; $v++) {
            $left = is_null($f[$i-1][$v-$item->getCost()]) ? null : $f[$i-1][$v-$item->getCost()] + $item->getValue();
            $right = $f[$i-1][$v];
            $left_item = $i;
            $right_item = $g[$v];
            
            $f[$i][$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);
            $loop_count++;
        }
    }
}

$items[] = new KnapsackItem("栗子", 4, 4500, 1);
$items[] = new KnapsackItem("苹果", 5, 5700, 1);
$items[] = new KnapsackItem("橘子", 2, 2250, 1);
$items[] = new KnapsackItem("草莓", 1, 1100, 1);
$items[] = new KnapsackItem("甜瓜", 6, 6700, 1);

$pack = new KnapsackPack("背包", 13);

ZeroOnePack_Solution_01::run($items, $pack, false);
ZeroOnePack_Solution_01::run($items, $pack, true);

?>