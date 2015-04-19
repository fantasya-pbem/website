#!/usr/bin/php
<?php

$loggingEnabled = true;
//$loggingEnabled = false;

function logReturn($message, $errorCode = 0) {
	global $loggingEnabled;
	if ($loggingEnabled) {
		$prompt   = $errorCode > 0 ? '>>> Fehler: ' : '>>> ';
		$logEntry = PHP_EOL . $prompt . $message . PHP_EOL . PHP_EOL;
		@file_put_contents(__DIR__ . '/app/storage/logs/mailfilter.log', $logEntry, FILE_APPEND);
	}
	if ($errorCode > 0) {
		echo 'Fehler: ' . $message . PHP_EOL;
	}
	exit($errorCode);
}


// Mail einlesen:
if ($argc !== 4) {
	logReturn('Falscher Aufruf des Mailfilter-Skripts.', 1);
}
$sender    = $argv[1];
$size      = $argv[2];
$recipient = $argv[3];
$file      = @fopen('php://stdin', 'r');
if (!$file) {
	logReturn('Die E-Mail konnte nicht eingelesen werden.', 1);
}
$email = '';
while (!@feof($file)) {
	$email .= @fread($file, 8192);
}
@fclose($file);

// Mail loggen:
if ($loggingEnabled ) {
	$file = @fopen(__DIR__ . '/app/storage/logs/mailfilter.log', 'a');
	if ($file) {
		@fputs($file, 'From: ' . $sender . PHP_EOL);
		@fputs($file, 'Size: ' . $size . PHP_EOL);
		@fputs($file, 'To:   ' . $recipient . PHP_EOL . PHP_EOL);
		@fputs($file, $email);
		@fclose($file);
	}
}

// Datenbankkonfiguration einlesen:
$configFile = __DIR__ . '/.env.php';
if (!@is_file($configFile)) {
	logReturn('Datenbankkonfiguration nicht gefunden.', 2);
}
$config = @include($configFile);

// Zuständige Datenbank ermitteln:
$atPos = strpos($recipient, '@fantasya-pbem.de');
if ($atPos <= 0) {
	logReturn('Empfängeradresse fehlerhaft: ', 1);
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
		logReturn('Unbekanntes Postfach: ' . $mailbox, 1);
}

// Header und Mailtext trennen:
$email        = str_replace("\r\n", "\n", $email);
$firstLinePos = strpos($email, "\n\n");
if (!$firstLinePos) {
	logReturn('Anfang der Befehle nicht gefunden.', 1);
}
$headers = trim(substr($email, 0, $firstLinePos));
if (strlen($headers) <= 0) {
	logReturn('Keine E-Mail-Header vorhanden.', 1);
}
$email = trim(quoted_printable_decode(substr($email, $firstLinePos + 2)));
if (strlen($email) <= 0) {
	logReturn('Leerer E-Mail-Text.', 1);
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
	logReturn('Falsches E-Mail-Format: ' . $type, 1);
}

// Befehle extrahieren:
$endOfLine = strpos($email, "\n");
if (!$endOfLine) {
	logReturn('Befehle bestehen nur aus einer Zeile.', 1);
}
$firstLine = substr($email, 0, $endOfLine);
if (strlen($firstLine) <= 0) {
	logReturn('Erste Befehlszeile ist leer.', 1);
}
if (!preg_match('/^([^ ]+)[ ]+([a-zA-Z0-9]+)[ ]+"([^"]*)"$/', $firstLine, $parts) || count($parts) < 4) {
	logReturn('Fehler: Erste Befehlszeile fehlerhaft.', 1);
}
$clientGame = $parts[1];
$party      = $parts[2];
$password   = $parts[3];

try {
	// Authentifizierung prüfen:
	$db     = new PDO('mysql:dbname=' . $database . ';host=localhost', $dbUser, $dbPassword);
	$stmt   = $db->query("SELECT COUNT(*) FROM partei WHERE id = '" . $party . "' AND password = MD5('" . $password . "')");
	$result = $stmt->fetchAll(PDO::FETCH_COLUMN);
	if (!isset($result[0]) || (int)$result[0] !== 1) {
		logReturn('Passwort falsch.', 3);
	}
	// Aktuelle Rundennummer ermitteln:
	$stmt   = $db->query("SELECT Value FROM settings WHERE Name = 'game.runde'");
	$result = $stmt->fetchAll(PDO::FETCH_COLUMN);
	if (!isset($result[0]) || (int)$result[0] <= 0) {
		logReturn('Rundennummer nicht gefunden.', 2);
	}
	$turn = (int)$result[0];
} catch (PDOException $e) {
	logReturn($e->getMessage(), 2);
}

// Befehle in Datei schreiben:
$file = __DIR__ . '/app/storage/orders/' . $game . '/' . $turn . '/' . $party . '.order';
$dir  = dirname($file);
if (!@is_dir($dir)) {
	umask(0022);
	if (!@mkdir($dir, 0755, true)) {
		logReturn('Rundenverzeichnis konnte nicht angelegt werden.', 4);
	}
}
umask(0133);
if (@file_put_contents($file, $email) <= 0) {
	logReturn('Befehle konnten nicht gespeichert werden.', 4);
}

// Bestätigungsmail senden:
$to      = isset($header['Reply-To']) ? implode(', ', $header['Reply-To']) : (isset($header['From']) ? implode(', ', $header['From']) : $sender);
$subject = isset($header['Subject']) ? 'Re: ' . $header['Subject'][0] : 'Fantasya-Befehle sind angekommen';
$message = "Deine Befehle sind angekommen:\n\n" . utf8_decode(@file_get_contents($file));
$from    = "From: Fantasya Server <" . $recipient . ">\r\n"
		 . "Reply-To: Fantasya Admin <admin@fantasya-pbem.de>\r\n"
		 . "X-Mailer: PHP " . phpversion();
if (isset($header['Message-ID'])) {
	$from .= "\r\nIn-Reply-To: " . $header['Message-ID'][0];
}
if (!mail($to, $subject, $message, $from)) {
	logReturn('Antwortmail konnte nicht gesendet werden.', 5);
}

logReturn('erfolgreich');

