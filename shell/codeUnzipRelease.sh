#!/bin/bash
shellpath=$(cd $(dirname $0);pwd)
if [ $# -ne 2 ];then
	echo "Usage:$0 tag [cn/us/jp/au/eu]"
	exit 1
elif [ $2 != "cn" -a $2 != "us" -a $2 != "jp" -a $2 != "au" -a  $2 != "eu" ];then
	echo "所选区域不正确!"
	exit 1
fi
#if [ $2 == "eu" ];then
#	jp_ip=$(${shellpath}/read.py jp master)
#	msg=$(ssh $jp_ip /etc/scripts/main_deploy.sh  $1 eu)
#	if [ ! -z "$msg" ];then
#		echo "发布eu失败,错误:${msg}"
#		exit 1
#	else
#		echo "发布eu成功,tag:$1"
#		exit 0
#	fi
#else
if [ "$2" == "eu" ];then
	master_ip=$(${shellpath}/read.py jp master)
else
	master_ip=$(${shellpath}/read.py $2 master)
fi
echo "master_ip:${master_ip}"
if [ "$2" == "eu" ];then
	res=$(ssh $master_ip /etc/scripts/jp_main_deploy.sh  $1 eu)
else
	sub_ip=$(${shellpath}/read.py $2 sub_host)
	res=$(ssh $master_ip "/etc/scripts/master_deploy.sh  $1 $sub_ip")
fi
if [ -z "$res" ];then
	if [ "$2" == "eu" ];then
		master_ip=$(${shellpath}/read.py eu master)
		sub_ip=$(${shellpath}/read.py eu sub_host)
	fi
	link_res=$(ssh $master_ip "/etc/scripts/master_link.sh  $1 $sub_ip")
	if [ ! -z "$link_res" ];then
		IFS='\v'
        	hostinfors=(${link_res[@]})
        	for infor in ${hostinfors[@]}
        	do
        	if [ $(echo $infor|wc -c) -gt 2 ];then
        	host=$(echo $infor|awk '{print $1}')
        	msg=$(echo $infor|awk '{$1="";sub(" ","");$NF="";print}')
        	exit_code=$(echo $infor|awk '{print $NF}')
        	echo "host:$host exit_code:$exit_code msg:$msg"
        	fi
        	done
		exit 1
	else
		echo "$2 节点 tag:$1 发布成功"
		exit 0
	fi
else
	IFS="\n"
	hostinfors=(${res[@]})
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
fi
#fi
