#!/bin/bash
result=0
md5sum /data/vnnox-source/$1.tar.bz2 > /data/vnnox-source/$1.tar.bz2_md5.txt_myself
if [ $? -eq 0 ];then
	old_md5=$(cat /data/vnnox-source/$1.tar.bz2_md5.txt|awk '{print $1}') 
	new_md5=$(cat /data/vnnox-source/$1.tar.bz2_md5.txt_myself|awk '{print $1}')
	if [ "${old_md5}" == "${new_md5}" ];then
		result=0
	else
		result=4
	fi
else
	result=$?
fi
exit $result
