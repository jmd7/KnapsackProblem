<?php
namespace rg4\knapsack;

require_once 'Autoloader.php';

class MultiplePack_Solution_final extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, $V+1, -1);

        for ($i = 1; $i <= $N; $i++) {
            if ($items[$i-1]->getCost() * $items[$i-1]->getCount() >= $V) {
                CompletePack_Solution_final::fillItem($items[$i-1], $i, $V, $f, $g, $loop_count, $items);
            } else {
                $k = 1;
                $cnt = $items[$i-1]->getCount();
                while ($k < $cnt) {
                    $tmp_item = new KnapsackItem(
                        "[X$k]" . $items[$i-1]->getName(),
                        $k * $items[$i-1]->getCost(),
                        $k * $items[$i-1]->getValue(),
                        1
                    );

                    ZeroOnePack_Solution_final::fillItem($tmp_item, $i, $V, $f, $g, $loop_count, $tmp_item->getCost());
                    $cnt -= $k;
                    $k *= 2;
                }

                $tmp_item = new KnapsackItem(
                    "[X$cnt]" . $items[$i-1]->getName(),
                    $cnt * $items[$i-1]->getCost(),
                    $cnt * $items[$i-1]->getValue(),
                    1
                );

                ZeroOnePack_Solution_final::fillItem($tmp_item, $i, $V, $f, $g, $loop_count, $tmp_item->getCost());
            }
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

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, $reserve = null) {
        return;
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

MultiplePack_Solution_final::run($items, $pack, false);
MultiplePack_Solution_final::run($items, $pack, true);
?>