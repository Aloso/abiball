<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

// Zahlungserinnerung
$data = $mysqli->query('SELECT SUM(preis) FROM bestellungen WHERE bezahlt = FALSE AND userID = ' . $userID);

$unbezahlteBestellungen = $data->fetch_assoc()['SUM(preis)'];
if ($unbezahlteBestellungen != '0' && $unbezahlteBestellungen != null) {
    echo '<p>
        Sie haben noch einen offenen Betrag von <b style="color: #c30200; text-shadow: 0 0 3px white">' . $unbezahlteBestellungen . ' Euro</b>.<br>
        <span style="font-size:90%">(Falls Sie den Betrag bereits überwiesen haben, kann es eine Weile dauern, bis das hier sichtbar wird)</span>
    </p>';
}

echo '<h1>Profil</h1>
<p><b>Name:</b> ' . $vorname . ' ' . $nachname . '</p>
<p><b>E-Mailadresse:</b> ' . $email . '</p>
<p><a href="changeMail.php">E-Mailadresse ändern</a> &nbsp; <a href="changePassword.php">Passwort ändern</a></p>';

echo '<h2>Bestellte Karten</h2>';

$data = $mysqli->query("SELECT * FROM bestellungen WHERE userID = $userID ORDER BY bezahlt");
if ($data->num_rows == 0) {
    echo '<p>Keine Bestellungen gefunden.</p>';
} else {
    echo '<table>
    <tr><th>Name</th><th>Preis</th><th>Bestellt am</th><th>Bezahlt</th><th></th></tr>';

    while (($row = $data->fetch_assoc()) != null) {
        $bestelltAm = date("d. m. Y  H:m", $row['bestelltAm']);

        echo '<tr>
            <td>' . $row['name'] . '</td>
            <td>' . $row['preis'] . ' Euro</td>
            <td>' . $bestelltAm . ' Uhr</td>
            <td>' . ($row['bezahlt'] ? 'Ja' : 'Nein') . '</td>
            <td>
                <form action="karteUebertragen.php" method="post">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <input type="submit" value="Karte übertragen" title="Karte auf andere Person übertragen">
                </form>
            </td>
        </tr>';
    }

    echo '</table><br><br>
    <a href="rechnung.php" class="button primary" target="_blank">Rechnung als PDF-Dokument speichern</a><br><br>';

}

echo '</div>';
include '_part2.inc.php';
