<?php

/*
 * Einstellungen sind durch Ausdrücke folgender Form definiert:
 *
 *      define("Name der Einstellung", "Wert der Einstellung");
 *
 * Du kannst die Einstellungen anpassen, indem du den WERT änderst.
 * Alles bis auf Zahlen und Wahrheitswerte (true / false) muss in Anführungszeichen stehen.
 */


// Voreingestelltes Passwort

define("DefaultPassword", "Abitur17");         // Voreingestelltes Passwort der Accounts
define("WebmasterPassword", "ananasTree567");  // Passwort für den Gmail Account


// ReCaptcha

define("ReCaptchaPublic", "6LdLXhoUAAAAAFp6ciRUJ0PSmaXw0T32xM-AHsgk");
define("ReCaptchaPrivate", "6LdLXhoUAAAAAPFnfyZv1v6YM_wLjrNSLrL_Jvzb");


// Datenbank-Verbindung

define("DbHost",     "localhost");
define("DbDatabase", "abiball");
define("DbUsername", "php_abiball");
define("DbPassword", "uxvHi9FGbNXkLXd1");


//////// Internal - nicht verändern!

$mysqli = @new mysqli(DbHost, DbUsername, DbPassword, DbDatabase);
if ($mysqli->connect_errno) {
    $error = 'Connect Error: ' . $mysqli->connect_errno;
    @include '../error_message.inc.php';
    include 'error_message.inc.php';
}

$mysqli->set_charset("utf8mb4");

$meta = @$mysqli->query('SELECT * FROM meta');
$meta = @$meta->fetch_assoc();

$meta['loginTimeout'] = intval($meta['loginTimeout']);
$meta['currentRound'] = intval($meta['currentRound']);
$meta['availableCards'] = intval($meta['availableCards']);
$meta['perUser'] = intval($meta['perUser']);


$loggedin = false;
