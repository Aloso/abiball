<?php

session_start();
include "resources/settings.inc.php";
if (!defined(DbDatabase)) {
    $error = 'Nicht initialisiert';
    include 'error_message.inc.php';
}

include "_part1.inc.php";

if (isset($_GET['error'])) {
    $errorMessage = $_GET['error'];
    switch ($errorMessage) {
        case 'unauthorized':
            $errorMessage = 'Sie müssen sich anmelden, um diese Seite zu sehen.';
            break;
        case 'expired':
            $errorMessage = 'Die Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.';
            break;
        case 'blocked':
            $errorMessage = 'Sie wurden blockiert.';
            break;
        case 'notAuthorizedToReset':
            $errorMessage = 'Sie können Ihr Passwort nicht zurücksetzen, bevor Sie sich das erste Mal angemeldet haben.';
            break;
        case 'resetEmailSent':
            $errorMessage = 'Die E-Mail zum Zurücksetzen Ihres Passwortes konnte nicht versendet werden.';
            break;
        case 'invalidData':
            $errorMessage = 'Die angegebenen Daten sind ungültig.';
            break;
        case 'missingData':
            $errorMessage = 'Bitte alle Felder ausfüllen!';
            break;
    }
    echo '<div class="message error">' . $errorMessage . '</div>';
    
} else if (isset($_GET['message'])) {
    
    $message = $_GET['message'];
    switch ($message) {
        case 'sessionExpired':
            $message = 'Sie waren zu lange inaktiv und<br>wurden automatisch ausgeloggt.';
            break;
        case 'resetEmailSent':
            $message = 'Die E-Mail zum Zurücksetzen<br>Ihres Passwortes wurde abgesendet.';
            break;
    }
    echo '<div class="message">' . $message . '</div>';
    
} else if (isset($loggedout)) {
    echo '<div class="message">Sie wurden ausgeloggt.</div>';
}

echo '<div id="loginBox">
<h3>Login</h3>
<form action="login2.php" method="post" style="margin: 0">
    <label>
        Vorname:<br>
        <input type="text" name="vorname" />
    </label><br>
    <label>
        Nachname:<br>
        <input type="text" name="nachname" />
    </label><br>
    <label>
        Passwort:<br>
        <input type="password" name="passwort" />
    </label>
    <div style="text-align: right;">
        <input type="submit" value="Einloggen" />
    </div>
    <a href="help.php" style="position: absolute; bottom: 20px; font-size: 85%">Passwort vergessen?</a>
</form>
</div>';

include "_part2.inc.php";
