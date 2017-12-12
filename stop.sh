#!/bin/sh
filename="/tmp/myf-swoole-master.pid"
if [ -f  $filename ];then
	masterId=$(cat $filename )
	pid_num=$(ps -ef|grep $masterId|grep -v grep|wc -l)
	if [ $pid_num -gt 0 ];then
		kill -15  $masterId
		echo "服务已经停止"
	else
		echo  -e "服务未启动，无需停止"
		./start.sh
	fi
else
	echo -e "服务未启动，无需停止"
	./start.sh
fi