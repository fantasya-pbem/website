#!/bin/bash

HOST=localhost
DATABASE=
USER=
PASSWORD=''
BACKUP=1
TRUNCATE_MESSAGES=0
LAST_TURN=`mysql -N -h $HOST -D $DATABASE -u $USER -p$PASSWORD -e "SELECT Value FROM settings WHERE Name='game.runde'"`
TURN=`expr $LAST_TURN + 1`
GAME=spiel
MONSTER_PARTIES=0,620480,1376883
BASE_DIR=/home/fantasya/games/$GAME
BACKUP_DIR=sqlbackup
REPORT_DIR=reporte
ZIP_DIR=zip
LOG_DIR=log
EMAIL_DIR=email
EMAIL_SUBJECT="Fantasya AW $TURN"
EMAIL_TEXT=$EMAIL_DIR/turn.email.txt
EMAIL_LOG=$EMAIL_DIR/log/$TURN
LOG=$LOG_DIR/run-$TURN.log
ZAT_LOG=$LOG_DIR/zat-$TURN.log

cd $BASE_DIR
touch $LOG

echo "Fantasya ZAT start: `date`" >> $LOG
echo "Running turn $TURN..." >> $LOG
if [ "$BACKUP" -gt 0 ]
then
	echo "Creating MySQL dump..." >> $LOG
	mysqldump -h $HOST -u $USER -p$PASSWORD $DATABASE | gzip -c --best > $BACKUP_DIR/before-$TURN.sql.gz 2>> $LOG
fi
if [ "$TRUNCATE_MESSAGES" -gt 0 ]
then
	echo "Delete previous battlefield messages..." >> $LOG
	mysql -h $HOST -D $DATABASE -u $USER -p$PASSWORD -e "DELETE FROM meldungen WHERE kategorie = 'Battle' AND partei NOT IN ($MONSTER_PARTIES)" 2>&1 >> $LOG
fi
echo >> $LOG

echo "Running the game..." >> $LOG
TIMER_START=`date +%s`
java -jar fantasya.jar -server $HOST:3306 -datenbank $DATABASE -benutzer $USER -passwort $PASSWORD -zat 2>&1 > $ZAT_LOG
ZAT_RESULT=$?
echo "Fantasya exit code: $ZAT_RESULT" >> $LOG
TIMER_END=`date +%s`
let DURATION=($TIMER_END-$TIMER_START+30)/60
echo "This AW took $DURATION minutes." >> $LOG
mv log-*.txt $LOG_DIR 2>&1 >> $LOG
if [ $ZAT_RESULT -gt 0 ]
then
	echo "Game aborted!" >> $LOG
	exit 1
fi
echo >> $LOG

echo "Moving reports..." >> $LOG
mkdir $REPORT_DIR/$TURN 2>&1 >> $LOG
mv $REPORT_DIR/$TURN-* $REPORT_DIR/$TURN/ 2>&1 >> $LOG
for zip in $ZIP_DIR/$TURN/*.zip
do
	mv $zip $ZIP_DIR/$TURN/$TURN-$(basename $zip) 2>&1 >> $LOG
done
echo >> $LOG

echo "Sending e-mails..." >> $LOG
mkdir -p $EMAIL_LOG
for ID in `mysql -N -s -h $HOST -u $USER -D $DATABASE -p$PASSWORD -e "SELECT id FROM partei"`
do
	EMAIL=`mysql -N -s -h $HOST -u $USER -D $DATABASE -p$PASSWORD -e "SELECT email FROM partei WHERE id = '$ID'"`
	ZIP=$ZIP_DIR/$TURN/$TURN-$ID.zip
	echo "$ZIP -> $EMAIL" >> $LOG
	mutt -F $EMAIL_DIR/muttrc -s "$EMAIL_SUBJECT" -a $ZIP -- $EMAIL < $EMAIL_TEXT 2>&1 >> $LOG
	echo $(cat $EMAIL_TEXT) > $EMAIL_LOG/$EMAIL.mail 2>> $LOG
done
echo >> $LOG

echo "Fantasya ZAT end: `date`" >> $LOG
echo "Finished." >> $LOG
