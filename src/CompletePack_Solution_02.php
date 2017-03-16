<?php
namespace rg4\knapsack;

require_once 'Autoloader.php';

define('INFINITE', 999999);

class CompletePack_Solution_02 extends AbstractKnapsackSolution {
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
            $f = array_fill(0, $N+1, array_fill(0, $V+1, null));
            for ($i = 0; $i <= $N; $i++) $f[$i][0] = 0;
        } else $f = array_fill(0, $N+1, array_fill(0, $V+1, 0));
        $g = array_fill(0, $V+1, -1);

        for ($i = 1; $i <= $N; $i++) {
            for ($v = 0; $v <= $V; $v++) {
                $g_org = $g[$v];
                for ($k = 0; $k*$items[$i-1]->getCost() <= $v; $k++) {
                    $left = is_null($f[$i-1][$v-$k*$items[$i-1]->getCost()]) ? null : $f[$i-1][$v-$k*$items[$i-1]->getCost()] + $k*$items[$i-1]->getValue();
                    $right = ($k == 0) ? $f[$i-1][$v] : $f[$i][$v];
                    $left_item = $i;
                    $right_item = ($k == 0) ? $g_org : $g[$v];
                    
                    $f[$i][$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);
                    $loop_count++;
                }
            }
        }

        //print_r($f); print_r($g);

        $res = array();
        $res["Value of best solution"] = $f[$N][$V];
        $res["Items of best solution"] = array();
        $V_real = $V;
        while ($f[$N][$V_real] == $f[$N][$V_real -1]) $V_real--;
        for ($i = $V_real; $i > 0 && $g[$i] >= 0; $i = $i - $items[$g[$i]-1]->getCost()) {
            $res["Items of best solution"][] = $items[$g[$i]-1]->getName();
            // echo $items[$g[$N][$i]]->getName()."\n";
        }
        // $res["Items"] = $items;
        // $res["Pack"] = $pack;

        // $res["Ref - Value array of best solution"] = $f;
        // $res["Ref - Item array of best solution"] = $g;

        //echo "[loop:$loop_count] f[v] = ".$f[$N][$V]."\n";
        $res["Loop count"] = $loop_count;
        return $res;

    }
}

//$items[] = new KnapsackItem("栗子", 4, 4500, INFINITE);
$items[] = new KnapsackItem("苹果", 5, 5700, INFINITE);
//$items[] = new KnapsackItem("橘子", 2, 2250, INFINITE);
//$items[] = new KnapsackItem("草莓", 1, 1100, INFINITE);
$items[] = new KnapsackItem("甜瓜", 6, 5600, INFINITE);

$pack = new KnapsackPack("背包", 6);

CompletePack_Solution_02::run($items, $pack, false);
CompletePack_Solution_02::run($items, $pack, true);
?>