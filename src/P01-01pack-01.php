<?php

require_once "KnapsackItem.php";
require_once "KnapsackPack.php";
require_once "AbstractKnapsackSolution.php";

class ZeroOnePack_Solution_01 extends AbstractKnapsackSolution {
    public static function ZeroOnePack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, count($f), -1);

        for ($i = 0; $i < $N; $i++) {
            $sum = 0;
            for ($j = $i; $j < $N; $j++) $sum += $items[$j]->getCost();
            $bound = self::kp_max($V-$sum, $items[$i]->getCost());
            for ($v = $V; $v >= $bound; $v--) {
            //for ($v = $V; $v >= $items[$i]->getCost(); $v--) {
                $left = is_null($f[$v-$items[$i]->getCost()]) ? null : $f[$v-$items[$i]->getCost()] + $items[$i]->getValue();
                $right = $f[$v];
                $left_item = $i;
                $right_item = $g[$v];
                
                $f[$v] = self::kp_max_tracing($left, $right, $g[$v], $left_item, $right_item);
                $loop_count++;
            }
        }

        // print_r($f); print_r($g);

        $res = array();
        $res["Loop count"] = $loop_count;
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();
        $V_real = $V;
        while ($f[$V_real] == $f[$V_real -1]) $V_real--;
        for ($i = $V_real; $i > 0 && $g[$i] >= 0; $i = $i - $items[$g[$i]]->getCost()) {
            $res["Items of best solution"][] = $items[$g[$i]]->getName();
            // echo $items[$g[$i]]->getName()."\n";
        }
        $res["Items"] = $items;
        $res["Pack"] = $pack;

        $res["Ref - Value array of best solution"] = $f;
        $res["Ref - Item array of best solution"] = $g;

        // echo "[loop:$loop_count] f[v] = $f[$V]"."\n";*/
        return $res;
    }
}

$items[] = new KnapsackItem("栗子", 4, 4500, 1);
$items[] = new KnapsackItem("苹果", 5, 5700, 1);
$items[] = new KnapsackItem("橘子", 2, 2250, 1);
$items[] = new KnapsackItem("草莓", 1, 1100, 1);
$items[] = new KnapsackItem("甜瓜", 6, 6700, 1);

$pack = new KnapsackPack("背包", 20);

ZeroOnePack_Solution_01::run($items, $pack, false);
ZeroOnePack_Solution_01::run($items, $pack, true);

?>