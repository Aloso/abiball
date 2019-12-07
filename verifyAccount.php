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

    if ($row['status'] == 'incomplete') {

        $success = $mysqli->query("UPDATE user SET status = 'member', verificationString = '' WHERE id = $encID");
        if ($success) {
            echo '<div class="success message">Der Account wurde aktiviert. Wechseln Sie zur
                    Startseite, um sich anzumelden.</div>
                    <a class="button primary" href="index.php">Startseite</a>';
        } else {
            echo '<div class="error message">Die Aktivierung war nicht erfolgreich.</div>
                    <a class="button primary" href="index.php">Startseite</a>';
        }

    } else {
        echo '<div class="error message">Der Aktivierunglink ist nicht gültig!</div>
                <a class="button primary" href="index.php">Startseite</a>';
    }

} else {
    echo '<div class="error message">Der Aktivierunglink ist nicht gültig!</div>
                <a class="button primary" href="index.php">Startseite</a>';
}



echo '</div>';
include '_part2.inc.php';
