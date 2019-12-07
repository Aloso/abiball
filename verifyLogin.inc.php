<?php

if (!isset($meta)) {
    $error = "Zugriff verweigert";
    include "error_message.inc.php";
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] && isset($_SESSION['vorname'])
        && isset($_SESSION['nachname']) && isset($_SESSION['passwort'])) {
    
    $vorname = $_SESSION['vorname'];
    $nachname = $_SESSION['nachname'];
    $passwort = $_SESSION['passwort'];
    
    $escVorname = $mysqli->real_escape_string($vorname);
    $escNachname = $mysqli->real_escape_string($nachname);
    $escPasswort = $mysqli->real_escape_string($passwort);
    
    $data = $mysqli->query("SELECT * FROM user WHERE vorname LIKE '$escVorname' AND nachname LIKE '$escNachname'");
    
    if (($row = $data->fetch_assoc()) != null) {
    
        $passwortHash = $row['passwortHash'];

        if (password_verify($passwort, $passwortHash)) {
            
            $status = $row['status'];
            $userID = $row['id'];
            $email = $row['email'];
            
            if ($status != 'blocked' && $status != 'incomplete') {
                
                $lastActive = $row['lastActive'];
                $timeNow = time();
                
                if (($timeNow - $lastActive) < $meta['loginTimeout']) {
                    
                    $_SESSION['loggedin'] = true;
                    $_SESSION['vorname'] = $vorname;
                    $_SESSION['nachname'] = $nachname;
                    $_SESSION['passwort'] = $passwort;
                    $_SESSION['userID'] = $row['id'];
                    
                    $encUserID = $mysqli->real_escape_string($row['id']);
                    
                    $success = $mysqli->query("UPDATE user SET lastActive = $timeNow WHERE id = $encUserID");
                    if (!$success) {
                        $error = 'Fehler beim Speichern des Logins.';
                        session_destroy();
                        include 'error_message.inc.php';
                    }
                    
                } else {
                    session_destroy();
                    header('Location: login.php?message=sessionExpired');
                    exit;
                }
                
            } else if ($status == 'incomplete') {
                header('Location: unverifiedLanding.php');
                exit;
            } else {
                header('Location: login.php?error=blocked');
                exit;
            }
            
        } else {
            header('Location: login.php?error=invalidData');
            exit;
        }
        
    } else {
        header('Location: login.php?error=invalidData');
        exit;
    }
    
} else if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === 'nearly') {
    header('Location: unverifiedLanding.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}