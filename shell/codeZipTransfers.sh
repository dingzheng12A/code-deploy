#!/bin/bash
case $1 in 
'success')echo "执行命令成功";exit 0;;
'failure')echo "执行命令失败";exit 1;;
esac
