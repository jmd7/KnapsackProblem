<?php
namespace rg4\knapsack;

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;

class MultiplePack_SolutionAll_final extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        $new_items = array();
        $mapping = array();
        foreach ($items as $item) {
            if ($item->getCount() == INFINITE || $item->getCost() * $item->getCount() >= $V) {
                $tmp_item = new KnapsackItem(
                    "[INF]" . $item->getName(),
                    $item->getCost(),
                    $item->getValue(),
                    INFINITE
                );
                $new_items[] = $tmp_item;
                $mapping[$tmp_item->getName()] = $item;
            }
            else {
                $k = 1;
                $cnt = $item->getCount();
                while ($k < $cnt) {
                    $tmp_item = new KnapsackItem(
                        "[X$k]" . $item->getName(),
                        $k * $item->getCost(),
                        $k * $item->getValue(),
                        $k
                    );
                    $new_items[] = $tmp_item;
                    $mapping[$tmp_item->getName()] = $item;
                    $cnt -= $k;
                    $k *= 2;
                }

                $tmp_item = new KnapsackItem(
                    "[X$cnt]" . $item->getName(),
                    $cnt * $item->getCost(),
                    $cnt * $item->getValue(),
                    $cnt
                );
                $new_items[] = $tmp_item;
                $mapping[$tmp_item->getName()] = $item;
            }
        }
        $N = count($new_items);

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
            self::fillItem($new_items[$i], $i, $V, $f, $g, $loop_count, $g_sum, $g_all);
        }
        // self::print_array($new_items); self::print_array($g_sum);

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
            $selected_item = $mapping[$new_items[$selected]->getName()];
            $selected_cnt = ($new_items[$selected]->getCount() == INFINITE) ? 1 : $new_items[$selected]->getCount();
            // echo "========= $i\t$v\t".$selected."\t".$selected_cnt."\t".$selected_item.PHP_EOL;
            
            if (!isset($res["Items of best solution"][$selected_item->getName()])) {
                $new_item = clone $selected_item;
                $res["Items of best solution"][$selected_item->getName()] = $new_item->setCount($selected_cnt);
            } else
                $res["Items of best solution"][$selected_item->getName()]->setCount(
                    $res["Items of best solution"][$selected_item->getName()]->getCount()+$selected_cnt);
            
            $actual_cost += $new_items[$selected]->getCost();
            $v = $v - $new_items[$selected]->getCost();
            if ($new_items[$selected]->getCount() != INFINITE) {
                if ($i > 0) $i = $g[$selected-1][$v];
                //else break;
            }
        }

        $res["Loop count"] = $loop_count;

        $res["Cost of best solution"] = $actual_cost;
        $res["Count of best solution"] = $g_sum[$N][$actual_cost];

        $g_all_best = array_unique($g_all[$N][$V], SORT_REGULAR);
        asort($g_all_best); 
        // self::print_array($g_all_best);
        // self::print_array($new_items);
        foreach ($g_all_best as $arr) {
            $sol = [];
            foreach ($arr as $v) {
                $selected_item = $mapping[$new_items[$v]->getName()];
                $selected_cnt = ($new_items[$v]->getCount() == INFINITE) ? 1 : $new_items[$v]->getCount();
                if (!isset($sol[$selected_item->getName()])) {
                    $new_item = clone $selected_item;
                    $sol[$selected_item->getName()] = $new_item->setCount($selected_cnt);
                } else
                    $sol[$selected_item->getName()]->setCount(
                        $sol[$selected_item->getName()]->getCount()+$selected_cnt);
            }
            $res["Best Solutions"][] = $sol;
        }

        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        if ($item->getCost() * $item->getCount() >= $V) {
            CompletePack_SolutionAll_final::fillItem($item, $i, $V, $f, $g, $loop_count, $reserves[0], $reserves[1]);
        } else {
            $reserve = $item->getCost();
            ZeroOnePack_SolutionAll_final::fillItem($item, $i, $V, $f, $g, $loop_count, $reserve, $reserves[0], $reserves[1]);
        }
    }
}

?>