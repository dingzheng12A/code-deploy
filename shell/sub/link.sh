#!/bin/bash
if [ -d /data/release/vnnox/$1 ];then
        chown -R www.www /data/release/vnnox/$1
        ln -snf /data/release/vnnox/$1 /data/vnnox
	if [ $? -eq 0 ];then
		/etc/init.d/php-fpm reload 2> /etc/scripts/.php-fpm 
		if [ $? -ne 0 ];then
			echo -e "启动php-fpm 失败,原因:$(cat /etc/scripts/.php-fpm)\n"
			exit 2
		fi
	else
		echo "连接 ln -snf /data/release/vnnox/$1 /data/vnnox 失败"
		exit 1
	fi

fi
~     
