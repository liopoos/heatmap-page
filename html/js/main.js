/**
 * Created by hades on 07/08/2017.
 */
$(document).ready(function () {
    $("#map").height($(window).height());
    $.ajax({
        url: "./get.php",
        data: {
            "t": 1
        },
        success: function (data) {
            init(JSON.parse(data));
        }
    });
    $(".btn").click(function () {
        $.ajax({
            url: "./get.php",
            data: {
                "w": $("#week").val(),
                "i": $("#js").val()

            },
            success: function (data) {
                init(JSON.parse(data));
            }
        })
    })

});
function loadCount(array) {
    var str = "";
    $.each(array, function (i, v) {
        str += "<li class=\"list-group-item\">" + v.name + "：" + v.count + "</li>";
    });
    //console.log(str);
    $("#count").html(str);

}
function loadClass(array) {
    var str = "";
    $.each(array, function (i, v) {
        str += "<li class=\"list-group-item\">" + v.name + "：" + v.class + "</li>";
    });
    //console.log(str);
    $("#class").html(str);

}
function init(heatmapData) {
    loadCount(heatmapData);
    loadClass(heatmapData);
    var map = new AMap.Map("map", {
        resizeEnable: true,
        center: [121.457481, 37.474499],
        zoom: 17,
        doubleClickZoom: false,//禁止双击放大

    });
    if (!isSupportCanvas()) {
        alert('热力图仅对支持canvas的浏览器适用,您所使用的浏览器不能使用热力图功能,请换个浏览器试试~')
    }
    var heatmap;
    map.plugin(["AMap.ToolBar"], function () {
        map.addControl(new AMap.ToolBar({
            liteStyle: true
        }));
    });
    map.plugin(["AMap.Heatmap"], function () {
        //初始化heatmap对象
        heatmap = new AMap.Heatmap(map, {
            radius: 120, //给定半径
            opacity: [0, 0.4],
            gradient: {
                0.5: 'blue',
                0.65: 'rgb(117,211,248)',
                0.7: 'rgb(0, 255, 0)',
                0.9: '#ffea00',
                1.0: 'red'
            },
        });
        heatmap.setDataSet({
            data: heatmapData,
            max: 3000
        });
    });
}

//判断浏览区是否支持canvas
function isSupportCanvas() {
    var elem = document.createElement('canvas');
    return !!(elem.getContext && elem.getContext('2d'));
}