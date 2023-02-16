#!/bin/sh

PW_PATH=/PWServer/146

if [ ! -d $PW_PATH/logs ]; then
mkdir $PW_PATH/logs
fi
cat /dev/null > $PW_PATH/logs/syslog

echo "===============================================================" | tee -a /PWServer/146/logs/syslog
echo "=            Starting Perfect World v1.4.6 Daemons            =" | tee -a /PWServer/146/logs/syslog
echo "=              Starting Minimum Instances / Maps              =" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
date | tee -a /PWServer/146/logs/syslog

echo "=== LOGSERVICE ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/logservice; ./logservice logservice.conf > $PW_PATH/logs/logservice.log 2>> $PW_PATH/logs/syslog &
sleep 2
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== UNIQUENAMED ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/uniquenamed; ./uniquenamed gamesys.conf > $PW_PATH/logs/uniquenamed.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== AUTH ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/authd; ./authd >> $PW_PATH/logs/syslog 2>&1 &
sleep 10
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== GAMEDBD ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/gamedbd; ./gamedbd gamesys.conf > $PW_PATH/logs/gamedbd.log 2>> $PW_PATH/logs/syslog &
sleep 5
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== GACD ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/gacd; ./gacd gamesys.conf > $PW_PATH/logs/gacd.log 2>> $PW_PATH/logs/syslog &
sleep 5
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== GFACTIOND ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/gfactiond; ./gfactiond gamesys.conf > $PW_PATH/logs/gfactiond.log 2>> $PW_PATH/logs/syslog &
sleep 5
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== GDELIVERYD ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/gdeliveryd; ./gdeliveryd gamesys.conf > $PW_PATH/logs/gdeliveryd.log 2>> $PW_PATH/logs/syslog &
sleep 5
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== GLINKD ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/glinkd; ./glinkd gamesys.conf 1 > $PW_PATH/logs/glink.log 2>> $PW_PATH/logs/syslog &
cd $PW_PATH/glinkd; ./glinkd gamesys.conf 2 > $PW_PATH/logs/glink2.log 2>> $PW_PATH/logs/syslog &
cd $PW_PATH/glinkd; ./glinkd gamesys.conf 3 > $PW_PATH/logs/glink3.log 2>> $PW_PATH/logs/syslog &
cd $PW_PATH/glinkd; ./glinkd gamesys.conf 4 > $PW_PATH/logs/glink4.log 2>> $PW_PATH/logs/syslog &
sleep 10
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "=== MAIN WORLD ===" | tee -a /PWServer/146/logs/syslog
cd $PW_PATH/gamed; ./gs gs01 > $PW_PATH/logs/game1.log 2>> $PW_PATH/logs/syslog &
sleep 30
echo "=== DONE! ===" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
echo "=                     All Daemons Started                     =" | tee -a /PWServer/146/logs/syslog
echo "=              ~ Minimum Instances / Maps Open ~              =" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
