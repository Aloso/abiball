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

        li ul {
            font-size: inherit;
        }
        li ul li {
            margin: 0;
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
        echo '<p>Fehler: ' . $mysqli->connect_error . '</p>';
        exit;
    } else {
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
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\Exception;

$text = 'Dies ist eine Testnachricht.';

try {
    phpMailerSend($email, 'Dies ist ein Test', '<h1 style="margin-top:0">Test</h1>' . $text, $text);
} catch (Exception $e) {
    echo '<span style="color: red">Email-Fehler: ' . $e->errorMessage() . '</span>';
    echo '<p>Falls die Verbindung fehlschlägt, kann daran SMTP schuld sein.
    Manche Server (z.B. bplaced, GoDaddy) verbieten SMTP-Verbindungen. Um dies zu umgehen,
    <a href="create.php">starte die Installation neu</a> und lasse das Gmail-Passwort leer.';
    exit;
}

echo '<h1>Nächste Schritte</h1>

    <ol>
        <li><a href="../index.php" target="_blank">Anmeldung</a><br>
            Dein Passwort: <b>adminadmin</b></li>
        <li><a href="../changePassword.php" target="_blank">Passwort ändern</a></li>
        <li><b>Lösche</b> folgende Dateien im <i>resources</i> Ordner:
            <ul>
                <li>create.php</li>
                <li>create2.php</li>
                <li>create3.php</li>
            </ul></li>
        <li><a href="../admin/settings.php" target="_blank">Einstellungen anpassen</a></li>
        <li><a href="../admin/useruebersicht.php" target="_blank">Die User hinzufügen</a></li>
        <li><a href="../admin/pageTexts.php" target="_blank">Seitentexte anpassen</a><br>
            Zur Formatierung siehe Tipps am Seitenende.</li>
    </ol>';

?>
</body>
</html>
