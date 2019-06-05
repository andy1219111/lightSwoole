#!/bin/sh

now=$(date "+%Y-%m-%d %k:%M:%S")
echo ${now}":stop the Light_swoole_server..."
pids=$(ps -ef|grep Light_swoole_server|grep -v grep|cut -c 9-15|xargs)
echo "Light_swoole_server pids:$pids"

for pid in $pids;
do
    kill -9 $pid
done
now=$(date "+%Y-%m-%d %k:%M:%S")
today=$(date "+%Y-%m-%d")
log_file="./logs/log_${today}.log"
if [ ! -f $log_file ];then
    mv ./logs/last.log $log_file
fi

echo ${now}":start the Light_swoole_server..."
php ./Light_swoole_server.php 9523 6 4

now=$(date "+%Y-%m-%d %k:%M:%S")
echo ${now}":the Light_swoole_server has started."
