<?php
//这是我根据动态规划原理写的
// max(opt(i-1,w),wi+opt(i-1,w-wi))
//背包可以装最大的重量
$w=15;
//这里有四件物品,每件物品的重量
$dx=array(3,4,5,6);
//每件物品的价值
$qz=array(8,7,4,9);
//定义一个数组
$a=array();
//初始化
for($i=0;$i<=15;$i++){ $a[0][$i]=0; }
for ($j=0;$j<=4;$j++){ $a[$j][0]=0; }

//opt(i-1,w),wi+opt(i-1,w-wi)
for ($j=1;$j<=4;$j++){
  for($i=1;$i<=15;$i++){
    $a[$j][$i]=$a[$j-1][$i];
    //不大于最大的w=15
    if($dx[$j-1]<=$w){
      if(!isset($a[$j-1][$i-$dx[$j-1]])) continue;
      //wi+opt(i-1,wi)
      $tmp = $a[$j-1][$i-$dx[$j-1]]+$qz[$j-1];
      //opt(i-1,w),wi+opt(i-1,w-wi) => 进行比较 
      if($tmp>$a[$j][$i]){
        $a[$j][$i]=$tmp;
      }
    }
  }
}
//打印这个数组,输出最右角的值是可以最大价值的
for ($j=0;$j<=4;$j++){
  for ($i=0;$i<=15;$i++){
    echo $a[$j][$i]."\t";
    } echo "\n";
}
?>