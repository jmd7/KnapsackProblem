<?php
namespace rg4\knapsack;

require_once 'Autoloader.php';

class MultiplePack_Solution_02 extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            self::sortItems($items, "Cost");
        }

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, $V+1, -1);

        for ($i = 1; $i <= $N; $i++) {
            for ($v = $items[$i-1]->getCost(); $v <= $V && $v <= $items[$i-1]->getCost()*$items[$i-1]->getCount(); $v++) {
                // for ($k = 1; $k <= $items[$i-1]->getCount() && $k*$items[$i-1]->getCost() <= $v; $k++) {
                $left = is_null($f[$v-$items[$i-1]->getCost()]) ? null : $f[$v-$items[$i-1]->getCost()] + $items[$i-1]->getValue();
                $right = $f[$v];
                $left_item = $i;
                $right_item = $g[$v];
                
                $f[$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);
                $loop_count++;
                //}
            }
        }

        // print_r($f); print_r($g);

        $res = array();
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();

        $V_real = $V;
        //while ($f[$V_real] == $f[$V_real -1]) $V_real--;
        for ($i = $V_real; $i > 0 && $g[$i] >= 0; $i = $i - $items[$g[$i]-1]->getCost()) {
            $res["Items of best solution"][] = $items[$g[$i]-1]->getName();
            // echo $items[$g[$N][$i]]->getName()."\n";
        }
        // $res["Items"] = $items;
        // $res["Pack"] = $pack;

        // $res["Ref - Value array of best solution"] = $f;
        // $res["Ref - Item array of best solution"] = $g;

        // echo "[loop:$loop_count] f[v] = ".$f[$N][$V]."\n";
        $res["Loop count"] = $loop_count;
        return $res;

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

MultiplePack_Solution_02::run($items, $pack, false);
MultiplePack_Solution_02::run($items, $pack, true);
?>