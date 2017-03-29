<?php
namespace rg4\knapsack;

require_once 'autoload.php';

class MultiplePack_Solution_final extends AbstractKnapsackSolution {
    public static function fillPack(array $items, KnapsackPack $pack, bool $fitPackVolume = false) {
        $N = count($items);
        $V = $pack->getVolume();
        $loop_count = 0;

        $new_items = array();
        $mapping = array();
        foreach ($items as $item) {
            if ($item->getCount() == INFINITE || $item->getCost() * $item->getCount() >= $V) {
                $tmp_item = new KnapsackItem(
                    "[INF]" . $item->getName(),
                    $item->getCost(),
                    $item->getValue(),
                    INFINITE
                );
                $new_items[] = $tmp_item;
                $mapping[$tmp_item->getName()] = $item;
            }
            else {
                $k = 1;
                $cnt = $item->getCount();
                while ($k < $cnt) {
                    $tmp_item = new KnapsackItem(
                        "[X$k]" . $item->getName(),
                        $k * $item->getCost(),
                        $k * $item->getValue(),
                        $k
                    );
                    $new_items[] = $tmp_item;
                    $mapping[$tmp_item->getName()] = $item;
                    $cnt -= $k;
                    $k *= 2;
                }

                $tmp_item = new KnapsackItem(
                    "[X$cnt]" . $item->getName(),
                    $cnt * $item->getCost(),
                    $cnt * $item->getValue(),
                    $cnt
                );
                $new_items[] = $tmp_item;
                $mapping[$tmp_item->getName()] = $item;
            }
        }
        $N = count($new_items);

        if ($fitPackVolume) {
            $f = array_fill(0, $V+1, null);
            $f[0] = 0;
        } else $f = array_fill(0, $V+1, 0);
        $g = array_fill(0, $N, array_fill(0, $V+1, -1));

        for ($i = 0; $i < $N; $i++) {
            self::fillItem($new_items[$i], $i, $V, $f, $g, $loop_count);
        }
        // self::print_array($new_items); self::print_array($g);

        $res = array();
        $res["Value of best solution"] = $f[$V];
        $res["Items of best solution"] = array();

        $i = $N - 1;
        $v = $V;
        while ($i >= 0 && $v > 0) {
            if ($g[$i][$v] < 0) {
                $i--;
                continue;
            }
            $selected = $g[$i][$v];
            $selected_item = $mapping[$new_items[$selected]->getName()];
            $selected_cnt = ($new_items[$selected]->getCount() == INFINITE) ? 1 : $new_items[$selected]->getCount();
            // echo "========= $i\t$v\t".$selected."\t".$selected_cnt."\t".$selected_item.PHP_EOL;
            
            if (!isset($res["Items of best solution"][$selected_item->getName()])) {
                $new_item = clone $selected_item;
                $res["Items of best solution"][$selected_item->getName()] = $new_item->setCount($selected_cnt);
            } else
                $res["Items of best solution"][$selected_item->getName()]->setCount(
                    $res["Items of best solution"][$selected_item->getName()]->getCount()+$selected_cnt);
            
            $v = $v - $new_items[$selected]->getCost();
            if ($new_items[$selected]->getCount() != INFINITE) {
                if ($i > 0) $i = $g[$selected-1][$v];
                //else break;
            }
        }

        $res["Loop count"] = $loop_count;
        return $res;
    }

    public static function fillItem(KnapsackItem $item, $i, $V, &$f, &$g, &$loop_count, &...$reserves) {
        if ($item->getCost() * $item->getCount() >= $V) {
            CompletePack_Solution_final::fillItem($item, $i, $V, $f, $g, $loop_count);
        } else {
            $reserve = $item->getCost();
            ZeroOnePack_Solution_final::fillItem($item, $i, $V, $f, $g, $loop_count, $reserve);
        }
    }
}

?>