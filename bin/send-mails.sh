#!/bin/bash

HOST=localhost
DATABASE=
USER=
PASSWORD=''
TURN=`mysql -N -h $HOST -D $DATABASE -u $USER -p$PASSWORD -e "SELECT Value FROM settings WHERE Name='game.runde'"`
GAME=spiel
MONSTER_PARTIES=(0 dark tier)
BASE_DIR=/home/fantasya/games/$GAME
ZIP_DIR=zip
EMAIL_DIR=email
EMAIL_SUBJECT="Fantasya AW $TURN"
EMAIL_TEXT=$EMAIL_DIR/turn.email.txt
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
for ID in `mysql -N -s -h $HOST -u $USER -D $DATABASE -p$PASSWORD -e "SELECT id FROM partei WHERE id NOT IN ($monsterParties)"`
do
	EMAIL=`mysql -N -s -h $HOST -u $USER -D $DATABASE -p$PASSWORD -e "SELECT email FROM partei WHERE id = '$ID'"`
	ZIP=$ZIP_DIR/$TURN/$TURN-$ID.zip
	echo "$ZIP -> $EMAIL"
	mutt -F $EMAIL_DIR/muttrc -s "$EMAIL_SUBJECT" -a $ZIP -- $EMAIL < $EMAIL_TEXT
	echo $(cat $EMAIL_TEXT) > $EMAIL_LOG/$EMAIL.mail
done
