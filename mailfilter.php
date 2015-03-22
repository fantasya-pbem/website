#!/usr/bin/php
<?php

$sender = $argv[1];
$size = $argv[2];
$recipient = $argv[3];

// read from stdin
$fd = fopen("php://stdin", "r");
$email = "";
while (!feof($fd)) {
    $email .= fread($fd, 1024);
    }
    fclose($fd);
    
    // Nun kann die E-Mail geparst und bearbeitet werden...
    // Der Inhalt kann in eine Datenbank geschrieben werden, die Anhänge extrahiert und abgelegt werden, Zusatzinhalt kann hinzugefügt werden
    
    $file = fopen(__DIR__ . '/app/storage/logs/mailfilter.log', 'w');
    fputs($file, $sender . PHP_EOL);
    fputs($file, $size . PHP_EOL);
    fputs($file, $recipient . PHP_EOL);
    fputs($file, $email);
    fclose($file);
    
