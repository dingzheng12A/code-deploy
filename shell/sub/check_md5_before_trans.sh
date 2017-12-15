#!/bin/bash
if [ $# -ne 2 ];then
        echo "参数不正确:Usage:$0 tag md5_string"
        exit 1
fi
if [ ! -s /data/release/vnnox/$1.tar.bz2 ] || [ ! -s /data/release/vnnox/$1.tar.bz2_md5.txt  ];then
        echo "代码包:/data/release/vnnox/$1.tar.bz2 不存在"
        exit 2
fi

old_md5=$(cat  /data/release/vnnox/$1.tar.bz2_md5.txt|awk '{print $1}')
if [ "$2" == "${old_md5}" ];then
        exit 0
else
        exit 4
fi
