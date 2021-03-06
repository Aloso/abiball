<?php

@include_once __DIR__.'/settings.inc.php';
if (isset($mysqli)) {
    // Falls die Einstellungen vorhanden sind, muss der Nutzer eingeloggt sein
    session_start();
    require_once __DIR__.'/../verifyLogin.inc.php';
    if ($status !== 'admin') {
        echo 'Keine Berechtigung';
        exit;
    }
}

$htmlTop = '<!DOCTYPE html>
<html lang="de">
<head>
    <title>Seitenerstellung</title>
    <meta charset="utf-8">
    <style>
        * {
            font-family: "Open Sans", sans-serif;
        }

        body {
            max-width: 1200px;
            margin: 0 auto 50px auto;
        }

        form span {
            display: inline-block;
            width: 220px;
        }

        input[type=text] {
            border: 1px solid #aaaaaa;
            border-radius: 3px;
            padding: 4px 7px;
            font-size: 100%;
            width: 300px;
            margin: 8px 0 0 0;
        }

        input[type=text]:hover, input[type=text]:focus {
            border: 1px solid #006bc7;
        }

        input[type=submit] {
            border: 1px solid #bbbbbb;
            border-radius: 3px;
            padding: 6px 14px;
            font-size: 105%;
            color: #222222;
            background-color: #eeeeee;
            margin: 8px 0 0 0;
        }

        input[type=submit]:hover {
            color: black;
            border: 1px solid #aaaaaa;
            background-color: #e1e1e1;
        }

        input[type=submit]:active {
            color: white;
            border: 1px solid #005bcb;
            background-color: #1e73f0;
        }
    </style>
</head>
<body>';

if (isset($_POST['defaultPassword']) && isset($_POST['emailPassword']) &&
    isset($_POST['recaptcha1']) && isset($_POST['recaptcha2']) &&
    isset($_POST['host']) && isset($_POST['user']) && isset($_POST['name']) && isset($_POST['pass'])) {

    if ($_POST['defaultPassword'] != '' && $_POST['host'] != '' &&
        $_POST['user'] != '' && $_POST['name'] != '' && $_POST['pass'] != '') {

        $defaultPassword = $_POST['defaultPassword'];
        $emailPassword = $_POST['emailPassword'];

        $recaptcha1 = $_POST['recaptcha1'];
        $recaptcha2 = $_POST['recaptcha2'];

        $host = $_POST['host'];
        $user = $_POST['user'];
        $name = $_POST['name'];
        $pass = $_POST['pass'];

        $mysqli = @new mysqli($host, $user, $pass, $name);
        if ($mysqli->connect_errno) {
            echo $htmlTop;
            echo '<p style="font-size:120%; color: red">Verbindung fehlgeschlagen: ' . $mysqli->connect_error . '</p>';
        } else {

            $fileStr = /** @lang InjectablePHP */
                '<?php

/*
 * Einstellungen sind durch Ausdrücke folgender Form definiert:
 *
 *      define("Name der Einstellung", "Wert der Einstellung");
 *
 * Du kannst die Einstellungen anpassen, indem du den WERT änderst.
 * Alles bis auf Zahlen und Wahrheitswerte (true / false) muss in Anführungszeichen stehen.
 */


// Voreingestelltes Passwort

define("DefaultPassword", "' . addslashes($defaultPassword) . '");   // Voreingestelltes Passwort der Accounts
define("WebmasterPassword", "' . addslashes($emailPassword) . '");   // Passwort für den Gmail Account
define("useSmtp", ' . ($emailPassword === '' ? 'false' : 'true') . ');


// ReCaptcha

define("ReCaptchaPublic", "' . addslashes($recaptcha1) . '");
define("ReCaptchaPrivate", "' . addslashes($recaptcha2) . '");


// Datenbank-Verbindung

define("DbHost",     "' . addslashes($host) . '");
define("DbDatabase", "' . addslashes($name) . '");
define("DbUsername", "' . addslashes($user) . '");
define("DbPassword", "' . addslashes($pass) . '");


//////// Internal - nicht verändern!

$mysqli = @new mysqli(DbHost, DbUsername, DbPassword, DbDatabase);
if ($mysqli->connect_errno) {
    $error = \'Connect Error: \' . $mysqli->connect_error;
    @include \'../error_message.inc.php\';
    include \'error_message.inc.php\';
}

$mysqli->set_charset("utf8mb4");

$meta = @$mysqli->query(\'SELECT * FROM meta\');
$meta = @$meta->fetch_assoc();

$meta[\'loginTimeout\'] = intval($meta[\'loginTimeout\']);
$meta[\'currentRound\'] = intval($meta[\'currentRound\']);
$meta[\'availableCards\'] = intval($meta[\'availableCards\']);
$meta[\'perUser\'] = intval($meta[\'perUser\']);


$loggedin = false;
';
            $handle = fopen('settings.inc.php', 'w');
            fwrite($handle, $fileStr);
            fclose($handle);

            $host = urlencode($host);
            $name = urlencode($name);
            $user = urlencode($user);
            $pass = urlencode($pass);

            header("Location: create2.php?host=$host&name=$name&user=$user&pass=$pass");
        }
    } else {
        echo $htmlTop;
        echo '<div style="margin: 1em 0; font-size:120%">Bitte alle Felder ausfüllen!</div>';
    }
} else {
    echo $htmlTop;
}

$ignoreFailedDbConnection = true;

echo '<h1>Datenbankverbindung aufbauen</h1>';

echo '
<form action="create.php" method="post">
    <h2>Passwörter</h2>
    <label>
        <span>Standardpasswort:</span>
        <input type="text" name="defaultPassword" value="Abitur" required><br>
        <span></span> Passwort, das jeder User für den ersten Login braucht
    </label><br>
    <label>
        <span>GMail Passwort:</span>
        <input type="text" name="emailPassword" value=""><br>
        <span></span> Passwort des GMail Accounts, von dem die E-Mails gesendet werden sollen
        <p>
            Für den E-Mailversand wird normalerweise SMTP verwendet, da die E-Mails dann seltener als Spam eingestuft werden.
            Falls sich herausstellt, dass dein Webserver keine SMTP-Verbindungen erlaubt,
            kannst du das Gmail-Passwort leer lassen. Es wird dann die mail()-Funktion verwendet.
        </p>
    </label>

    <h2>reCAPTCHA</h2>
    Gehe auf <b><a href="https://www.google.com/recaptcha/">diese Website</a></b> und erstelle ein <b>reCAPTCHA, Version 2</b>.
    Gib dann die reCAPTCHA-Schlüssel hier an:<br>
    <label>
        <span>Websiteschlüssel:</span>
        <input type="text" name="recaptcha1" value="" placeholder="z.B. 6LfqlcYUAAAAAP1Gg1yvlCyQq0etZ66hQReVeBY_">
    </label><br>
    <label>
        <span>Geheimer Schlüssel:</span>
        <input type="text" name="recaptcha2" value="" placeholder="z.B 6LfqlcYUAAAAACK_xWJTdcj5nh6XVFdCTG93Y0Dq">
    </label><br>
    <span></span> Lasse die Felder leer, wenn du reCAPTCHA deaktivieren willst.

    <h2>Datenbank</h2>
    <label>
        <span>Host:</span>
        <input type="text" name="host" value="localhost" required><br>
        <span></span> Lass dies unverändert, falls die Datenbank auf dem gleichen Server ist wie die Website!
    </label><br>
    <label>
        <span>Name der Datenbank:</span>
        <input type="text" name="name" value="" required>
    </label><br>
    <label>
        <span>Nutzername:</span>
        <input type="text" name="user" value="" required>
    </label><br>
    <label>
        <span>Passwort:</span>
        <input type="text" name="pass" value="" required><br>
        <span></span> Diese Anmeldedaten solltest du aufschreiben/im Passwortmanager speichern.
    </label><br>';

if (isset($mysqli)) {
    echo '<p style="padding: 1em; font-size: 120%; background-color: #ffb8b8">
        <b>Warnung</b>: Wenn du fortfährst, wird die bestehende Installation überschrieben.
    </p>';
}

echo '<input type="submit" value="Verbindung prüfen">
</form>';


?>
</body>
</html>
