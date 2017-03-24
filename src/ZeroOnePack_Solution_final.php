<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class ZeroOnePack_Solution_final extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, $N, array_fill(0, $V+1, -1));

        for ($i = 0; $i < $N; $i++) {
            $sum = 0;
            for ($j = $i; $j < $N; $j++) $sum += $items[$j]->getCost();
            $bound = self::kp_max($V-$sum, $items[$i]->getCost());
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count, $bound);
        }

        // self::print_array($f); self::print_array($g); 

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = [];

        $i = $N - 1;
        $v = $V;
        while ($i >= 0 && $v > 0 && $g[$i][$v] >= 0) {
            $selected = $g[$i][$v];
            $res["Items of best solution"][] = $items[$selected];
            $v = $v - $items[$selected]->getCost();
            if ($i > 0) $i = $g[$selected-1][$v];
            else break;
        }

        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = $V; $v >= $reserves[0]; $v--) {
            $left = is_null($f[$v-$item->getCost()]) ? null : $f[$v-$item->getCost()] + $item->getValue();
            $right = $f[$v];
            $left_item = is_null($f[$v-$item->getCost()]) ? -1 : $i;
            $right_item = ($i == 0) ? -1 : $g[$i-1][$v];
            
            $f[$v] = self::kp_max_tracing($left, $right, $g[$i][$v], $left_item, $right_item);
            $loop_count++;
        }
    }
}

?>