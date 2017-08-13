#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Time    : 06/08/2017 21:53
# @Author  : hades
# @Software: PyCharm

import sys
import urllib
import urllib2

import demjson

reload(sys)
sys.setdefaultencoding('utf8')


def resData(jxl):
    # 可以当作教室的计数器
    ctr = 0
    # 座位总数
    zw = 0
    url = 'http://127.0.0.1/apicenter/v2/ytunullroom/'
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

for i in range(1,9):
    resData(i)
