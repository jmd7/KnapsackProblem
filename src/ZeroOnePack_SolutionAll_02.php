<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class ZeroOnePack_SolutionAll_02 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;

            $g_sum = array_fill(0, $N+1, array_fill(0, $V+1, 0));
            $g_sum[0][0] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, array()));
            $g_all[0][0] = [[]];
        } else {
            $f = array_fill(0, $V+1, 0);

            $g_sum = array_fill(0, $N+1, array_fill(0, $V+1, 0));
            for ($n = 0; $n <= $V; $n++) $g_sum[0][$n] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, [[]]));
        }
        $g = array_fill(0, $N, array_fill(0, $V+1, -1));

        for ($i = 0; $i < $N; $i++) {
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count, $g_sum, $g_all);
        }

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();

        $i = $N - 1;
        $v = $V;
        $actual_cost = 0;
        while ($i >= 0 && $v > 0 && $g[$i][$v] >= 0) {
            $selected = $g[$i][$v];
            $res["Items of best solution"][] = $items[$selected];
            $actual_cost += $items[$selected]->getCost();
            $v = $v - $items[$selected]->getCost();
            if ($i > 0) $i = $g[$selected-1][$v];
            else break;
        }

        $res["Cost of best solution"] = $actual_cost;
        $res["Count of best solution"] = $g_sum[$N][$V];

        $g_all_best = $g_all[$N][$V];
        asort($g_all_best);
        foreach ($g_all_best as $arr) {
            $sol = [];
            foreach ($arr as $v) $sol[] = $items[$v];
            $res["Best Solutions"][] = $sol;
        }

        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = 0; $v <= $V; $v++) {
            $reserves[0][$i+1][$v] = $reserves[0][$i][$v];
            $reserves[1][$i+1][$v] = $reserves[1][$i][$v];
            $loop_count++;
        }

        for ($v = $V; $v >= $item->getCost(); $v--) {
            $left = is_null($f[$v-$item->getCost()]) ? null : $f[$v-$item->getCost()] + $item->getValue();
            $right = $f[$v];
            $left_item = is_null($f[$v-$item->getCost()]) ? -1 : $i;
            $right_item = ($i == 0) ? -1 : $g[$i-1][$v];
            
            $f[$v] = self::kp_max_tracing($left, $right, $g[$i][$v], $left_item, $right_item);

            if ($f[$v] == $left && $left > $right) {
                $reserves[0][$i+1][$v] = $reserves[0][$i][$v-$item->getCost()];
                $reserves[1][$i+1][$v] = $reserves[1][$i][$v-$item->getCost()];
                for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) $reserves[1][$i+1][$v][$x][] = $i;
            } else if ($f[$v] == $left && $left == $right) {
                $reserves[0][$i+1][$v] = $reserves[0][$i][$v-$item->getCost()] + $reserves[0][$i][$v];

                $reserves[1][$i+1][$v] = $reserves[1][$i][$v-$item->getCost()];
                for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) $reserves[1][$i+1][$v][$x][] = $i;
                $reserves[1][$i+1][$v] = array_merge($reserves[1][$i+1][$v], $reserves[1][$i][$v]);
            } else if ($f[$v] == $right) {
                $reserves[0][$i+1][$v] = $reserves[0][$i][$v];
                $reserves[1][$i+1][$v] = $reserves[1][$i][$v];
            }

            $loop_count++;
        }
    }
}

?>