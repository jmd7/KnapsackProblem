<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class MultiplePack_SolutionAll_02 extends AbstractKnapsackSolution {
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
            $g_sum[0][0] = 1;

            $g_all = array_fill(0, $N+1, array_fill(0, $V+1, [[]]));
        }
        $g = array_fill(0, $N+1, array_fill(0, $V+1, array()));

        for ($i = 0; $i < $N; $i++) {
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count, $g_sum, $g_all);
        }

        // self::print_array($f); self::print_array($g_sum); 

        $res = array();
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();

        $V_real = $V;
        $actual_cost = 0;
        while ($f[$V_real] == $f[$V_real -1]) $V_real--;
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
        $f_org = $f;
        $f_v_max = array_fill(0, $V+1, -1);
        for ($z = 0; $z <= $V; $z++) {
            $reserves[0][$i+1][$z] = $reserves[0][$i][$z];
            $reserves[1][$i+1][$z] = $reserves[1][$i][$z];
            $loop_count++;
        }

        for ($k = 1; $k <= $item->getCount() && $k*$item->getCost() <= $V; $k++) {
            for ($v = $k*$item->getCost(); $v <= $V; $v++) {
                $left = is_null($f_org[$v-$k*$item->getCost()]) ? null : $f_org[$v-$k*$item->getCost()] + $k*$item->getValue();
                $right = $f_org[$v];
                
                $tmp = self::kp_max($left, $right);
                if (self::kp_max($f_v_max[$v], $tmp) == $f_v_max[$v]) continue;
                else $f_v_max[$v] = $f[$v] = $tmp;

                if ($f[$v] == $left) {
                    $g[$i+1][$v] = $g[$i][$v-$k*$item->getCost()];
                    for ($gg = 0; $gg < $k; $gg++) array_push($g[$i+1][$v], $i+1);
                } else $g[$i+1][$v] = (empty($g[$i+1][$v])) ? $g[$i][$v] : $g[$i+1][$v];

                if ($f[$v] == $left && $left > $right) {
                    $reserves[0][$i+1][$v] = $reserves[0][$i][$v-$k*$item->getCost()];
                    $reserves[1][$i+1][$v] = $reserves[1][$i][$v-$k*$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) 
                        for ($y = 0; $y < $k; $y++) $reserves[1][$i+1][$v][$x][] = $i+1;
                } else if ($f[$v] == $left && $left == $right) {
                    $reserves[0][$i+1][$v] = $reserves[0][$i][$v-$k*$item->getCost()] + 
                        ($k == 0) ? $reserves[0][$i][$v] : $reserves[0][$i+1][$v];

                    $reserves[1][$i+1][$v] = $reserves[1][$i][$v-$k*$item->getCost()];
                    for($x = 0; $x<count($reserves[1][$i+1][$v]); $x++) 
                        for ($y = 0; $y < $k; $y++) $reserves[1][$i+1][$v][$x][] = $i+1;
                    $reserves[1][$i+1][$v] = array_merge($reserves[1][$i+1][$v], 
                        ($k == 0) ? $reserves[1][$i][$v] : $reserves[1][$i+1][$v]);
                } else if ($f[$v] == $right) {
                    $reserves[0][$i+1][$v] = ($k == 0) ? $reserves[0][$i][$v] : $reserves[0][$i+1][$v];
                    $reserves[1][$i+1][$v] = ($k == 0) ? $reserves[1][$i][$v] : $reserves[1][$i+1][$v];
                }
                
                $loop_count++;
            }
        }
    }

}

?>