<?php

session_start();
require_once 'resources/settings.inc.php';

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['userID'])) {
    $error = 'Ungültige Anfrage';
    include 'error_message.inc.php';
}

include '_part1a.inc.php';
echo '<div id="content">';

if ($_SESSION['loggedin'] == 'createPassword') {
    
    $encUserID = $mysqli->real_escape_string($_SESSION['userID']);
    
    $data = $mysqli->query("SELECT * FROM user WHERE id = $encUserID");
    if (($row = $data->fetch_assoc()) != null) {
        
        if ($row['status'] != 'blocked' && $row['status'] != 'inactive') {
            
            echo '<h1>Neues Passwort wählen</h1>
            <p>Ihr Passwort wurde zurückgesetzt. Bitte wählen Sie ein neues:</p>';
    
            if (isset($_GET['error'])) {
                $errorMessage = $_GET['error'];
                switch ($errorMessage) {
                    case 'weakPassword':
                        $errorMessage = 'Das Passwort ist zu schwach. Bitte geben Sie mindestens 8 Zeichen ein.';
                        break;
                    case 'passwordsDontMatch':
                        $errorMessage = 'Die Passwörter stimmen nicht überein.';
                        break;
                    case 'missingData':
                        $errorMessage = 'Bitte füllen Sie alle Felder aus!';
                        break;
                }
        
                echo '<p class="errorP"><b>Fehler:</b> ' . $errorMessage . '</p>';
            }
    
            echo '
            <form action="createPassword2.php" method="post">
                <label>
                    <span class="labelText">Passwort:</span> <input type="password" name="passwort"> (mindestens 8 Zeichen)
                </label>
                <div style="height: 10px"></div>
                <label>
                    <span class="labelText">Passwort wiederholen:</span> <input type="password" name="passwort2">
                </label>
                <div style="height: 10px"></div>
                <span class="labelText"></span> <input type="submit" value="Absenden">
                <a class="button" href="login.php">Abbrechen</a>
            </form> ';
            
            
        } else {
            $error = 'Ungültige Anfrage';
            include 'error_message.inc.php';
        }
        
    } else {
        $error = 'Ungültige Anfrage';
        include 'error_message.inc.php';
    }
    
} else {
    $error = 'Ungültige Anfrage';
    include 'error_message.inc.php';
}

echo '</div>';
include '_part2.inc.php';