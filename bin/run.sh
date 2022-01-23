#!/bin/bash

HOST=localhost
DATABASE_GAME=
USER_GAME=
PASSWORD_GAME=''
BACKUP=1
TRUNCATE_MESSAGES=0
LAST_TURN=`mysql -N -h $HOST -D $DATABASE_GAME -u $USER_GAME -p$PASSWORD_GAME -e "SELECT Value FROM settings WHERE Name='game.runde'"`
TURN=`expr $LAST_TURN + 1`
GAME=spiel
MONSTER_PARTIES=(0 dark tier)
BASE_DIR=/home/fantasya/games/$GAME
BACKUP_DIR=sqlbackup
REPORT_DIR=reporte
ZIP_DIR=zip
LOG_DIR=log
EMAIL_COMMAND="php /var/customers/webs/fantasya/website/bin/console send:fantasya $GAME -vvv"
LOG=$LOG_DIR/run-$TURN.log
ZAT_LOG=$LOG_DIR/zat-$TURN.log

which b36 > /dev/null
if [ "$?" -gt 0 ]
then
	echo "b36 tool not found."
	exit 1
fi

cd $BASE_DIR
touch $LOG

echo "Fantasya ZAT start: `date`" >> $LOG
echo "Running turn $TURN..." >> $LOG
if [ "$BACKUP" -gt 0 ]
then
	echo "Creating MySQL dump..." >> $LOG
	mysqldump -h $HOST -u $USER_GAME -p$PASSWORD_GAME $DATABASE_GAME | gzip -c --best > $BACKUP_DIR/before-$TURN.sql.gz 2>> $LOG
fi
if [ "$TRUNCATE_MESSAGES" -gt 0 ]
then
	echo "Delete previous battlefield messages..." >> $LOG
	for id in ${MONSTER_PARTIES[*]}
	do
		number=`b36 -d $id`
		if [ -z "$monsterParties" ]
		then
			monsterParties=$number
		else
			monsterParties=$monsterParties,$number
		fi
	done
	mysql -h $HOST -D $DATABASE_GAME -u $USER_GAME -p$PASSWORD_GAME -e "DELETE FROM meldungen WHERE kategorie = 'Battle' AND partei NOT IN ($monsterParties)" 2>&1 >> $LOG
fi
echo >> $LOG

echo "Running the game..." >> $LOG
TIMER_START=`date +%s`
java -jar fantasya.jar -server $HOST:3306 -datenbank $DATABASE_GAME -benutzer $USER_GAME -passwort $PASSWORD_GAME -zat -ohnemonster 2>&1 > $ZAT_LOG
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

# Send emails via website command.
echo "Sending e-mails..." >> $LOG
EMAIL_RESULT=`EMAIL_COMMAND 2>&! >> $LOG`
if [ $EMAIL_RESULT -ne 0 ]
then
	echo "Sending e-mails failed (code $EMAIL_RESULT)!" >> $LOG
fi
echo >> $LOG

echo "Fantasya ZAT end: `date`" >> $LOG
echo "Finished." >> $LOG
