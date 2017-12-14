#!/bin/bash
if [ $# -ne 1 ];then
	echo "Usage:$0 tag"
	exit 1
fi
dirlist=("assets" "config" "controllers" "images" "lang" "main" "model" "views")
dirlen=${#dirlist[@]}
msg=""
res=0
checkdir(){
	for((i=0;i<dirlen;i++))
	do
		test -d /data/vnnox-source/$1/CloudWEBAPP/dist/${dirlist[$i]}
		if [ $? -ne 0 ];then
			msg="$msg ${dirlist[$i]}"
		fi
	done

}
if [ ! -d /data/vnnox-source/$1 ];then
	echo "/data/vnnox-source/$1 don't exits"
	exit 2
fi

inside_version=$(cat /data/vnnox-source/$1/version.txt|cut -d @ -f 1)
if [ "${inside_version}" != "$1" ];then
	echo "版本错误,/data/vnnox-source/$1/version.txt 中包含的tag版本不匹配"
	exit 4
fi

cd /data/vnnox-source/$1
if [ -d /data/npm/node_modules ];then
	\cp -rf /data/npm/node_modules /data/vnnox-source/$1/CloudWEBAPP/
fi
if [ -e /data/npm/package-lock.json ];then
	 \cp -rf /data/npm/package-lock.json /data/vnnox-source/$1/CloudWEBAPP/
fi
cd /data/vnnox-source/$1/CloudWEBAPP/
[ ! -d /data/vnnox-source/$1/CloudWEBAPP/dist ] || rm -rf  /data/vnnox-source/$1/CloudWEBAPP/dist
/usr/local/node/bin/npm install >.npmerr 2>&1
if [ $? -ne 0 ];then
	echo "未知错误,$(cat .npmerr)";
	exit 1
fi
gulp build  2>.err
if [ $? -ne 0 ];then
	echo  "前端代码编译出错:原因:$(cat .err)"
	exit 3
fi
`checkdir`
if [ -z "$msg" ];then 
	rm -rf /data/npm/{node_modules,package-lock.json}
	mv /data/vnnox-source/$1/CloudWEBAPP/{node_modules,package-lock.json} /data/npm/
	echo -e "前端代码编译成功!"
	exit 0
else
	echo "前端编译失败，这些目录不存在:${msg}"
	exit 3
fi

  
