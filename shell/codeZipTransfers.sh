#!/bin/bash
shellpath=$(cd $(dirname $0);pwd)
mkdir -p /data/vnnox-source
if [ $# -ne 2 ];then
	echo "Usage:$0 tag [cn/us/jp/eu/au]"
	exit 1
elif [ $2 != "cn" -a $2 != "us" -a $2 != "jp" -a $2 != "au" -a  $2 != "eu" ];then
        echo "所选区域不正确!"
        exit 1
fi
#if [ $2 == "eu" ];then
#        jp_ip=$(${shellpath}/read.py jp master)
#	echo "jp_ip:$jp_ip"
#		#日本传输校验逻辑
#        msg=$(ssh $jp_ip /etc/scripts/codeZipTransfers.sh  $1 eu)
#        if [ ! -z "$msg" ];then
#                echo "发布eu:${msg}"
#        fi
#else
tag=$1
if [ "$2" == "eu" ];then
	master_ip=$(${shellpath}/read.py jp master)
else
	master_ip=$(${shellpath}/read.py $2 master)
fi
sub_ip=$(${shellpath}/read.py $2 sub_host)
if [ ! -s /data/vnnox-source/$1.tar.bz2  ];then
	echo "源文件:/data/vnnox-source/$1.tar.bz2 不存在 "
	exit 2	
fi 
 
if [ ! -s "/data/vnnox-source/$1.tar.bz2_md5.txt" ];then 
        echo "MD5文件:/data/vnnox-source/$1.tar.bz2_md5.txt 不存在 "
        exit 2
fi  
ssh -v www@$master_ip "mkdir -p /data/release/vnnox/"
scp  -v /data/vnnox-source/$1.tar.bz2* www@$master_ip:/data/release/vnnox/   >.tansfer 2>&1
if [ $? -ne 0 ];then
	echo "代码包同步失败"
	exit 3
fi
echo "tag is:$tag"
ssh $master_ip  "/etc/scripts/md5_check.sh $tag"
if [ $? -ne 0 ];then
	echo "主机:${master_ip} MD5检查失败"
	exit 4
else
	if [ "$2" == "eu" ];then
		echo "aaaaaa"
		msg=$(ssh $master_ip "/etc/scripts/master_codeZipTransfers.sh  $1 eu")
	else
		msg=$(ssh $master_ip "/etc/scripts/transfer_to_sub.sh $tag ${sub_ip}")
	fi
fi

if [ ! -z "$msg" ];then
	IFS="\n"
        hostinfors=(${msg[@]})
        for infor in ${hostinfors[@]}
        do
                if [ $(echo $infor|wc -c) -gt 2 ];then
                host=$(echo $infor|awk '{print $1}')
                exit_code=$(echo $infor|awk '{print $3}')
                msg=$(echo $infor|awk '{print $2}')
                echo "area:$2 host:$host exit_code:$exit_code msg:$msg"
                fi
        done
	exit 1
else
	exit 0
fi
#fi
