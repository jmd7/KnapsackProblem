<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class ZeroOnePack_Solution_00 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $N+1, array_fill(0, $V+1, null));
            for ($i = 0; $i <= $N; $i++) $f[$i][0] = 0;
        } else $f = array_fill(0, $N+1, array_fill(0, $V+1, 0));

        for ($i = 1; $i <= $N; $i++) {
            self::fillItem($items[$i-1], $i, $V, $f, $dummy, $loop_count);
        }

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();

        $i = $N;
        $v = $V;
        while ($i > 0 && $v > 0) {
            if ($v >= $items[$i-1]->getCost()) {
                if ($f[$i][$v] == $f[$i-1][$v - $items[$i-1]->getCost()] + $items[$i-1]->getValue()) {
                    $res["Items of best solution"][] = $items[$i-1]; //sprintf("%s", $items[$i-1]);
                    $v = $v - $items[$i-1]->getCost();
                }
            }
            $i--;
        }
        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = $item->getCost(); $v <= $V; $v++) {
            $left = is_null($f[$i-1][$v-$item->getCost()]) ? null : $f[$i-1][$v-$item->getCost()] + $item->getValue();
            $right = $f[$i-1][$v];
            
            $f[$i][$v] = self::kp_max($left, $right);
            $loop_count++;
        }
    }
}

?>