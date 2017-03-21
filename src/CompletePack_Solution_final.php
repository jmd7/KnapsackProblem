<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class CompletePack_Solution_final extends AbstractKnapsackSolution {
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
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, $V+1, -1);

        for ($i = 1; $i <= $N; $i++) {
            self::fillItem($items[$i-1], $i, $V, $f, $g, $loop_count);
        }

        // print_r($f); print_r($g);

        $res = array();
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();
        $V_real = $V;
        while ($f[$V_real] == $f[$V_real -1]) $V_real--;
        for ($i = $V_real; $i > 0 && $g[$i] >= 0; $i = $i - $items[$g[$i]-1]->getCost()) {
            if (!isset($res["Items of best solution"][$items[$g[$i]-1]->getName()])) {
                $new_item = clone $items[$g[$i]-1];
                $res["Items of best solution"][$items[$g[$i]-1]->getName()] = $new_item->setCount(1);
            } else
                $res["Items of best solution"][$items[$g[$i]-1]->getName()]->setCount(
                    $res["Items of best solution"][$items[$g[$i]-1]->getName()]->getCount()+1);
            // echo $items[$g[$N][$i]]->getName()."\n";
        }
        $res["Loop count"] = $loop_count;
        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        for ($v = $item->getCost(); $v <= $V; $v++) {
            $left = is_null($f[$v-$item->getCost()]) ? null : $f[$v-$item->getCost()] + $item->getValue();
            $right = $f[$v];
            $left_item = $i;
            $right_item = $g[$v];
            
            $f[$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);
            $loop_count++;
        }
    }
}

?>