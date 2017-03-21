<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class ZeroOnePack_Solution_final extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, count($f), -1);

        $res = array();
        $res["Items of best solution"] = [];
        $callback = function($f_cur,$g_cur) use($items, $N, $V, $f, &$res) {
            $res["Items of best solution"] = [];
            $V_real = $V;
            while ($f_cur[$V_real] == $f_cur[$V_real -1]) $V_real--;
            for ($i = $V; $i > 0 && $g_cur[$i] >= 0; $i = $i - $items[$g_cur[$i]]->getCost()) {
                $res["Items of best solution"][] = $items[$g_cur[$i]]; //sprintf("%s", $items[$g[$i]]);
                // echo $items[$g_cur[$i]]."\n";
            }
        };

        for ($i = 0; $i < $N; $i++) {
            $sum = 0;
            for ($j = $i; $j < $N; $j++) $sum += $items[$j]->getCost();
            $bound = self::kp_max($V-$sum, $items[$i]->getCost());
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count, $bound, $callback);
        }

        // print_r($f); print_r($g);

        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$V];
        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = $V; $v >= $reserves[0]; $v--) {
            $left = is_null($f[$v-$item->getCost()]) ? null : $f[$v-$item->getCost()] + $item->getValue();
            $right = $f[$v];
            $left_item = $i;
            $right_item = $g[$v];
            
            $f[$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);
            if ($v == $V && $f[$v] == $left && $left > $right) $reserves[1]($f, $g);
            $loop_count++;
        }
    }
}

?>