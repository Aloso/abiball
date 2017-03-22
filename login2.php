<?php

session_start();
include "resources/settings.inc.php";

if (!isset($_POST['vorname']) || !isset($_POST['nachname']) || !isset($_POST['passwort']) ||
        $_POST['vorname'] === '' || $_POST['nachname'] === '' || $_POST['passwort'] === '') {
    header("Location: login.php?error=missingData");
    exit;
}

$vorname = $_POST['vorname'];
$nachname = $_POST['nachname'];
$passwort = $_POST['passwort'];

$escVorname = $mysqli->real_escape_string($vorname);
$escNachname = $mysqli->real_escape_string($nachname);
$escPasswort = $mysqli->real_escape_string($passwort);

$data = $mysqli->query("SELECT * FROM user WHERE vorname LIKE '$escVorname' AND nachname LIKE '$escNachname'");

if (($row = $data->fetch_assoc()) != null) {
    $passwortHash = $row['passwortHash'];
    
    if (($row['status'] == 'inactive' || $row['status'] == 'incomplete') && $passwort == DefaultPassword) {
        $_SESSION['loggedin'] = 'nearly';
        $_SESSION['vorname'] = $vorname;
        $_SESSION['nachname'] = $nachname;
        $_SESSION['passwort'] = $passwort;
        $_SESSION['userID'] = $row['id'];
        
        header("Location: createLogin.php");
        exit;
        
    } else if (password_verify($passwort, $passwortHash)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['vorname'] = $vorname;
        $_SESSION['nachname'] = $nachname;
        $_SESSION['passwort'] = $passwort;
        $_SESSION['userID'] = $row['id'];
    
        $encUserID = $mysqli->real_escape_string($row['id']);
        $timeNow = time();
        
        $success = $mysqli->query("UPDATE user SET lastActive = $timeNow WHERE id = $encUserID");
        if (!$success) {
            $error = 'Fehler beim Speichern des Logins.';
            session_destroy();
            include 'error_message.inc.php';
        }
        
        header("Location: index.php");
        exit;
        
    } else if ($passwortHash == '' && $passwort == DefaultPassword) {
    
        $_SESSION['loggedin'] = 'createPassword';
        $_SESSION['vorname'] = $vorname;
        $_SESSION['nachname'] = $nachname;
        $_SESSION['passwort'] = $passwort;
        $_SESSION['userID'] = $row['id'];
    
        header("Location: createPassword.php");
        exit;
        
    } else {
        header("Location: login.php?error=invalidData");
        exit;
    }
    
} else {
    header("Location: login.php?error=invalidData");
    exit;
}