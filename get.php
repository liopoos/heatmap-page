<?php
$week = $_GET['w'];//获取星期数
$id = $_GET['i'];//获取节数
$today = $_GET['t'];//今日标志位
echo file_get_contents("https://api.mayuko.cn/v2/heatmap/?w=".$week."&i=".$id."&t=".$today);
?>