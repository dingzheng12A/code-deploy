#!/bin/bash
if [ $# -ne 2 ];then
	echo "Usage:$0 tag [cn/us/jp/au/eu/beta]"
	exit 1
elif [ $2 != "cn" -a $2 != "us" -a $2 != "jp" -a $2 != "au" -a  $2 != "eu" -a $2 != "beta" ];then
	echo "所选区域不正确!"
	exit 1
fi
shellpath=$(cd $(dirname $0);pwd)
master_ip=$(/etc/scripts/read.py $2 master)
#echo "master_ip:${master_ip}"
sub_ip=$(/etc/scripts/read.py $2 sub_host)
res=$(ssh $master_ip "/etc/scripts/master_deploy.sh  $1 $sub_ip")
if [  -z "$res" ];then
	link_res=$(ssh $master_ip "/etc/scripts/master_link.sh  $1 $sub_ip")
	if [ ! -z "$link_res" ];then
		IFS='\v'
        	hostinfors=(${link_res[@]})
        	for infor in ${hostinfors[@]}
        	do
		if [ ! -z "$infor" ];then
        	host=$(echo $infor|awk '{print $1}')
        	exit_code=$(echo $infor|awk '{print $NF}')
        	msg=$(echo $infor|awk '{$1="";sub(" ","");$NF="";print}')
        	echo "host:$host exit_code:$exit_code msg:$msg"
        	fi
        	done
		exit 1
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
		echo "host:$host exit_code:$exit_code msg:$msg"
		fi
	done
	exit 1
fi
