<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

$encUserID = $mysqli->real_escape_string($userID);

$eigeneKarten = $mysqli->query("SELECT COUNT(*) FROM bestellungen WHERE userID = $encUserID");
if ($eigeneKarten->num_rows == 0) {
    $error = 'Fehler bei einer Datenbankabfrage';
    include 'error_message.inc.php';
}
$eigeneKarten = intval($eigeneKarten->fetch_assoc()['COUNT(*)']);

if ($eigeneKarten <= 0) {
    header('Location: reservierung.php?error=boughtNoCards');
    exit;
}
if ($meta['reservierungAktiviert'] != '1') {
    header('Location: reservierung.php?error=disabled');
    exit;
}


$data = $mysqli->query('
SELECT r.rID id, r.prioritaet prioritaet,
    u2.vorname vn, u2.nachname nn, u2.id u2id, u2.email mail, u2.status stat,
    personenAnz
FROM reservierung r
    JOIN user u1 ON r.userID = u1.id
    JOIN user u2 ON r.wunschUserID = u2.id
    JOIN (
        SELECT userID, COUNT(id) personenAnz
        FROM bestellungen b
        GROUP BY userID
    ) pers ON pers.userID = u2.id
WHERE u1.id = ' . $encUserID);

$availableWishes = $meta['maxReservierungen'] - $data->num_rows;
$availablePoints = $meta['reservierungsPunkte'];

// enthält alle Personen, die bereits gespeichert wurden
$personen = array();
$selbst = $vorname . ' ' . $nachname;

if ($data->num_rows > 0) {
    while (($row = $data->fetch_assoc()) != null) {
        $availablePoints -= $row['prioritaet'];
        $personen[$row['vn'] . ' ' . $row['nn']] = 1;
    }
}

if (!isset($_POST['vorname']) || !isset($_POST['nachname']) || !isset($_POST['punkte'])
        || $_POST['vorname'] == '' || $_POST['nachname'] == '' || $_POST['punkte'] == '') {
    header('Location: reservierung.php?error=emptyString');
    exit;
}

$punkte = intval($_POST['punkte']);
$name = $_POST['vorname'] . ' ' . $_POST['nachname'];

if ($name == $selbst) {
    header('Location: reservierung.php?error=selbst');
    exit;
}
if (isset($personen[$name])) {
    header('Location: reservierung.php?error=schonVorhanden');
    exit;
}
if ($data->num_rows >= $meta['maxReservierungen']) {
    header('Location: reservierung.php?error=reservierungenAufgebraucht');
    exit;
}
if ($availablePoints <= 0) {
    header('Location: reservierung.php?error=punkteAufgebraucht');
    exit;
}
if ($availablePoints - $punkte < 0) {
    header('Location: reservierung.php?error=zuVielePunkteAngegeben');
    exit;
}

$encWunschVorname = $mysqli->real_escape_string($_POST['vorname']);
$encWunschNachname = $mysqli->real_escape_string($_POST['nachname']);

$wunschData = $mysqli->query("SELECT * FROM user WHERE vorname = '$encWunschVorname' AND nachname = '$encWunschNachname'");

if (($row = $wunschData->fetch_assoc()) == null) {
    header('Location: reservierung.php?error=notparticipating');
    exit;
}

$wunschID = $row['id'];

$wunschData = $mysqli->query("SELECT * FROM bestellungen WHERE userID = $wunschID");

if ($wunschData->num_rows == 0) {
    header('Location: reservierung.php?error=notparticipating');
    exit;
}

$encPrioritaet = $mysqli->real_escape_string($_POST['punkte']);

$success = $mysqli->query("INSERT INTO reservierung (userID, wunschUserID, prioritaet)
        VALUES ($encUserID, $wunschID, $encPrioritaet)");

if ($success) {
    header('Location: reservierung.php?success=' . urlencode($name));
    exit;
} else {
    header('Location: reservierung.php?error=dbError');
    exit;
}