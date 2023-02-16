#!/bin/sh

PW_PATH=/PWServer/146

if [ ! -d $PW_PATH/logs ]; then
mkdir $PW_PATH/logs
fi
cat /dev/null > $PW_PATH/logs/syslog

echo "=== LOADING MAP ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/gamed; ./gs gs01 > $PW_PATH/logs/game1.log 2>> $PW_PATH/logs/syslog &
sleep 10
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
