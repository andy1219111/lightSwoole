#!/bin/sh

pids=$(ps -ef|grep Light_swoole_server|grep -v grep|cut -c 9-15|xargs)
echo $pids

for pid in $pids;
do
    echo $pid
    kill -9 $pid
done
