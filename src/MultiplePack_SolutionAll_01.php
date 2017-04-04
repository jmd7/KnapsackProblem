<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class MultiplePack_SolutionAll_01 extends AbstractKnapsackSolution {
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
            $g_sum[0][0] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, [[]]));
        }
        $g = array_fill(0, $N+1, array_fill(0, $V+1, array()));

        for ($i = 0; $i < $N; $i++) {
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count, $g_sum, $g_all);
        }

        // self::print_array($g_sum); self::print_array($f); 

        $res = array();
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();

        $V_real = $V;
        $actual_cost = 0;
        while ($f[$N][$V_real] == $f[$N][$V_real -1]) $V_real--;
        foreach ($g[$N][$V_real] as $i) {
            if (!isset($res["Items of best solution"][$items[$i-1]->getName()])) {
                $new_item = clone $items[$i-1];
                $res["Items of best solution"][$items[$i-1]->getName()] = $new_item->setCount(1);
            } else
                $res["Items of best solution"][$items[$i-1]->getName()]->setCount(
                    $res["Items of best solution"][$items[$i-1]->getName()]->getCount()+1);
            $actual_cost += $items[$i-1]->getCost();
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
            $reserves[0][$i+1][$v] = $reserves[0][$i][$v];
            $reserves[1][$i+1][$v] = $reserves[1][$i][$v];
            $loop_count++;
        }

        for ($v = $item->getCost(); $v <= $V; $v++) {
            for ($k = 0; $k <= $item->getCount() && $k*$item->getCost() <= $v; $k++) {
                $left = is_null($f[$i][$v-$k*$item->getCost()]) ? null : $f[$i][$v-$k*$item->getCost()] + $k*$item->getValue();
                $right = ($k == 0) ? $f[$i][$v] : $f[$i+1][$v];

                $f[$i+1][$v] = self::kp_max($left, $right);
                if ($f[$i+1][$v] == $left) {
                    $g[$i+1][$v] = $g[$i][$v-$k*$item->getCost()];
                    for ($gg = 0; $gg < $k; $gg++) array_push($g[$i+1][$v], $i+1);
                } else $g[$i+1][$v] = ($k == 0) ? $g[$i][$v] : $g[$i+1][$v];
                
                if ($f[$i+1][$v] == $left && $left > $right) {
                    $reserves[0][$i+1][$v] = $reserves[0][$i][$v-$k*$item->getCost()];
                    $reserves[1][$i+1][$v] = $reserves[1][$i][$v-$k*$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) 
                        for ($y = 0; $y < $k; $y++) $reserves[1][$i+1][$v][$x][] = $i+1;
                } else if ($f[$i+1][$v] == $left && $left == $right) {
                    $reserves[0][$i+1][$v] = $reserves[0][$i][$v-$k*$item->getCost()] + 
                        ($k == 0) ? $reserves[0][$i][$v] : $reserves[0][$i+1][$v];

                    $reserves[1][$i+1][$v] = $reserves[1][$i][$v-$k*$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) 
                        for ($y = 0; $y < $k; $y++) $reserves[1][$i+1][$v][$x][] = $i+1;
                    $reserves[1][$i+1][$v] = array_merge($reserves[1][$i+1][$v], 
                        ($k == 0) ? $reserves[1][$i][$v] : $reserves[1][$i+1][$v]);
                } else if ($f[$i+1][$v] == $right) {
                    $reserves[0][$i+1][$v] = ($k == 0) ? $reserves[0][$i][$v] : $reserves[0][$i+1][$v];
                    $reserves[1][$i+1][$v] = ($k == 0) ? $reserves[1][$i][$v] : $reserves[1][$i+1][$v];
                }
                
                $loop_count++;
            }
        }
    }
    
}

// $items[] = new KI("栗子", 4, 4500, 5);
// $items[] = new KI("苹果", 5, 5700, 7);
// $items[] = new KI("橘子", 2, 2300, 7);
// $items[] = new KI("草莓", 3, 1200, 5);
// $items[] = new KI("甜瓜", 6, 5600, 2);

// $pack = new KP("背包", 43);

// MultiplePack_SolutionAll_01::run($items, $pack, false);

?>