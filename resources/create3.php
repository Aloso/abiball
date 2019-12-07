<!DOCTYPE html>
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
            margin: 0 auto;
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
        
        input[type=submit], .button {
            border: 1px solid #bbbbbb;
            border-radius: 3px;
            padding: 6px 14px;
            font-size: 105%;
            color: #222222;
            text-decoration: none;
            background-color: #eeeeee;
            margin: 8px 0 0 0;
        }
        
        input[type=submit]:hover, .button:hover {
            color: black;
            border: 1px solid #aaaaaa;
            background-color: #e1e1e1;
        }
        
        input[type=submit]:active, .button:active {
            color: white;
            border: 1px solid #005bcb;
            background-color: #1e73f0;
        }
        p, ul, ol {
            font-size: 120%;
        }
        ul li, ol li {
            margin: 0.7em 0;
        }
    </style>
</head>
<body>
<?php

if (!isset($_GET['email']) || !isset($_GET['host']) || !isset($_GET['name']) || !isset($_GET['user']) || !isset($_GET['pass'])) {
    header('Location: create.php');
    exit;
} else {
    $email = $_GET['email'];
    $host = $_GET['host'];
    $name = $_GET['name'];
    $user = $_GET['user'];
    $pass = $_GET['pass'];
    $mysqli = @new mysqli($host, $user, $pass, $name);
    if ($mysqli->connect_errno) {
        echo '<p>Fehler: ' . $mysqli->connect_errno . '</p>';
        exit;
    } else {
        if (!file_exists('settings.inc.php')) {
            echo '<p style="color: red">Fehler: Die Einstellungsdatei konnte nicht erstellt werden. Bitte stelle sicher, dass PHP Schreibrechte hat.</p>
            <p>Die einfachste Möglichkeit, um dies unter Linux sicherzustellen ist</p>
            <pre>sudo chmod a+rwx -Rf --verbose .</pre>';
            exit;
        }
        require_once 'settings.inc.php';
    }
}

echo '<h1>E-Mailadresse verifizieren</h1>

<p>Um zu prüfen, ob die E-Mail Daten korrekt sind, erhältst du nun eine Testnachricht.</p>

<p>
    Möglicherweise bekommst du in den nächsten Minuten die Nachricht, dass jemand versucht hat,
    sich bei deinem GMail Account einzuloggen. Bestätige, dass du das selbst warst.
    Aktualisiere dann diese Seite, um die E-Mail erneut zu senden.
</p>

<p>
    <b>Fahre erst fort, wenn du die Testnachricht empfangen hast!</b>
    Falls E-Mailadresse bzw. Passwort falsch ist, <a href="create.php">klicke hier</a>.
</p>';

$name = $mysqli->query("SELECT * FROM user WHERE id = 1");
if (($row = $name->fetch_assoc()) != null) {
    $name = $row['vorname'] . ' ' . $row['nachname'];
} else {
    $name = 'deinem Namen';
}

include '../mailtemplate.inc.php';

$text = 'Dies ist eine Testnachricht.';

phpmailerSend($email, 'Dies ist ein Test', '<h1 style="margin-top:0">Test</h1>' . $text, $text);

echo '<h1>Nächste Schritte</h1>

<ol>
    <li><b>Anmeldung</b> als ' . $name . ' und dem Passwort "adminadmin"</li>
    <li><b>Änderung des Passwortes</b> im Profil</li>
    <li><b>Löschen</b> der Dateien <i>create.php</i>, <i>create2.php</i> und <i>create3.php</i> im Ordner <i>resources</i></li>
    <li><b>Anpassen der Einstellungen</b> im Admin-Bereich</li>
    <li><b>Hinzufügen der User</b> im Admin-Bereich</li>
    <li><b>Anpassung der Seitentexte</b> im Admin-Bereich (zur Formatierung siehe Tipps am Seitenende)</li>
</ol>

<a class=\'button\' href=\'../index.php\' target=\'_blank\'>Zur Anmeldung</a>';

?>
</body>
</html>
