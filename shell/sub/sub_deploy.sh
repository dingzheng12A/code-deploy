#!/bin/bash
shellpath=$(cd $(dirname $0);pwd)
tag=$1

checkdir(){
        while read line
        do
                test -d /data/release/vnnox/${tag}/$line
                if [ $? -ne 0 ];then
                        msg="${msg} ${line}"
                fi
        done< ${shellpath}/dirlist.txt
        echo $msg
}

islink(){
	linkpath=$(ls -ld /data/vnnox|awk '{print $NF}' 2>/dev/null)
	if [ "$linkpath" == "/data/release/vnnox/$1" ];then
		echo 0
	else
		echo 1
	fi
}

md5check(){
	result=0
	md5sum /data/release/vnnox/$1.tar.bz2 > /data/release/vnnox/$1.tar.bz2_md5.txt_myself
	if [ $? -eq 0 ];then
		old_md5=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt|awk '{print $1}')
        	new_md5=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt_myself|awk '{print $1}')
        	if [ "${old_md5}" == "${new_md5}" ];then
                	result=0
        	else
                	result=4
        	fi
	else
        	result=$?
	fi
	echo $result
}
untar(){
	tar -xjvf /data/release/vnnox/$1.tar.bz2 -C /data/release/vnnox/ >${shellpath}/.tarmsg 2>&1
        if [ $? -ne 0 ];then
                echo "解压:/data/release/vnnox/$1.tar.bz2 失败,原因:$(cat ${shellpath}/.tarmsg)"
                exit 3
        else
                version=$(cat /data/release/vnnox/$1/version.txt |cut -d '@' -f 1)
                if [ "${version}" == "$1" ];then
                        checkres=`checkdir`
                        if [ ! -z "$checkres" ];then
                                echo -e "$msg 目录不存在"
                                exit 1
                        fi
			if [ $(id www >/dev/null 2>&1;echo $?) -ne 0 ];then
        			useradd www -s /sbin/nologin
			fi
			chown -R www.www /data/release/vnnox/$1
                else
                        echo -e "版本不匹配"
                        exit 1
                fi
		cp /data/release/vnnox/$1.tar.bz2_md5.txt /data/release/vnnox/$1/
        fi
	
}
if [ ! -s /data/release/vnnox/$1.tar.bz2 ];then
	echo "目标压缩包:/data/release/vnnox/$1.tar.bz2 不存在"
	exit 2
fi
res=`md5check $1`
if [ $res -ne 0 ];then
	echo "校验代码包/data/release/vnnox/$1.tar.bz2 md5 失败"
	exit 4
#else
#	echo "验代码包/data/release/vnnox/$1.tar.bz2 md5 成功"
fi

if [ ! -d /data/release/vnnox/$1 ];then
	tar -xjvf /data/release/vnnox/$1.tar.bz2 -C /data/release/vnnox/ >${shellpath}/.tarmsg 2>&1
	if [ $? -ne 0 ];then
		echo "解压:/data/release/vnnox/$1.tar.bz2 失败,原因:$(cat ${shellpath}/.tarmsg)"
		exit 3
	else
		version=$(cat /data/release/vnnox/$1/version.txt |cut -d '@' -f 1)
		if [ "${version}" == "$1" ];then
			checkres=`checkdir`
			if [ ! -z "$checkres" ];then
				echo -e "$msg 目录不存在"
				exit 1		
			fi	
			if [ $(id www >/dev/null 2>&1;echo $?) -ne 0 ];then
        			useradd www -s /sbin/nologin
			fi
			chown -R www.www /data/release/vnnox/$1
		else
			echo -e "版本不匹配"
			exit 1
		fi
	fi
	cp /data/release/vnnox/$1.tar.bz2_md5.txt /data/release/vnnox/$1/
else
	test -s /data/release/vnnox/$1/$1.tar.bz2_md5.txt
	if [ $? -ne 0 ];then
		#rm -rf /data/release/vnnox/$1.tar.bz2 /data/release/vnnox/$1/
		rm -rf  /data/release/vnnox/$1/
		untar $1
	else
		inside_md5=$(cat /data/release/vnnox/$1/$1.tar.bz2_md5.txt|awk '{print $1}')
		curr_md5=$(cat /data/release/vnnox/$1.tar.bz2_md5.txt|awk '{print $1}')
		if [ "${inside_md5}" == "${curr_md5}" ];then
			echo "解压代码包/data/release/vnnox/$1/$1.tar.bz2 成功"
			exit 0
		else
			linkok=`islink $1`
			if [ $linkok -eq 0 ];then
				echo "有错误，请联系相关人员解决"
				exit 6
			else
				echo "软链接路径不存在，删除目录"
				rm -rf /data/release/vnnox/$1
				untar $1
				
			fi
			
			
		fi
	fi
fi

