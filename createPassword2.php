<?php

session_start();
require_once 'resources/settings.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 'createPassword') {
    
    $encUserID = $mysqli->real_escape_string($_SESSION['userID']);
    
    $data = $mysqli->query("SELECT * FROM user WHERE id = $encUserID");
    
    if (($row = $data->fetch_assoc()) != null) {
        
        if ($row['status'] == 'inactive' || $row['status'] == 'incomplete') {
            $error = 'Ungültige Anfrage';
            include 'error_message.inc.php';
        }
        
        if (isset($_POST['passwort']) && isset($_POST['passwort2']) &&
                $_POST['passwort'] != '' && $_POST['passwort2'] != '') {
            
            $passwort = $_POST['passwort'];
            $passwort2 = $_POST['passwort2'];
        
            if (strlen($passwort) > 7) {
                
                if ($passwort == $passwort2) {
                    
                    $encPasswort = $mysqli->real_escape_string(password_hash($passwort, PASSWORD_BCRYPT));
                    
                    $success = $mysqli->query("UPDATE user SET passwortHash = '$encPasswort'
                            WHERE id = $encUserID");
                    
                    if ($success) {
                        $_SESSION['passwort'] = $passwort;
                        $_SESSION['loggedin'] = true;
                        
                        $now = time();
                        
                        $mysqli->query("UPDATE user SET lastActive = $now WHERE id = $encUserID");
                        
                        header('Location: index.php');
                        exit;
                    } else {
                        $error = 'Datenbankfehler: ' . $mysqli->error;
                        include 'error_message.inc.php';
                    }
                    
                } else {
                    header('Location: createPassword.php?error=passwordsDontMatch');
                    exit;
                }
                
            } else {
                header('Location: createPassword.php?error=weakPassword');
                exit;
            }
            
        } else {
            header('Location: createPassword.php?error=missingData');
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