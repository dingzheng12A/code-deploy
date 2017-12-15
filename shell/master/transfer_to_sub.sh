#!/bin/bash
shellpath=$(cd $(dirname $0);pwd)
transfer(){
	md5_string=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt|awk '{print $1}')
        ssh $2 "/etc/scripts/check_md5_before_trans.sh $1 ${md5_string}"
        if [ $? -ne 0 ];then
        echo " $(date +'%Y-%m-%d %H:%M:%S') transaction ...." >/etc/scripts/transfer.log
	ssh $2 "mkdir -p /data/release/vnnox"
	scp /data/release/vnnox/$1.tar.bz2 /data/release/vnnox/$1.tar.bz2_md5.txt $2:/data/release/vnnox/  >${shellpath}/.transfer
	if [ $? -ne 0 ];then
		echo -e "同步包/data/release/vnnox/$1.tar.bz2 到$2失败,原因:$(cat ${shellpath}/.transfer)"
		exit 3
	fi
	ssh $2 "/etc/scripts/client_md5_check.sh $1"
	if [ $? -ne 0 ];then
		echo "主机:$2 MD5校验代码包/data/release/vnnox/$1.tar.bz2 失败"
		exit 4
	else
		echo "代码发布成功"
		exit 0
	fi
	fi
}
if [ $# -ne 2 ];then
        echo "Usage:$0 tag sub_host"
        exit 1
fi
hostlist=($2)
error=""
if [ ! -e /data/release/vnnox/$1.tar.bz2 ] || [ ! -e /data/release/vnnox/$1.tar.bz2_md5.txt ];then
        echo "/data/release/vnnox/$1.tar.bz2 不存在"
        exit 2
fi
res=$(${shellpath}/md5_check.sh $1;echo $?)
#echo "res:$res\n"
if [ $res -ne 0 ];then
        echo "/data/release/vnnox/$1.tar.bz2 MD5校验失败"
        exit 4
fi
trans_res=0
IFS=","
declare -i num=0
for host in ${hostlist[@]}
do
	declare -A status
	res=`transfer $1 $host` 
	#echo $res
	exit_code=$?
	status=(['host']=$host ['exit_code']=$exit_code [msg]=${res} ['term']='\n')
	hosts_status[$num]=${status[*]}
	num=`expr $num + 1 `
done
echo "${hosts_status[*]}"

