<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class ZeroOnePack_Solution_02 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, $V+1, -1);
        $g_max = [];

        for ($i = 0; $i < $N; $i++) {
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count, $g_max);
        }
        // print_r($f); print_r($g);

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();
        $V_real = $V;
        while ($f[$V_real] == $f[$V_real -1]) $V_real--;
        foreach ($g_max as $gg) {
            for ($i = $V; $i > 0 && $gg[$i] >= 0; $i = $i - $items[$gg[$i]]->getCost()) {
                $res["Items of best solution"][] = $items[$gg[$i]]; //sprintf("%s", $items[$gg[$i]]);
                // echo $items[$gg[$i]]."\n";
            }
        }
        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = $V; $v >= $item->getCost(); $v--) {
            $left = is_null($f[$v-$item->getCost()]) ? null : $f[$v-$item->getCost()] + $item->getValue();
            $right = $f[$v];
            $left_item = $i;
            $right_item = $g[$v];
            
            $f[$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);
            if ($v == $V && $f[$v] == $left && $left > $right) $reserves[0] = [$g];
            $loop_count++;
        }
    }
}

?>