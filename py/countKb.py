#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Time    : 05/08/2017 22:07
# @Author  : hades
# @Software: PyCharm
# 统计某个班级的课表信息

import json
import sys
import urllib
import urllib2

reload(sys)
sys.setdefaultencoding('utf8')

# 创建一个10*42二维数组存储教学楼数据
# 综合楼：0
# 一教-七教：1-7
# 计算机中心：8
# 外院：9
roomCount = [[0 for i in range(10)] for i in range(42)]


# 获取课表信息
def resData(id):
    url = 'http://127.0.0.1/apicenter/v2/ytukb/list.php'
    param = {
        'id': id
    }
    param = urllib.urlencode(param)
    resData = urllib2.urlopen(urllib2.Request(url='%s%s%s' % (url, '?', param)))
    resData = resData.read()
    return resData


# 将json数据转换成字典
def convertData(data):
    text = json.loads(data)
    for i in range(41):
        judageRoom(text[str(i)]['0']['kbAddr'], i)
        # 忽略单双周课程不一致问题
        # if '1' in text[str(i)].keys():


# 判断每节课的教学楼
def judageRoom(s, i):
    if "综合楼" in s:
        roomCount[i][0] += 1
    elif "一教" in s:
        roomCount[i][1] += 1
    elif "二教" in s:
        roomCount[i][2] += 1
    elif "三教" in s:
        roomCount[i][3] += 1
    elif "四教" in s:
        roomCount[i][4] += 1
    elif "五教" in s:
        roomCount[i][5] += 1
    elif "六教" in s:
        roomCount[i][6] += 1
    elif "七教" in s:
        roomCount[i][7] += 1
    elif "计算中心" in s:
        roomCount[i][8] += 1
    elif "外院" in s:
        roomCount[i][9] += 1


# 循环遍历所有班级课表信息
# 共449个班级
for id in range(1, 450):
    convertData(resData(id))
json = json.dumps(roomCount)
print json
