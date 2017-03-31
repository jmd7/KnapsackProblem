<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class ZeroOnePack_SolutionAll_01 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $N+1, array_fill(0, $V+1, null));
            for ($i = 0; $i <= $N; $i++) $f[$i][0] = 0;

            $g_sum = array_fill(0, $N+1, array_fill(0, $V+1, 0));
            $g_sum[0][0] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, array()));
            $g_all[0][0] = [[]];
        } else {
            $f = array_fill(0, $N+1, array_fill(0, $V+1, 0));

            $g_sum = array_fill(0, $N+1, array_fill(0, $V+1, 0));
            for ($n = 0; $n <= $V; $n++) $g_sum[0][$n] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, [[]]));
        }
        $g = array_fill(0, $N+1, array_fill(0, $V+1, -1));
        
        for ($i = 1; $i <= $N; $i++) {
            self::fillItem($items[$i-1], $i, $V, $f, $g, $loop_count, $g_sum, $g_all);
        }
        // self::print_array($f); self::print_array($g_sum);

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();

        $i = $N;
        $v = $V;
        $actual_cost = 0;
        while ($i > 0 && $v > 0) {
            if ($v >= $items[$i-1]->getCost()) {
                if ($g[$i][$v] == 1) {
                    $res["Items of best solution"][] = $items[$i-1]; //sprintf("%s", $items[$i-1]);
                    $actual_cost += $items[$i-1]->getCost();
                    $v = $v - $items[$i-1]->getCost();
                }
            }
            $i--;
        }

        $res["Cost of best solution"] = $actual_cost;
        $res["Count of best solution"] = $g_sum[$N][$V];

        $g_all_best = $g_all[$N][$V];
        asort($g_all_best);
        foreach ($g_all_best as $arr) {
            $sol = [];
            foreach ($arr as $v) $sol[] = $items[$v-1];
            $res["Best Solutions"][] = $sol;
        }

        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = 0; $v <= $V; $v++) {
            $f[$i][$v] = $f[$i-1][$v];
            $reserves[0][$i][$v] = $reserves[0][$i-1][$v];
            $reserves[1][$i][$v] = $reserves[1][$i-1][$v];

            if ($v >= $item->getCost()) {
                $left = is_null($f[$i-1][$v-$item->getCost()]) ? null : $f[$i-1][$v-$item->getCost()] + $item->getValue();
                $right = $f[$i-1][$v];
                
                $f[$i][$v] = self::kp_max($left, $right);
                if ($f[$i][$v] == $left && $left > $right) {
                    $g[$i][$v] = 1;
                    $reserves[0][$i][$v] = $reserves[0][$i-1][$v-$item->getCost()];
                    $reserves[1][$i][$v] = $reserves[1][$i-1][$v-$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i][$v]); $x++) $reserves[1][$i][$v][$x][] = $i;
                } else if ($f[$i][$v] == $left && $left == $right) {
                    $reserves[0][$i][$v] = $reserves[0][$i-1][$v-$item->getCost()] + $reserves[0][$i-1][$v];

                    $reserves[1][$i][$v] = $reserves[1][$i-1][$v-$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i][$v]); $x++) $reserves[1][$i][$v][$x][] = $i;
                    $reserves[1][$i][$v] = array_merge($reserves[1][$i][$v], $reserves[1][$i-1][$v]);
                } else if ($f[$i][$v] == $right) {
                    $reserves[0][$i][$v] = $reserves[0][$i-1][$v];
                    $reserves[1][$i][$v] = $reserves[1][$i-1][$v];
                }
                // echo "$i\t$v\t$left\t$right\t".$f[$i][$v]."\t".$reserves[0][$i][$v].PHP_EOL;
            }
            $loop_count++;
        }
    }
}

?>