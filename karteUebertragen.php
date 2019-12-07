<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

if (!isset($_POST['id'])) {
    header('Location: profil.php');
    exit;
}

$id = $_POST['id'];
$encID = $mysqli->real_escape_string($_POST['id']);

$bestellung = $mysqli->query("SELECT * FROM bestellungen WHERE id = $encID");
if (($row = $bestellung->fetch_assoc()) != null) {

    $fullname = $row['name'];

    if (isset($_POST['newName'])) {
        $fullname = $_POST['newName'];
        $encName = $mysqli->real_escape_string($fullname);
        $len = strlen($fullname);

        if ($len == 0) {
            echo '<div class="error message"><b>Fehler:</b> Bitte einen Namen eingeben!</div>';
        } else if ($len < 5) {
            echo '<div class="error message"><b>Fehler:</b> Der angegebene Name ist zu kurz.</div>';
        } else if ($len > 50) {
            echo '<div class="error message"><b>Fehler:</b> Der angegebene Name ist zu lang (länger als 50 Zeichen).</div>';
        } else {

            $success = $mysqli->query("UPDATE bestellungen SET name = '$encName' WHERE id = $encID");
            if ($success) {
                echo '<div class="success message">Die Karte wurde übertragen.</div>
                <a class="button primary" href="profil.php">Zum Profil</a>';
            } else {
                echo '<div class="error message"><b>Fehler:</b> In der Datenbank liegt ein Problem vor.
                        Bitte kontaktiere den Webmaster: ' . $meta['webmasterMail'] . '</div>';
            }

        }
    }

    echo '<h1>Karte übertragen</h1>
    <form action="karteUebertragen.php" method="post">
        <fieldset>
            <p>Wer soll diese Karte erhalten?</p>
            <input type="hidden" name="id" value="' . $id . '">
            <label>
                <span class="labelText">Vor- und Nachname:</span>
                <input type="text" name="newName" value="' . $fullname . '" placeholder="' . $fullname . '">
            </label>
            <div style="height:10px"></div>
            <input type="submit" value="Speichern">
            <a class="button" href="profil.php">Abbrechen</a>
        </fieldset>
    </form>';

} else {
    $error = 'Die Karte existiert nicht';
    include 'error_message.inc.php';
}

echo '</div>';
include '_part2.inc.php';
