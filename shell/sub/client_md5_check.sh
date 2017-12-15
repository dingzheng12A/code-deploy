#!/bin/bash
result=0
md5sum /data/release/vnnox/$1.tar.bz2 > /data/release/vnnox/$1.tar.bz2_md5.txt_myself
if [ $? -eq 0 ];then
	old_md5=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt|awk '{print $1}') 
	inside_md5=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt_myself|awk '{print $1}')
	if [ "${old_md5}" == "${inside_md5}" ];then
		result=0
	else
		echo -e "代码包:/data/release/vnnox/$1,校验失败"
		result=4
	fi
else
	result=$?
fi
exit $result
