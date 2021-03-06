<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class CompletePack_SolutionAll_02 extends AbstractKnapsackSolution {
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
            $f = array_fill(0, $N+1, array_fill(0, $V+1, null));
            for ($i = 0; $i <= $N; $i++) $f[$i][0] = 0;

            $g_sum = array_fill(0, $N+1, array_fill(0, $V+1, 0));
            $g_sum[0][0] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, array()));
            $g_all[0][0] = [[]];
        } else {
            $f = array_fill(0, $N+1, array_fill(0, $V+1, 0));

            $g_sum = array_fill(0, $N+1, array_fill(0, $V+1, 0));
            $g_sum[0][0] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, [[]]));
        }
        $g = array_fill(0, $V+1, -1);

        for ($i = 1; $i <= $N; $i++) {
            self::fillItem($items[$i-1], $i, $V, $f, $g, $loop_count, $g_sum, $g_all);
        }

        //print_r($f); print_r($g);

        $res = array();
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();
        $V_real = $V;
        $actual_cost = 0;
        while ($f[$N][$V_real] == $f[$N][$V_real -1]) $V_real--;
        for ($i = $V_real; $i > 0 && $g[$i] >= 0; $i = $i - $items[$g[$i]-1]->getCost()) {
            if (!isset($res["Items of best solution"][$items[$g[$i]-1]->getName()])) {
                $new_item = clone $items[$g[$i]-1];
                $res["Items of best solution"][$items[$g[$i]-1]->getName()] = $new_item->setCount(1);
            } else
                $res["Items of best solution"][$items[$g[$i]-1]->getName()]->setCount(
                    $res["Items of best solution"][$items[$g[$i]-1]->getName()]->getCount()+1);
            $actual_cost += $items[$g[$i]-1]->getCost();
            // echo $items[$g[$N][$i]]->getName()."\n";
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
                if (!isset($sol[$items[$v-1]->getName()])) {
                    $new_item = clone $items[$v-1];
                    $sol[$items[$v-1]->getName()] = $new_item->setCount(1);
                } else
                    $sol[$items[$v-1]->getName()]->setCount(
                        $sol[$items[$v-1]->getName()]->getCount()+1);
            }
            $res["Best Solutions"][] = $sol;
        }

        return $res;
   }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = 0; $v <= $V; $v++) {
            $g_org = $g[$v];
            for ($k = 0; $k*$item->getCost() <= $v; $k++) {
                $left = is_null($f[$i-1][$v-$k*$item->getCost()]) ? null : $f[$i-1][$v-$k*$item->getCost()] + $k*$item->getValue();
                $right = ($k == 0) ? $f[$i-1][$v] : $f[$i][$v];
                $left_item = $i;
                $right_item = ($k == 0) ? $g_org : $g[$v];
                
                $f[$i][$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);

                if ($f[$i][$v] == $left && $left > $right) {
                    $reserves[0][$i][$v] = $reserves[0][$i-1][$v-$k*$item->getCost()];
                    $reserves[1][$i][$v] = $reserves[1][$i-1][$v-$k*$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i][$v]); $x++) 
                        for ($y = 0; $y < $k; $y++) $reserves[1][$i][$v][$x][] = $i;
                } else if ($f[$i][$v] == $left && $left == $right) {
                    $reserves[0][$i][$v] = $reserves[0][$i-1][$v-$k*$item->getCost()] + 
                        ($k == 0) ? $reserves[0][$i-1][$v] : $reserves[0][$i][$v];

                    $reserves[1][$i][$v] = $reserves[1][$i-1][$v-$k*$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i][$v]); $x++) 
                        for ($y = 0; $y < $k; $y++) $reserves[1][$i][$v][$x][] = $i;
                    $reserves[1][$i][$v] = array_merge($reserves[1][$i][$v], 
                        ($k == 0) ? $reserves[1][$i-1][$v] : $reserves[1][$i][$v]);
                } else if ($f[$i][$v] == $right) {
                    $reserves[0][$i][$v] = ($k == 0) ? $reserves[0][$i-1][$v] : $reserves[0][$i][$v];
                    $reserves[1][$i][$v] = ($k == 0) ? $reserves[1][$i-1][$v] : $reserves[1][$i][$v];
                }
                
                $loop_count++;
            }
        }
    }

}

?>