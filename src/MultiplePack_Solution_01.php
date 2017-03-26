<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class MultiplePack_Solution_01 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $N+1, array_fill(0, $V+1, null));
            for ($i = 0; $i <= $N; $i++) $f[$i][0] = 0;
        } else $f = array_fill(0, $N+1, array_fill(0, $V+1, 0));
        $g = array_fill(0, $N+1, array_fill(0, $V+1, array()));

        for ($i = 0; $i < $N; $i++) {
            self::fillItem($items[$i], $i, $V, $f, $g, $loop_count);
        }

        // self::print_array($f); self::print_array($g); 

        $res = array();
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();

        $V_real = $V;
        while ($f[$N][$V_real] == $f[$N][$V_real -1]) $V_real--;
        foreach ($g[$N][$V_real] as $i) {
            if (!isset($res["Items of best solution"][$items[$i-1]->getName()])) {
                $new_item = clone $items[$i-1];
                $res["Items of best solution"][$items[$i-1]->getName()] = $new_item->setCount(1);
            } else
                $res["Items of best solution"][$items[$i-1]->getName()]->setCount(
                    $res["Items of best solution"][$items[$i-1]->getName()]->getCount()+1);
            // echo $items[$g[$N][$i]]->getName()."\n";
        }
        $res["Loop count"] = $loop_count;
        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = $item->getCost(); $v <= $V; $v++) {
            for ($k = 0; $k <= $item->getCount() && $k*$item->getCost() <= $v; $k++) {
                $left = is_null($f[$i][$v-$k*$item->getCost()]) ? null : $f[$i][$v-$k*$item->getCost()] + $k*$item->getValue();
                $right = ($k == 0) ? $f[$i][$v] : $f[$i+1][$v];

                $f[$i+1][$v] = self::kp_max($left, $right);
                if ($f[$i+1][$v] == $left) {
                    $g[$i+1][$v] = $g[$i][$v-$k*$item->getCost()];
                    for ($gg = 0; $gg < $k; $gg++) array_push($g[$i+1][$v], $i+1);
                } else $g[$i+1][$v] = ($k == 0) ? $g[$i][$v] : $g[$i+1][$v];
                
                $loop_count++;
            }
        }
    }
    
}

?>