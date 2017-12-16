#!/bin/bash
set -x
shellpath=$(cd $(dirname $0);pwd)
my_ip="172.16.80.135"
transfer(){
<<<<<<< HEAD
	#md5_string=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt|awk '{print $1}')
	#ssh $2 "/etc/scripts/check_md5_before_trans.sh $1 ${md5_string}"
	ssh $2 "test -d /data/release/vnnox/$1"
	if [ $? -eq 0 ];then
		echo " $(date +'%Y-%m-%d %H:%M:%S') $2 transaction tag:$1 ...." >>/etc/scripts/transfer.log
		ssh  -o StrictHostKeychecking=no $2 "mkdir -p /data/release/vnnox"
		echo "$(date +'%Y-%m-%d %H:%M:%S') $2" >> /etc/scripts/.transfer
		rsync --exclude ".git*" -avzc  /data/release/vnnox/$1/* rsync@$2::vnnox/$1/ --password-file=/etc/rsync.pass >> ${shellpath}/.transfer 2>&1
		if [ $? -ne 0 ];then
			echo -e "同步代码/data/release/vnnox/$1 到$2失败,原因:$(cat ${shellpath}/.transfer)"
			exit 3
		fi
		ssh $2 "rm -rf /data/release/vnnox/vnnox-source && \cp -rf /data/release/vnnox/$1 /data/release/vnnox/vnnox-source"
		exit $?
=======
	md5_string=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt|awk '{print $1}')
        ssh $2 "/etc/scripts/check_md5_before_trans.sh $1 ${md5_string}"
        if [ $? -ne 0 ];then
	echo " $(date +'%Y-%m-%d %H:%M:%S') $2 transaction tag:$1 ...." >>/etc/scripts/transfer.log
	ssh  -o StrictHostKeychecking=no $2 "mkdir -p /data/release/vnnox"
	scp  -o StrictHostKeychecking=no /data/release/vnnox/$1.tar.bz2 /data/release/vnnox/$1.tar.bz2_md5.txt $2:/data/release/vnnox/  >${shellpath}/.transfer
	if [ $? -ne 0 ];then
		echo -e "同步包/data/release/vnnox/$1.tar.bz2 到$2失败,原因:$(cat ${shellpath}/.transfer)"
		exit 3
	fi
	ssh $2 "/etc/scripts/client_md5_check.sh $1"
	if [ $? -ne 0 ];then
		echo "主机:$2 MD5校验代码包/data/release/vnnox/$1.tar.bz2 失败"
		exit 4
>>>>>>> 3b5e47b7725f35f75eb63ae40ba753dba35a1b23
	else
		echo "bu cunzai"
		echo " $(date +'%Y-%m-%d %H:%M:%S') $2 transaction tag:$1 ...." >>/etc/scripts/transfer.log
                ssh  -o StrictHostKeychecking=no $2 "mkdir -p /data/release/vnnox/vnnox-source"
                echo "$(date +'%Y-%m-%d %H:%M:%S') $2" >> /etc/scripts/.transfer
                rsync --exclude ".git*" -avzc --progress /data/release/vnnox/$1/* rsync@$2::vnnox/vnnox-source/ --password-file=/etc/rsync.pass >> ${shellpath}/.transfer 2>&1
		if [ $? -ne 0 ];then
                        echo -e "同步代码/data/release/vnnox/$1 到$2失败,原因:$(cat ${shellpath}/.transfer)"
                        exit 3
                fi
		 ssh $2 " \cp -rf /data/release/vnnox/vnnox-source /data/release/vnnox/$1"
		 exit $?
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
echo "res:$res\n"
if [ $res -ne 0 ];then
        echo "/data/release/vnnox/$1.tar.bz2 MD5校验失败"
        exit 4
else
	tar -xjvf /data/release/vnnox/$1.tar.bz2 -C /data/release/vnnox/ >${shellpath}/.untar 2>&1
	if [ $? -ne 0 ];then
		echo "解压/data/release/vnnox/$1.tar.bz2 失败，原因:$(cat ${shellpath}/.untar)"
		exit 3
	else
		rm -f /data/release/vnnox/$1/*_md5.txt
		\cp -rf /data/release/vnnox/$1.tar.bz2_md5.txt /data/release/vnnox/$1/
	fi
fi
trans_res=0
IFS=","
declare -i num=0
for host in ${hostlist[@]}
do
	if [ "$host" != "$my_ip" ];then
	declare -A status
	res=`transfer $1 $host` 
	#echo $res
	exit_code=$?
	if [ $exit_code -ne 0 ];then
		status=(['host']=$host ['exit_code']=$exit_code [msg]=$(cat ${shellpath}/.transfer) ['term']='\n')
		hosts_status[$num]=${status[*]}
		num=`expr $num + 1 `
	fi
	fi
done
echo "${hosts_status[*]}"

