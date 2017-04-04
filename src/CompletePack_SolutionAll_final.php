<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class CompletePack_SolutionAll_final extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if (!$fitPackVolume) {
            $item_to_be_removed = array();
            for ($i = 0; $i < $N; $i++) {
                if ($items[$i]->getCost() > $V) {
                    $item_to_be_removed[] = $i;
                    continue;
                }

                for ($j = $i + 1; $j < $N; $j++) {
                    if ($items[$i]->getCost() <= $items[$j]->getCost() &&
                        $items[$i]->getValue() >= $items[$j]->getValue())
                            $item_to_be_removed[] = $j;
                    else if ($items[$i]->getCost() >= $items[$j]->getCost() &&
                        $items[$i]->getValue() <= $items[$j]->getValue())
                            $item_to_be_removed[] = $i;
                }
            }
            foreach ($item_to_be_removed as $idx) unset($items[$idx]);
            $items = array_merge($items);
            $N = count($items);
        }

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
            $g_sum[0][0] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, [[]]));
        }
        $g = array_fill(0, $N, array_fill(0, $V+1, -1));

        for ($i = 0; $i < $N; $i++) {
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count, $g_sum, $g_all);
        }

        // self::print_array($f); self::print_array($g); 

        $res = array();
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();

        $i = $N - 1;
        $v = $V;
        $actual_cost = 0;
        while ($i >= 0 && $v > 0) {
            if ($g[$i][$v] < 0) {
                $i--;
                continue;
            }
            $selected = $g[$i][$v];
            if (!isset($res["Items of best solution"][$items[$selected]->getName()])) {
                $new_item = clone $items[$selected];
                $res["Items of best solution"][$items[$selected]->getName()] = $new_item->setCount(1);
            } else
                $res["Items of best solution"][$items[$selected]->getName()]->setCount(
                    $res["Items of best solution"][$items[$selected]->getName()]->getCount()+1);
            $v = $v - $items[$selected]->getCost();
            $actual_cost += $items[$selected]->getCost();
        }

        $res["Loop count"] = $loop_count;

        $res["Cost of best solution"] = $actual_cost;
        $res["Count of best solution"] = $g_sum[$N][$actual_cost];

        $g_all_best = array_unique($g_all[$N][$V], SORT_REGULAR);
        asort($g_all_best);
        foreach ($g_all_best as $arr) {
            $sol = [];
            foreach ($arr as $v) {
                // $sol[] = $items[$v-1];
                if (!isset($sol[$items[$v]->getName()])) {
                    $new_item = clone $items[$v];
                    $sol[$items[$v]->getName()] = $new_item->setCount(1);
                } else
                    $sol[$items[$v]->getName()]->setCount(
                        $sol[$items[$v]->getName()]->getCount()+1);
            }
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

        for ($v = $item->getCost(); $v <= $V; $v++) {
            $left = is_null($f[$v-$item->getCost()]) ? null : $f[$v-$item->getCost()] + $item->getValue();
            $right = $f[$v];
            $left_item = is_null($f[$v-$item->getCost()]) ? -1 : $i;
            $right_item = $g[$i][$v];
            
            $f[$v] = self::kp_max_tracing($left, $right, $g[$i][$v], $left_item, $right_item);

            if ($f[$v] == $left && $left > $right) {
                $reserves[0][$i+1][$v] = $reserves[0][$i+1][$v-$item->getCost()];
                $reserves[1][$i+1][$v] = $reserves[1][$i+1][$v-$item->getCost()];
                for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) $reserves[1][$i+1][$v][$x][] = $i;
            } else if ($f[$v] == $left && $left == $right) {
                $reserves[0][$i+1][$v] = $reserves[0][$i+1][$v-$item->getCost()] + $reserves[0][$i+1][$v];

                $reserves[1][$i+1][$v] = $reserves[1][$i+1][$v-$item->getCost()];
                for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) $reserves[1][$i+1][$v][$x][] = $i;
                $reserves[1][$i+1][$v] = array_merge($reserves[1][$i+1][$v], $reserves[1][$i+1][$v]);
            } else if ($f[$v] == $right) {
                $reserves[0][$i+1][$v] = $reserves[0][$i+1][$v];
                $reserves[1][$i+1][$v] = $reserves[1][$i+1][$v];
            }
            
            $loop_count++;
        }
    }
}

?>