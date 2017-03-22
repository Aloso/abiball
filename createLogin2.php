<?php

session_start();
require_once 'resources/settings.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 'nearly' &&
        isset($_SESSION['passwort']) && $_SESSION['passwort'] == DefaultPassword) {
    
    $encUserID = $mysqli->real_escape_string($_SESSION['userID']);
    
    $data = $mysqli->query("SELECT * FROM user WHERE id = $encUserID");
    
    if (($row = $data->fetch_assoc()) != null) {
    
        if ($row['status'] != 'inactive' && $row['status'] != 'incomplete') {
            $error = 'Ungültige Anfrage';
            include 'error_message.inc.php';
        }
        
        if (isset($_POST['email']) && isset($_POST['passwort']) && isset($_POST['passwort2']) &&
                $_POST['email'] != '' && $_POST['passwort'] != '' && $_POST['passwort2'] != '') {
    
            $email = $_POST['email'];
            $passwort = $_POST['passwort'];
            $passwort2 = $_POST['passwort2'];
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                
                if (strlen($passwort) > 7) {
                    
                    if ($passwort == $passwort2) {
    
                        $encEmail = $mysqli->real_escape_string($email);
                        $encPasswort = $mysqli->real_escape_string(password_hash($passwort, PASSWORD_BCRYPT));
                        $encEmail = $mysqli->real_escape_string($email);
                        
                        $vString = time() . mt_rand(1000, 10000);
                        
                        $success = $mysqli->query("UPDATE user SET email = '$encEmail', status = 'incomplete',
                                passwortHash = '$encPasswort', verificationString = $vString
                                WHERE id = $encUserID");
                        
                        if ($success) {
                            $_SESSION['passwort'] = $passwort;
                            $_SESSION['loggedin'] = 'nearly';
    
                            $empfaenger = $email;
                            $betreff = 'Abiball 2017 - Bestätigung Ihres Accounts';
                            $nachricht = 'Sie erhalten diese Nachricht, weil mit Ihrer E-Mailadresse ein Account für den Kartenverkauf des Abiballs des LMGU erstellt wurde.

Um den Account zu aktivieren, rufen Sie bitte folgenden Link auf:

        ' . $meta['url'] . 'verifyAccount.php?id=' . $encUserID . '&verificationString=' . urlencode($vString) . '

Falls dies ein Versehen ist, können Sie diese Nachricht ignorieren.';
                            $header = 'From: ' . $meta['webmasterMail'] . "\r\n" .
                                    'Reply-To: ' . $meta['webmasterMail'] . "\r\n" .
                                    'X-Mailer: PHP/' . phpversion();
    
                            if (mail($empfaenger, $betreff, $nachricht, $header)) {
                                header('Location: unverifiedLanding.php?');
                                exit;
                            } else {
                                header('Location: unverifiedLanding.php?error=sendFailed');
                                exit;
                            }
                        } else {
                            $error = 'Datenbankfehler: ' . $mysqli->error;
                            include 'error_message.inc.php';
                        }
                        
                    } else {
                        header('Location: createLogin.php?error=passwordsDontMatch');
                        exit;
                    }
                    
                } else {
                    header('Location: createLogin.php?error=weakPassword');
                    exit;
                }
                
            } else {
                header('Location: createLogin.php?error=invalidEmail');
                exit;
            }
            
        } else {
            header('Location: createLogin.php?error=missingData');
            exit;
        }
        
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