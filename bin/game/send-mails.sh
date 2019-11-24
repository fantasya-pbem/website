#!/bin/bash

HOST=localhost
DATABASE_GAME=
USER_GAME=
PASSWORD_GAME=''
DATABASE_USER=
USER_USER=
PASSWORD_USER=''
TURN=`mysql -N -h $HOST -D $DATABASE_GAME -u $USER_GAME -p$PASSWORD_GAME -e "SELECT Value FROM settings WHERE Name='game.runde'"`
GAME=spiel
GAME_ID=1
MONSTER_PARTIES=(0 dark tier)
BASE_DIR=/home/fantasya/games/$GAME
ZIP_DIR=zip
EMAIL_DIR=email
EMAIL_SUBJECT="Fantasya AW $TURN"
EMAIL_TEMPLATE=$EMAIL_DIR/turn.email.template
EMAIL_TEXT=$EMAIL_DIR/turn.email.txt
EMAIL_LINK='https://www.fantasya-pbem.de/report/t'
FANTASYACOMMAND=/var/customers/webs/fantasya/website/bin/console
EMAIL_LOG=$EMAIL_DIR/log/$TURN

cd $BASE_DIR

echo "Sending e-mails..."
mkdir -p $EMAIL_LOG
for id in ${MONSTER_PARTIES[*]}
do
	if [ -z "$monsterParties" ]
	then
		monsterParties="'$id'"
	else
		monsterParties="$monsterParties,'$id'"
	fi
done
for ID in `mysql -N -s -h $HOST -u $USER_GAME -D $DATABASE_GAME -p$PASSWORD_GAME -e "SELECT id FROM partei WHERE id NOT IN ($monsterParties)"`
do
	EMAIL=`mysql -N -s -h $HOST -u $USER_GAME -D $DATABASE_GAME -p$PASSWORD_GAME -e "SELECT email FROM partei WHERE id = '$ID'"`
	USER_ID=`mysql -N -s -h $HOST -u $USER_GAME -D $DATABASE_GAME -p$PASSWORD_GAME -e "SELECT user_id FROM partei WHERE id = '$ID'"`
	WITH_ATTACHMENT=`mysql -N -s -h $HOST -u $USER_USER -D $DATABASE_USER -p$PASSWORD_USER -e "SELECT flags & 1 FROM user WHERE id = $USER_ID"`
	EMAIL_TOKEN=`$FANTASYACOMMAND download:token $GAME_ID $ID $EMAIL $TURN`
	if [ $? -eq 0 ]
	then
		cat $EMAIL_TEMPLATE > $EMAIL_TEXT
		echo "$EMAIL_LINK/$EMAIL_TOKEN" >> $EMAIL_TEXT
		echo "$ID -> $EMAIL"
		if [ $WITH_ATTACHMENT -eq 1 ]
		then
			ZIP=$ZIP_DIR/$TURN/$TURN-$ID.zip
			echo "$ZIP -> $EMAIL"
			mutt -F $EMAIL_DIR/muttrc -s "$EMAIL_SUBJECT" -a $ZIP -- $EMAIL < $EMAIL_TEXT
		else
			mutt -F $EMAIL_DIR/muttrc -s "$EMAIL_SUBJECT" -- $EMAIL < $EMAIL_TEXT
		fi
		echo $(cat $EMAIL_TEXT) > $EMAIL_LOG/$EMAIL.mail
	else
		echo "Creation of download token failed for $EMAIL! No mail sent."
	fi
done
