<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 06/08/2017
 * Time: 23:51
 */
include "jsonData.php";
$arrayData = json_decode($jsonData, 1);//json数据解析

$week = $_GET['w'];//获取星期数
$id = $_GET['i'];//获取节数
$today = $_GET['t'];//今日标志位
if ($today == 1) {
    getToday($arrayData);
} else {
    convertData($arrayData, $week, $id);
}

function convertData($arrayData, $week, $id)
{
    $centerLocal = "121.457481,37.474499";//地图中心显示坐标
    $classID = ($id - 1) * 7 + ($week - 1);//将数据转换成相对应的id，例如：周一第2节=>7
    $classLocal = array(
        "0" => array("lng" => "121.458854", "lat" => "37.471774", "name" => "综合楼"),
        "1" => array("lng" => "121.455303", "lat" => "37.475682", "name" => "一教"),
        "2" => array("lng" => "121.455228", "lat" => "37.475921", "name" => "二教"),
        "3" => array("lng" => "121.455346", "lat" => "37.476261", "name" => "三教"),
        "4" => array("lng" => "121.459176", "lat" => "37.476048", "name" => "四教"),
        "5" => array("lng" => "121.459948", "lat" => "37.476423", "name" => "五教"),
        "6" => array("lng" => "121.458886", "lat" => "37.476627", "name" => "六教"),
        "7" => array("lng" => "121.459122", "lat" => "37.476312", "name" => "七教"),
        "8" => array("lng" => "121.455378", "lat" => "37.476696", "name" => "计算机中心"),
        "9" => array("lng" => "121.455474", "lat" => "37.471638", "name" => "外国语学院")
    );
    $classNum = array("78", "260", "139", "104", "182", "50", "178", "163", "50", "50");

    $heatmapData = array();

    for ($i = 0; $i < 10; $i++) {
        $heatmapData[$i]["lng"] = $classLocal[$i]["lng"];
        $heatmapData[$i]["lat"] = $classLocal[$i]["lat"];
        $heatmapData[$i]["count"] = (int)$arrayData[$classID][$i] * (int)$classNum[$i] * 1/2;
        $heatmapData[$i]["class"] = (int)$arrayData[$classID][$i];
        $heatmapData[$i]["name"] = $classLocal[$i]["name"];
    }
    echo json_encode($heatmapData);
}

function getToday($arrayData)
{
    date_default_timezone_set('PRC');
    $week = Date("w");
    $hour = Date("H");
    if ($week == 0) {
        $week = 7;
    }
    if ($hour >= 8 && $hour < 10) {
        $id = 1;
    } elseif ($hour >= 10 && $hour < 12) {
        $id = 2;
    } elseif ($hour >= 14 && $hour < 16) {
        $id = 3;
    } elseif ($hour >= 16 && $hour < 18) {
        $id = 4;
    } elseif ($hour >= 19 && $hour < 21) {
        $id = 5;
    } elseif ($hour >= 21 && $hour < 23) {
        $id = 6;
    } else {
        $id = 0;
    }
    convertData($arrayData, $week, $id);
}

?>