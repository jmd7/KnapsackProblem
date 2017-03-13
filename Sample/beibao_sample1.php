<?php
//背包承重上限
$limit = 8;
//物品种类
$total = 5;
//物品
$array = array(
			array("栗子", 4, 4500),
			array("苹果", 5, 5700),
			array("橘子", 2, 2250),
			array("草莓", 1, 1100),
			array("甜瓜", 6, 6700)
			);
//存放物品的数组
$item = array_fill(0, $limit + 1, 0);
//存放价值的数组
$value = array_fill(0, $limit + 1, 0);
$p = $newvalue = 0;			
for ($i = 0; $i < $total; $i++) {
    //echo "i: ".$i."\n";
	for ($j = $array[$i][1]; $j <= $limit; $j++) {
        //echo "j: ".$j."\n";
        //echo "ITEM: ".implode(",",$item)."\n";
        //echo "VALUE: ".implode(",", $value)."\n";

		$p = $j - $array[$i][1];

        //echo "\$p: ".$p."\n";
        //echo "\$value[$p]: ".$value[$p]."\n";
        //echo "\$array[$i][2]: ".$array[$i][2]."\n";
 		
        $newvalue = $value[$p] + $array[$i][2];
        
        //echo "\$newvalue: ".$newvalue."\n";
        //echo "\$value[$j]: ".$value[$j]."\n";
		//找到最优解的阶段
		if ($newvalue > $value[$j]) {
			$value[$j] = $newvalue;
			$item[$j] = $i;
        
            //echo "ITEM: ".implode(",",$item)."\n";
            //echo "VALUE: ".implode(",", $value)."\n";
		}
        //echo "=========================================="."\n";
	}
    //echo "*****************************************"."\n";
}
echo "物品  价格  重量\n";
for ($i = $limit; 1 <= $i; $i = $i - $array[$item[$i]][1]) {
	echo $array[$item[$i]][0] . "  " . $array[$item[$i]][2] . "  " . $array[$item[$i]][1] ."\n";
}
echo "合计  " . $value[$limit];
?>