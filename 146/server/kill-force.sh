#!/bin/sh
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
echo "=                    Shutting Down Daemons                    =" | tee -a /PWServer/146/logs/syslog
echo "=                       Please Wait....                       =" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
sleep 5
pkill -9 gs
echo "STOPPING GAMESERVER..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 gamedbd
echo "STOPPING GAMEDB DAEMON..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 gdeliveryd
echo "STOPPING GAME DELIVERY DAEMON..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 gfactiond
echo "STOPPING GAME FACTION DAEMON..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 authd
echo "STOPPING AUTH DAEMON..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 uniquenamed
echo "STOPPING UNIQUENAME DAEMON..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 glinkd
echo "STOPPING GAME LINK DAEMON..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 gacd
echo "STOPPING GAME ANTICHEAT DAEMON..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 logservices
echo "STOPPING LOG SERVICES..!" | tee -a /PWServer/146/logs/syslog
sleep 3
pkill -9 startup.sh
echo "STOPPING BASH SCRIPT..!" | tee -a /PWServer/146/logs/syslog
sleep 5
pkill -9 start_servers.s
pkill -9 start.sh
pkill -9 start2.sh
echo "STOPPING SERVER STARTERS..!" | tee -a /PWServer/146/logs/syslog
sleep 5 
#pkill -9 java
pkill -9 -f table.xml
echo "STOPPING JAVA..!" | tee -a /PWServer/146/logs/syslog
sleep 5
echo "" | tee -a /PWServer/146/logs/syslog
echo "" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
echo "=                     All Daemons Stopped                     =" | tee -a /PWServer/146/logs/syslog
echo "=             Perfect World v1.4.6 is now offline             =" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
