#!/usr/bin/php
<?php

// Mail einlesen:
$sender    = $argv[1];
$size      = $argv[2];
$recipient = $argv[3];
$file      = fopen('php://stdin', 'r');
if (!$file) {
	echo 'Fehler beim Einlesen der E-Mail.' . PHP_EOL;
	exit(1);
}
$email = '';
while (!feof($file)) {
	$email .= fread($file, 8192);
}
fclose($file);

// Mail loggen:
$loggingEnabled = true;
$loggingEnabled = false;
if ($loggingEnabled ) {
	$file = fopen(__DIR__ . '/app/storage/logs/mailfilter.log', 'a');
	if ($file) {
	fputs($file, 'From: ' . $sender . PHP_EOL);
	fputs($file, 'Size: ' . $size . PHP_EOL);
	fputs($file, 'To:   ' . $recipient . PHP_EOL . PHP_EOL);
	fputs($file, $email);
		fclose($file);
	}
}

// Datenbankkonfiguration einlesen:
$configFile = __DIR__ . '/.env.php';
if (!is_file($configFile)) {
	echo 'Datenbankkonfiguration nicht gefunden.' . PHP_EOL;
	exit(2);
}
$config = include($configFile);

// Zuständige Datenbank ermitteln:
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

// Header und Mailtext trennen:
$email        = str_replace("\r\n", "\n", $email);
$firstLinePos = strpos($email, "\n\n");
if (!$firstLinePos) {
	echo 'Fehler: Anfang der Befehle nicht gefunden.' . PHP_EOL;
	exit(1);
}
$headers = trim(substr($email, 0, $firstLinePos));
if (strlen($headers) <= 0) {
	echo 'Fehler: Keine E-Mail-Header vorhanden.' . PHP_EOL;
	exit(1);
}
$email = trim(quoted_printable_decode(substr($email, $firstLinePos + 2)));
if (strlen($email) <= 0) {
	echo 'Fehler: Leerer E-Mail-Text.' . PHP_EOL;
	exit(1);
}

// Header parsen:
$header = array();
foreach (explode("\n", preg_replace('/\n[ \t]+/', ' ', $headers)) as $h) {
	if (preg_match("/^([A-Z][A-Za-z-]*):[ \t]+(.*)$/", $h, $matches) === 1) {
		$tag = $matches[1];
		if (!isset($header[$tag])) {
			$header[$tag] = array();
		}
		$header[$tag][] = rtrim($matches[2]);
	}
}

// E-Mail-Format validieren:
$type = isset($header['Content-Type']) ? $header['Content-Type'] : array('');
if (strpos($type[0], 'text/plain') !== 0) {
	echo 'Fehler: Falsches E-Mail-Format: ' . $type . PHP_EOL;
	exit(1);
}

// Befehle extrahieren:
$endOfLine = strpos($email, "\n");
if (!$endOfLine) {
	echo 'Fehler: Befehle bestehen nur aus einer Zeile.' . PHP_EOL;
	exit(1);
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

// Aktuelle Rundennummer ermitteln:
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
	umask(0022);
	if (!@mkdir($dir, 0755, true)) {
		echo 'Fehler: Rundenverzeichnis konnte nicht angelegt werden.' . PHP_EOL;
		exit(4);
	}
}
umask(0133);
if (@file_put_contents($file, $email) <= 0) {
	echo 'Fehler: Befehle konnten nicht gespeichert werden.' . PHP_EOL;
	exit(4);
}

// Bestätigungsmail senden:
$to      = isset($header['Reply-To']) ? implode(', ', $header['Reply-To']) : (isset($header['From']) ? implode(', ', $header['From']) : $sender);
$subject = isset($header['Subject']) ? 'Re: ' . $header['Subject'][0] : 'Fantasya-Befehle sind angekommen';
$message = "Deine Befehle sind angekommen:\n\n" . utf8_decode(file_get_contents($file));
$from    = "From: Fantasya Server <" . $recipient . ">\r\n"
		 . "Reply-To: Fantasya Admin <admin@fantasya-pbem.de>\r\n"
		 . "X-Mailer: PHP " . phpversion();
if (isset($header['Message-ID'])) {
	$from .= "\r\nIn-Reply-To: " . $header['Message-ID'][0];
}
if (!mail($to, $subject, $message, $from)) {
	echo 'Antwortmail konnte nicht gesendet werden.' . PHP_EOL;
	exit(5);
}
