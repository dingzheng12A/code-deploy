#!/bin/bash
if [ $# -ne 1 ];then
	echo -e "参数tag不能为空!"
	exit 1
fi 
if [ ! -d /data/vnnox-source/$1/CloudWEBAPP/dist ];then
	echo "目标路径:/data/vnnox-source/$1/CloudWEBAPP/dist 不存在"
	exit 2
fi
if [ -d /data/vnnox-source/$1/.git ];then
	rm -rf /data/vnnox-source/$1/.git
fi
[ ! -f /data/vnnox-source/$1.tar.bz2 ]|| rm -rf /data/vnnox-source/$1.tar.bz2
cd /data/vnnox-source/
tar -pcjvf $1.tar.bz2 $1 >.tarmsg 2>&1
if [ $? -ne 0 ];then
	echo "压缩代码失败,原因:$(cat .tarmsg)"
else
	md5sum $1.tar.bz2 > $1.tar.bz2_md5.txt
	echo "代码包:$1.tar.bz2 压缩成功"
	exit 0
fi
