#!/bin/sh
#kll all gameservers by bp0 <F4> upated



test -z "$(pidof -x start.sh)" && kill $(pidof -x start.sh)

 /usr/bin/killall glinkd
echo "Sleeping 60 seconds..." | tee -a /PWServer/146/logs/syslog
sleep 10
 /usr/bin/killall loader
 /usr/bin/killall gs
 /usr/bin/killall gacd
 /usr/bin/killall gamedbd
 /usr/bin/killall gdeliveryd
 /usr/bin/killall gfactiond
 /usr/bin/killall authd
 /usr/bin/killall logservice
 /usr/bin/killall uniquenamed
# /bin/kill `ps -ef | grep commons-collections-3.1.jar | grep -v grep | awk '{print $2}'`
#test -z "`ps -ef | grep authd | grep -v grep | awk '{print $2}'`" &&  /bin/kill `ps -ef | grep authd | grep -v grep | awk '{print $2}'`
 /bin/kill `ps -ef | grep authd | grep -v grep | awk '{print $2}'`
echo "Killing the last UniqueName daemon..." | tee -a /PWServer/146/logs/syslog
sleep 10
test -z "$(pidof uniquenamed)" && kill -9 $(pidof uniquenamed)
echo "Kill server complete" | tee -a /PWServer/146/logs/syslog
