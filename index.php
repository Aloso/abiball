<?php

session_start();
@include_once 'resources/settings.inc.php';
if (!isset($mysqli)) {
    $error = 'Nicht initialisiert';
    include 'error_message.inc.php';
}
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

// Kurzinfo und Zahlungserinnerung
$encID = $mysqli->real_escape_string($_SESSION['userID']);
$anz = $mysqli->query('SELECT COUNT(*) FROM bestellungen WHERE userID = ' . $encID);
$bestellungen = $anz->fetch_assoc()['COUNT(*)'];

if ($bestellungen != '0') {
    echo '<p>Sie haben bisher ' . $bestellungen . ' Karte(n) bestellt. Im
            <a href="profil.php">Profil</a> werden alle Ihre Bestellungen angezeigt.</p>
            <a class="button primary" href="rechnung.php">Rechnung als PDF-Dokument speichern</a>';

    $data = $mysqli->query('SELECT SUM(preis) FROM bestellungen WHERE bezahlt = FALSE AND userID = ' . $userID);
    $unbezahlteBestellungen = $data->fetch_assoc()['SUM(preis)'];
    if ($unbezahlteBestellungen != '0' && $unbezahlteBestellungen != null) {
        echo '<p>
            Sie haben noch einen offenen Betrag von <b style="color: #c30200; text-shadow: 0 0 3px white">' . $unbezahlteBestellungen . ' Euro</b>.<br>
            <span style="font-size:90%">(Falls Sie den Betrag bereits überwiesen haben, kann es eine Weile dauern, bis das hier sichtbar wird)</span>
        </p>';
    }
}

$data = $mysqli->query("SELECT * FROM seitentexte WHERE name = 'Aktuelles'");
if (($row = $data->fetch_assoc()) != null) {
    echo '<div class="pageText">' . $row['htmlText'] . '</div>';
}

echo '</div>';
include '_part2.inc.php';
