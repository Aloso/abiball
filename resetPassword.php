<?php

session_start();
session_unset();
include "resources/settings.inc.php";

if (!isset($_GET['verificationString']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}


include '_part1a.inc.php';
echo '<div id="content">';

$vString = $_GET['verificationString'];
$id = $_GET['id'];

$encVString = $mysqli->real_escape_string($vString);
$encID = $mysqli->real_escape_string($id);

$user = $mysqli->query("SELECT * FROM user WHERE verificationString = '$encVString' AND id = $encID");
if (($row = $user->fetch_assoc()) != null) {
    
    if ($row['status'] != 'incomplete' && $row['verificationString'] != '') {
        
        $success = $mysqli->query("UPDATE user SET verificationString = '' WHERE id = $encID");
        if ($success) {
            
            $_SESSION['loggedin'] = 'createPassword';
            $_SESSION['vorname'] = $row['vorname'];
            $_SESSION['nachname'] = $row['nachname'];
            $_SESSION['passwort'] = DefaultPassword;
            $_SESSION['userID'] = $id;
    
            header("Location: createPassword.php");
            exit;
            
        } else {
            echo '<div class="error message">Die Zurücksetzung des Passworts war nicht erfolgreich.</div>
                    <a class="button primary" href="index.php">Startseite</a>';
        }
        
    } else {
        echo '<div class="error message">Der Passwort-Zurücksetzungslink ist nicht gültig!</div>
                <a class="button primary" href="index.php">Startseite</a>';
    }
    
} else {

}



echo '</div>';
include '_part2.inc.php';