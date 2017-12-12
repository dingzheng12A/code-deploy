#!/bin/bash
currDay=`date +%Y-%m-%d`
logFile=/tmp/myf-swoole-${currDay}.log
nohup php swooleServer.php >> ${logFile}  2>&1 &
tail -f ${logFile}
