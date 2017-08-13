想了半天实在是想不到一个不拗口的名字，简单来说就是：[我们下了课去几餐吃？][1]


背景
--

为了解决世界性的三大难题：

- 去几餐？ 
- 几楼吃？ 
- 吃什么？

每天都是费劲了脑子，虽然学校有10个餐厅，可是对于同时下课的我们，每天中午或晚上餐厅里都是人山人海。根据大家的习惯：在综合楼上课的同学下课后一般会去七餐吃饭，在四教、五教上课的同学一般会去六餐、一餐、二餐吃饭。所以，可以把某节课上课的人数统计起来，利用热力图的显示效果投射在地图上，这样就能避免用餐的高峰期，可以放心去吃饭了。


想法
--

**1.统计课表数据**

Api Center中的[烟大课表查询][2]提供了每个班级的课表数据，所以可以统计出每天的某大节中教学楼的统计数目，比如：统计周一第一大节有多少个班级在综合楼上课。得到所有的统计结果后（忽略单双周的课程问题），我们就可以得到一个大致的教学楼使用情况。

创建一个Python数组用来存放每节课的教学楼统计数量：

    # 创建一个10*42二维数组存储教学楼数据
    # 综合楼：0
    # 一教-七教：1-7
    # 计算机中心：8
    # 外院：9
    roomCount = [[0 for i in range(10)] for i in range(42)]

之后，GET请求课表数据：

    # 获取课表信息
    def resData(id):
        url = 'https://api.mayuko.cn/v2/ytukb/list'
        param = {
            'id': id
        }
        param = urllib.urlencode(param)
        resData = urllib2.urlopen(urllib2.Request(url='%s%s%s' % (url, '?', param)))
        resData = resData.read()
        return resData

得到Json数据的课程信息后，将Json数据解析成Python字典：

    # 将json数据转换成字典
    def convertData(data):
        text = json.loads(data)
        for i in range(41):
            judageRoom(text[str(i)]['0']['kbAddr'], i)

再判断每节课的教学楼进行统计之后，得到了每节课的教学楼统计数量（Json数据格式），它大概长这样：

    [
        [
            102,
            10,
            0,
            13,
            26,
            57,
            11,
            21,
            0,
            3
        ],
        ···
        [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0
        ]]

**2.粗略计算每个教室的人数**

如何判断每个班级有多少人？学校并没有公开每个班级的人数，所以无法精确计算每个教室（比如：综合楼207）的具体上课人数。

Api Center提供了[烟大空闲教室][3]的数据，每天的第12小节（晚上第二大节）学校基本是不会排课的，此时教学楼内的空闲教室座位达到了最大饱和态，利用此时的座位数，可以粗略计算一下教学楼中每个教室的平均人数。

另外，教室类型type中，*多媒体教室*或*多媒体教室+黑板*是可以当作上课教室的：

    def resData(jxl):
        # 可以当作教室的计数器
        ctr = 0
        # 座位总数
        zw = 0
        url = 'https://api.mayuko.cn/v2/ytunullroom/'
        param = {
            'sk': 'a4d456c728303e7a67f81f135af88cb2',
            'jxl': jxl,
            'js': 12
        }
        param = urllib.urlencode(param)
        resData = urllib2.urlopen(urllib2.Request(url='%s%s%s' % (url, '?', param)))
        resData = resData.read()
        text = demjson.decode(resData)  # 解析json数据
    
        for i in range(len(text)):
            # print text[i]['type']
            if "多媒体教室" in text[i]['type']:
                ctr += 1
                zw += int(text[i]['zw'])
    
        print "[" + str(jxl) + "]座位总数：" + str(zw)
        print "[" + str(jxl) + "]多媒体教室：" + str(ctr)
        print "[" + str(jxl) + "]平均座位数：" + str(zw / ctr)

之后，就可以粗略得到了每个教室的平均座位数，也可以等价于每个教室的上课人数3/2（忽略逃课的人数）：

    [1]座位总数：234
    [1]多媒体教室：3
    [1]平均座位数：78
    [2]座位总数：1040
    [2]多媒体教室：4
    [2]平均座位数：260
    ···
    [8]座位总数：10456
    [8]多媒体教室：64
    [8]平均座位数：163

**3.制作热力图数据**

有了每节课每个教学楼的人数，需要把这些数据整理出来，用Json数据格式进行展示：


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
            $heatmapData[$i]["count"] = (int)$arrayData[$classID][$i] * (int)$classNum[$i];
            $heatmapData[$i]["name"] = $classLocal[$i]["name"];
        }
        echo json_encode($heatmapData);
    }


使用GET方法得到了**星期几**和**当前的第几节**，需要将它们转换成Json数据中的ID值：

    ($id - 1) * 7 + ($week - 1)

**4.在地图中展示**

高德地图提供了热力图API，可以很方便的将教学楼的热力图数据展示在地图上。

进行地图初始化：

    function init(heatmapData) {
            var map = new AMap.Map("map", {
                resizeEnable: true,
                center: [121.457481, 37.474499],
                zoom: 17,
                zoomEnable: false,//禁止缩放
                doubleClickZoom: false,//禁止双击放大
    
            });
            if (!isSupportCanvas()) {
                alert('热力图仅对支持canvas的浏览器适用,您所使用的浏览器不能使用热力图功能,请换个浏览器试试~')
            }
            var heatmap;
            map.plugin(["AMap.Heatmap"], function () {
                //初始化heatmap对象
                heatmap = new AMap.Heatmap(map, {
                    radius: 90, //给定半径
                    opacity: [0, 0.8],
                    gradient: {
                        0.5: 'blue',
                        0.65: 'rgb(117,211,248)',
                        0.7: 'rgb(0, 255, 0)',
                        0.9: '#ffea00',
                        1.0: 'red'
                    }
                });
                heatmap.setDataSet({
                    data: heatmapData,
                    max: 6000
                });
            });
        }

加载热力图数据：

    $(document).ready(function () { 
        $(".btn").click(function () {
            $.ajax({
                url: "https://api.mayuko.cn/v2/heatmap",
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

这样就可以在地图上显示统计的热力图数据咯。


效果
--

左边可以选择星期几和节数，点击查询就可以显示当前星期下第几节的热力图了！默认打开时将显示当前时间的热力图。

![](https://github.com/mayuko2012/heatmap/screenshot/01.png)

从这个热力图我们可以看出来：开学周之后的周一的第二节，在综合楼、一二三教和四五六七教的人都挺多的，这个时候回宿舍叫外卖是一个很不错的选择。（逃

嗯？没找到链接？[传送门][5]


[1]: http://lab.mayuko.cn/heatmap/
[2]: https://api.mayuko.cn/!13
[3]: https://api.mayuko.cn/!12
[4]: https://cdn.mayuko.cn/usr/uploads/2017/08/2204083874.png?imageView2/2/w/3360/q/75
[5]: http://lab.mayuko.cn/heatmap/