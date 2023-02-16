#!/bin/sh

PW_PATH=/PWServer/146

if [ ! -d $PW_PATH/logs ]; then
mkdir $PW_PATH/logs
fi
cat /dev/null > $PW_PATH/logs/syslog

echo "===============================================================" | tee -a /PWServer/146/logs/syslog
echo "=            Starting Perfect World v1.4.6 Daemons            =" | tee -a /PWServer/146/logs/syslog
echo "=                Starting All Instances / Maps                =" | tee -a /PWServer/146/logs/syslog
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
echo "=== OTHER WORLDS ===" | tee -a /PWServer/146/logs/syslog
./gs arena01 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 10
echo "=== ARENA1 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs arena02 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 10
echo "=== ARENA2 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs arena03 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 10
echo "=== ARENA3 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs arena04 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 10
echo "=== ARENA4 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is01 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== City of Abominations DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is02 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Secret Passage DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is05 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Firecrag Grotto DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is06 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Den of Rabid Wolves DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is07 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Cave of the Vicious DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is08 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Hall of Deception DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is09 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Gate of Delirium DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is10 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Secret Frostcover Grounds DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is11 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Valley of Disaster DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is12 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Forest Ruins DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is13 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Cave of Sadistic Glee DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is14 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Wraithgate DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is15 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Hallucinatory Trench DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is16 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Eden DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is17 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Brimstone Pit DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is18 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Temple of the Dragon DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is19 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Nightscream Island DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is20 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Snake Isle DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is21 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Lothranis DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is22 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Momaganon DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is23 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Seat of Torment DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is24 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Abaddon DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is25 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== City Of Naught DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is26 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Hall of Blasphemy DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is27 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Lunar Glade DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is28 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Valley of Reciprocity DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is29 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Frostcover City DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is31 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Twilight Temple DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is32 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Cube of Fate DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is33 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Chrono City DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is34 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Wedding Chapel DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is35 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Guild Base DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is37 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Morai DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is38 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Phoenix Valley DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is39 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Endless Universe DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is40 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Morai Instance DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is41 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== -Advanced- Endless Universe DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is42 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== N/A DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is43 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Nation Wars - Strategic Map DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is44 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Nation Wars - Battlefield DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is45 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== N/A DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is46 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== N/A DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs is50 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Realm of Reflection DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs bg01 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Battle Ground 1 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs bg02 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Battle Ground 2 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs bg03 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Battle Ground 3 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs bg04 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Battle Ground 4 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs bg05 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Battle Ground 5 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
./gs bg06 > $PW_PATH/logs/game_all.log 2>> $PW_PATH/logs/syslog &
sleep 3
echo "=== Battle Ground 6 DONE! ===" | tee -a /PWServer/146/logs/syslog

echo "" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
echo "=                     All Daemons Started                     =" | tee -a /PWServer/146/logs/syslog
echo "=                ~ All Instances / Maps Open ~                =" | tee -a /PWServer/146/logs/syslog
echo "===============================================================" | tee -a /PWServer/146/logs/syslog
