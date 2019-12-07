<?php

$email = null;

if (!isset($meta)) {
    $error = 'Zugriff verweigert';
    include __DIR__.'/error_message.inc.php';
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

        if ($row['status'] != 'admin') {
            $error = 'Kein Zugriff';
            include __DIR__.'/error_message.inc.php';
        }

        $passwortHash = $row['passwordHash'];

        if (password_verify($passwort, $passwortHash)) {

            $status = $row['status'];
            $userID = $row['id'];
            $email = $row['email'];

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
                    include __DIR__.'/error_message.inc.php';
                }

            } else {
                session_destroy();
                header('Location: ../login.php?message=sessionExpired');
                exit;
            }

        } else {
            $error = 'Kein Zugriff';
            include __DIR__.'/error_message.inc.php';
        }

    } else {
        $error = 'Kein Zugriff';
        include __DIR__.'/error_message.inc.php';
    }

} else {
    $error = 'Kein Zugriff';
    include __DIR__.'/error_message.inc.php';
}
