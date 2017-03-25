<?php

session_start();
require_once 'resources/settings.inc.php';

include '_part1a.inc.php';
echo '
<script src="https://www.google.com/recaptcha/api.js"></script>
<div id="content">';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 'nearly' &&
        isset($_SESSION['passwort']) && $_SESSION['passwort'] == DefaultPassword) {
    
    $encUserID = $mysqli->real_escape_string($_SESSION['userID']);
    
    $data = $mysqli->query("SELECT * FROM user WHERE id = $encUserID");
    
    if (($row = $data->fetch_assoc()) != null) {
        
        if ($row['status'] != 'inactive' && $row['status'] != 'incomplete') {
            $error = 'Ungültige Anfrage';
            include 'error_message.inc.php';
        }
        
        echo '<h1>Account erstellen</h1>
        <p>Herzlich willkommen auf der Abiball-Website des LMGU!</p>';
        
        if (isset($_GET['error'])) {
            $errorMessage = $_GET['error'];
            switch ($errorMessage) {
                case 'invalidEmail':
                    $errorMessage = 'Dies ist keine E-Mailadresse.';
                    break;
                case 'weakPassword':
                    $errorMessage = 'Das Passwort ist zu schwach. Bitte geben Sie mindestens 8 Zeichen ein.';
                    break;
                case 'passwordsDontMatch':
                    $errorMessage = 'Die Passwörter stimmen nicht überein.';
                    break;
                case 'missingData':
                    $errorMessage = 'Bitte füllen Sie alle Felder aus!';
                    break;
                case 'invalidReCaptcha':
                    $errorMessage = 'Das ReCaptcha ist ungültig.';
                    break;
            }
            
            echo '<p class="errorP"><b>Fehler:</b> ' . $errorMessage . '</p>';
        }
        
        echo '
        <form action="createLogin2.php" method="post">
            <label>
                <span class="labelText">E-Mailadresse:</span> <input type="email" name="email"><br>
                Diese verwenden wir, um Sie über wichtige Informationen zur Kartenbestellung zu
                informieren.
            </label>
            <div style="height: 10px"></div>
            <label>
                <span class="labelText">Passwort:</span> <input type="password" name="passwort"> (mindestens 8 Zeichen)
            </label>
            <div style="height: 10px"></div>
            <label>
                <span class="labelText">Passwort wiederholen:</span> <input type="password" name="passwort2">
            </label>
            <div style="height: 10px"></div>
            <span class="labelText"></span>
            <div class="g-recaptcha" data-sitekey="' . ReCaptchaPublic . '" style="display:inline-block"></div>
            <div style="height: 10px"></div>
            <span class="labelText"></span> <input type="submit" value="Absenden">
            <a class="button" href="login.php">Abbrechen</a>
        </form> ';
        
    } else {
        $error = 'Nutzer nicht gefunden';
        include 'error_message.inc.php';
    }
    
} else {
    $error = 'Ungültige Anfrage';
    include 'error_message.inc.php';
}

echo '</div>';
include '_part2.inc.php';