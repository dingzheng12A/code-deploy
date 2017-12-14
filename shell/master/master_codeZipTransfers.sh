#!/bin/bash
if [ $# -ne 2 ];then
	echo "Usage:$0 tag [cn/us/jp/eu/au]"
	exit 1
elif [ $2 != "cn" -a $2 != "us" -a $2 != "jp" -a $2 != "au" -a  $2 != "eu" ];then
        echo "所选区域不正确!"
        exit 1
fi
tag=$1
shellpath=$(cd $(dirname $0);pwd)
master_ip=$(${shellpath}/read.py $2 master)
sub_ip=$(${shellpath}/read.py $2 sub_host)
if [ ! -s /data/release/vnnox/$1.tar.bz2  ];then
	echo "源文件:/data/vnnox-source/$1.tar.bz2 不存在 "
	exit 2	
fi 
 
if [ ! -s "/data/release/vnnox/$1.tar.bz2_md5.txt" ];then 
        echo "MD5文件:/data/vnnox-source/$1.tar.bz2_md5.txt 不存在 "
        exit 2
fi  
ssh -v $master_ip "mkdir -p /data/release/vnnox/"
scp  -v /data/release/vnnox/$1.tar.bz2* $master_ip:/data/release/vnnox/   >.tansfer 2>&1
if [ $? -ne 0 ];then
	echo "代码包同步失败"
	exit 3
fi
#echo "tag is:$tag"
ssh $master_ip  "/etc/scripts/md5_check.sh $tag"
if [ $? -ne 0 ];then
	echo "主机:${master_ip} MD5检查失败"
	exit 4
else
	ssh $master_ip "/etc/scripts/transfer_to_sub.sh $tag ${sub_ip}"
fi


