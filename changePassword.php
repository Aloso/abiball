<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';
echo '<h1>Passwort ändern</h1>';

$success = false;

if (isset($_POST['oldPassword']) && isset($_POST['password']) && isset($_POST['confirm'])) {
    $oldPassword = $_POST['oldPassword'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    
    $data = $mysqli->query("SELECT * FROM user WHERE id = $userID");
    if (($row = $data->fetch_assoc()) != null) {
        $hash = $row['passwortHash'];
        if (password_verify($oldPassword, $hash)) {
            if (strlen($password) > 7) {
                if ($password == $confirm) {
                    echo '<p>Das Passwort wurde aktualisiert.</p>';
            
                    $encPassword = $mysqli->real_escape_string(password_hash($password, PASSWORD_BCRYPT));
            
                    $success = $mysqli->query("UPDATE user SET passwortHash = '$encPassword' WHERE id = $userID");
                    if (!$success) {
                        echo '<p class="errorP"><b>Fehler:</b> Bitte versuchen Sie es erneut.</p>';
                    } else {
                        $_SESSION['passwort'] = $password;
                    }
                } else {
                    echo '<p class="errorP"><b>Fehler:</b> Die Passwörter stimmen nicht überein.</p>';
                }
            } else {
                echo '<p class="errorP"><b>Fehler:</b> Das angegebene Passwort ist nicht stark genug. Bitte
                geben Sie mindestens 8 Zeichen ein.</p>';
            }
        } else {
            echo '<p class="errorP"><b>Fehler:</b> Das alte Passwort stimmt nicht.</p>';
        }
    } else {
        header('Location: logout.php');
        exit;
    }
}

if (!$success) {
    echo '<form action="changePassword.php" method="post">
        <span class="labelText">Altes Passwort:</span> <input type="password" name="oldPassword">
        <div style="height:10px"></div>
        <span class="labelText">Neues Passwort:</span> <input type="password" name="password">
        <div style="height:10px"></div>
        <span class="labelText">Nochmal eingeben:</span> <input type="password" name="confirm">
        <div style="height:10px"></div>
        <span class="labelText"></span> <input type="submit" value="Absenden">
    </form>';
}

echo '<p><a href="profil.php">Zurück zum Profil</a></p>';


echo '</div>';
include '_part2.inc.php';