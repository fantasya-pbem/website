#!/usr/bin/php
<?php

// Mail einlesen:
$sender    = $argv[1];
$size      = $argv[2];
$recipient = $argv[3];
$file      = fopen('php://stdin', 'r');
$email     = '';
while (!feof($file)) {
    $email .= fread($file, 1024);
}
fclose($file);

// Mail loggen:
//$file = fopen(__DIR__ . '/app/storage/logs/mailfilter.log', 'w');
//fputs($file, 'From: ' . $sender . PHP_EOL);
//fputs($file, 'Size: ' . $size . PHP_EOL);
//fputs($file, 'To:   ' . $recipient . PHP_EOL . PHP_EOL);
//fputs($file, $email);
//fclose($file);

// Datenbankkonfiguration einlesen:
$configFile = __DIR__ . '/.env.php';
if (!is_file($configFile)) {
    echo 'Datenbankkonfiguration nicht gefunden.' . PHP_EOL;
    exit(1);
}
$config = include($configFile);

// Befehle extrahieren:
$atPos = strpos($recipient, '@fantasya-pbem.de');
if ($atPos <= 0) {
	echo 'Empfängeradresse fehlerhaft: ' . $recipient . PHP_EOL;
	exit(1);
}
$mailbox = substr($recipient, 0, $atPos);
switch ($mailbox) {
    case 'befehle' :
        $game       = 'spiel';
        $database   = $config['MYSQL_DB_MAIN'];
        $dbUser     = $config['MYSQL_USER_MAIN'];
        $dbPassword = $config['MYSQL_PASS_MAIN'];
        break;
	case 'beta' :
	case 'test' :
        $game       = 'beta';
        $database   = $config['MYSQL_DB_BETA'];
        $dbUser     = $config['MYSQL_USER_BETA'];
        $dbPassword = $config['MYSQL_PASS_BETA'];
        break;
    default :
        echo 'Unbekanntes Postfach: ' . $mailbox . PHP_EOL;
		exit(1);
}

$email        = str_replace("\r\n", "\n", $email);
$firstLinePos = strpos($email, "\n\n");
if (!$firstLinePos) {
    echo 'Fehler: Anfang der Befehle nicht gefunden.';
    exit(1) . PHP_EOL;
}
$email = quoted_printable_decode(substr($email, $firstLinePos + 2));
if (strlen($email) <= 0) {
    echo 'Fehler: Leerer E-Mail-Text.';
    exit(1) . PHP_EOL;
}
$endOfLine = strpos($email, "\n");
if (!$endOfLine) {
    echo 'Fehler: Befehle bestehen nur aus einer Zeile.';
    exit(1) . PHP_EOL;
}
$firstLine = substr($email, 0, $endOfLine);
if (strlen($firstLine) <= 0) {
    echo 'Fehler: Erste Befehlszeile ist leer.' . PHP_EOL;
    exit(1);
}
if (!preg_match('/^([^ ]+)[ ]+([a-z0-9]+)[ ]+"([^"]*)"$/', $firstLine, $parts) || count($parts) < 4) {
    echo 'Fehler: Erste Befehlszeile fehlerhaft.' . PHP_EOL;
    exit(1);
}
$clientGame = $parts[1];
$party      = $parts[2];
$password   = $parts[3];

//echo 'Spielkennzeichnung: ' . $parts[1] . PHP_EOL;
//echo 'Partei-ID: ' . $parts[2]. PHP_EOL;
//echo 'Passwort: ' . $parts[3] . PHP_EOL;

// Authentifizierung prüfen:
try {
    $db     = new PDO('mysql:dbname=' . $database . ';host=localhost', $dbUser, $dbPassword);
    $stmt   = $db->query("SELECT COUNT(*) FROM partei WHERE id = '" . $party . "' AND password = MD5('" . $password . "')");
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!isset($result[0]) || (int)$result[0] !== 1) {
        echo 'Fehler: Passwort falsch.' . PHP_EOL;
        exit(3);
    }
} catch (PDOException $e) {
    echo 'Fehler: ' . $e->getMessage() . PHP_EOL;
    exit(2);
}

try {
    $stmt   = $db->query("SELECT Value FROM settings WHERE Name = 'game.runde'");
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!isset($result[0]) || (int)$result[0] <= 0) {
        echo 'Fehler: Rundennummer nicht gefunden.' . PHP_EOL;
        exit(2);
    }
    $turn = (int)$result[0];
} catch (PDOException $e) {
    echo 'Fehler: ' . $e->getMessage() . PHP_EOL;
    exit(2);
}

// Befehle in Datei schreiben:
$file = __DIR__ . '/app/storage/orders/' . $game . '/' . $turn . '/' . $party . '.order';
$dir  = dirname($file);
if (!is_dir($dir)) {
    umask(0002);
    if (!@mkdir($dir, 0775, true)) {
        echo 'Fehler: Rundenverzeichnis konnte nicht angelegt werden.' . PHP_EOL;
        exit(1);
    }
}
if (@file_put_contents($file, $email) <= 0) {
    echo 'Fehler: Befehle konnten nicht gespeichert werden.' . PHP_EOL;
    exit(1);
}
