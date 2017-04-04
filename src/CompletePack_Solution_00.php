<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class CompletePack_Solution_00 extends AbstractKnapsackSolution {
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

        // self::print_array($f);

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();

        $i = $N;
        $v = $V;
        while ($i > 0 && $v > 0) {
            while ($v >= $items[$i-1]->getCost() && $f[$i][$v] == $f[$i][$v - $items[$i-1]->getCost()] + $items[$i-1]->getValue()) {
                if (!isset($res["Items of best solution"][$items[$i-1]->getName()])) {
                    $new_item = clone $items[$i-1];
                    $res["Items of best solution"][$items[$i-1]->getName()] = $new_item->setCount(1);
                } else
                    $res["Items of best solution"][$items[$i-1]->getName()]->setCount(
                        $res["Items of best solution"][$items[$i-1]->getName()]->getCount()+1);
                $v = $v - $items[$i-1]->getCost();
            }
            $i--;
        }

        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = 0; $v <= $V; $v++) {
            for ($k = 0; $k*$item->getCost() <= $v; $k++) {
                $left = is_null($f[$i-1][$v-$k*$item->getCost()]) ? null : $f[$i-1][$v-$k*$item->getCost()] + $k*$item->getValue();
                $right = ($k == 0) ? $f[$i-1][$v] : $f[$i][$v];

                $f[$i][$v] = self::kp_max($left, $right);
                $loop_count++;
            }
        }
    }
}

?>