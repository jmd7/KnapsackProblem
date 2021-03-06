<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class CompletePack_Solution_01 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $N+1, array_fill(0, $V+1, null));
            for ($i = 0; $i <= $N; $i++) $f[$i][0] = 0;
        } else $f = array_fill(0, $N+1, array_fill(0, $V+1, 0));
        $g = array_fill(0, $V+1, -1);

        for ($i = 1; $i <= $N; $i++) {
            self::fillItem($items[$i-1], $i, $V, $f, $g, $loop_count);
        }

        //print_r($f); print_r($g);

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();
        $V_real = $V;
        while ($f[$N][$V_real] == $f[$N][$V_real -1]) $V_real--;
        for ($i = $V_real; $i > 0 && $g[$i] >= 0; $i = $i - $items[$g[$i]-1]->getCost()) {
            if (!isset($res["Items of best solution"][$items[$g[$i]-1]->getName()])) {
                $new_item = clone $items[$g[$i]-1];
                $res["Items of best solution"][$items[$g[$i]-1]->getName()] = $new_item->setCount(1);
            } else
                $res["Items of best solution"][$items[$g[$i]-1]->getName()]->setCount(
                    $res["Items of best solution"][$items[$g[$i]-1]->getName()]->getCount()+1);
            // echo $items[$g[$N][$i]]->getName()."\n";
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
                $loop_count++;
            }
        }
    }
}

?>