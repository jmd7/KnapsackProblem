<?php
namespace rg4\knapsack;

require_once 'Autoloader.php';

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

        for ($i = 1; $i <= $N; $i++) {
            self::fillItem($items[$i-1], $i, $V, $f, $g, $loop_count);
        }

        // print_r($f); print_r($g);

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

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, $reserve = null) {
        for ($v = $item->getCost(); $v <= $V; $v++) {
            for ($k = 1; $k <= $item->getCount() && $k*$item->getCost() <= $v; $k++) {
                $left = is_null($f[$i-1][$v-$k*$item->getCost()]) ? null : $f[$i-1][$v-$k*$item->getCost()] + $k*$item->getValue();
                $right = ($k == 0) ? $f[$i-1][$v] : $f[$i][$v];

                $f[$i][$v] = self::kp_max($left, $right);
                if ($f[$i][$v] == $left) {
                    $g[$i][$v] = $g[$i-1][$v-$k*$item->getCost()];
                    for ($gg = 0; $gg < $k; $gg++) array_push($g[$i][$v], $i);
                } else $g[$i][$v] = ($k == 0) ? $g[$i-1][$v] : $g[$i][$v];
                
                $loop_count++;
            }
        }
    }
    
}

// $items[] = new KnapsackItem("栗子", 4, 4500, 5);
$items[] = new KnapsackItem("苹果", 5, 5700, 7);
// $items[] = new KnapsackItem("橘子", 2, 2300, 7);
$items[] = new KnapsackItem("草莓", 3, 1200, 5);
// $items[] = new KnapsackItem("甜瓜", 6, 5600, 2);

$pack = new KnapsackPack("背包", 29);

// //$items[] = new KnapsackItem("栗子", 4, 4500, INFINITE);
// $items[] = new KnapsackItem("苹果", 5, 5700, 5);
// $items[] = new KnapsackItem("橘子", 2, 2270, 5);
// //$items[] = new KnapsackItem("草莓", 1, 1100, INFINITE);
// //$items[] = new KnapsackItem("甜瓜", 6, 5600, INFINITE);

// $pack = new KnapsackPack("背包", 14);

MultiplePack_Solution_01::run($items, $pack, false);
MultiplePack_Solution_01::run($items, $pack, true);
?>