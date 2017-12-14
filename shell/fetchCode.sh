#!/bin/bash
msg=""
res=0
shellpath=$(cd $(dirname $0);pwd)
tag=$1
checkdir(){
	while read line
	do
		test -d /data/vnnox-source/${tag}/$line
		if [ $? -ne 0 ];then
			msg="${msg} ${line}"
		fi 
	done< ${shellpath}/dirlist.txt
	echo $msg
}
comm()
{
	echo -e "\033[31m $1 \033[0m"
}
comm_ok(){
	echo -e "\033[32m $1 \033[0m"
}
if [ $# -ne 1 ];then
	echo "Usage: $0 tag"
	exit 1
fi

${shellpath}/code_md5_check.sh
if [ $? -eq 0 ];then
	exit 0
fi

if [ ! -d /data/vnnox-git ];then
	git clone git@172.16.80.102:/vnnox/vnnox.git  /data/vnnox-git
fi
	cd /data/vnnox-git
	git reset --hard
	git pull
	git fetch --tags
	tags=$(git tag)
	result=$(echo $tags|grep $1)
	if [ -z "$result" ];then
		echo -e "tag $1 is not exits!\n"
		exit 2
	else
		#echo
		git checkout $1
		if [ $? -eq 0 ];then
			echo -e  "tag $1 checkout ok!\n"
		else
			
			echo -e "tag $1 checkout failure!\n"
			exit 3
		fi

	fi

if [ -d  /data/vnnox-source/$1 ];then
	rm -rf /data/vnnox-source/$1
fi
\cp -rf /data/vnnox-git /data/vnnox-source/$1
if [ $? -ne 0 ];then
	echo -e "代码复制失败:\cp -rf /data/vnnox-git /data/vnnox-source/$1\n"
	exit 4
fi
echo "$1@$(date +'%Y-%m-%d %H:%M:%S')" > /data/vnnox-source/$1/version.txt
cd /data/vnnox-source/$1
rm -rf .git
[ ! -d CloudAdmin/Admin/Runtime/ ] || rm -rf CloudAdmin/Admin/Runtime/
[ ! -d CloudAdmin/Admin/Runtime/ ] || rm -rf CloudLisence/Vnnox/Runtime/
[ ! -d CloudOrder/Vnnox/Runtime/ ] || rm -rf CloudOrder/Vnnox/Runtime/
[ ! -d CloudInit/Vnnox/Runtime/ ] || rm -rf CloudInit/Vnnox/Runtime/
[ ! -d CloudREST/NovaRest/Runtime/ ] || rm -rf CloudREST/NovaRest/Runtime/
[ ! -d CloudSERVICE/NovaService/Runtime/ ] || rm -rf CloudSERVICE/NovaService/Runtime/
[ ! -d CloudUpload/CloudUpload/Runtime/ ] || rm -rf CloudUpload/CloudUpload/Runtime/
msg=` checkdir `
if [ ! -z "$msg" ];then
	echo -e "directory ${msg} don't exits! "
	exit 5
fi
