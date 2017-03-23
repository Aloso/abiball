<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

include 'header.php';

if (isset($_POST['mahnen'])) {
    $id = $mysqli->real_escape_string($_POST['mahnen']);

    $bestellungen = $mysqli->query("SELECT b.*, u.email, u.vorname, u.nachname, u.status, u.lastActive
            FROM user u JOIN bestellungen b ON u.id = b.userID WHERE b.userID = $id AND bezahlt = 0");
    if ($bestellungen->num_rows > 0) {

        $nummern = '';
        $lastRow = null;
        while (($row = $bestellungen->fetch_assoc()) != null) {
            $nummern .= ', ' . $row['id'];
            $lastRow = $row;
        }
        $nummern = substr($nummern, 2);

        echo "<h1>Mahnung an $lastRow[vorname] $lastRow[nachname]</h1>";

        require_once '../mailtemplate.inc.php';

        $now = date('d.m.Y H:i');

        $heading = '<h1 style="margin-top: 0; font-size: 30px">Mahnung</h1>';

        $text = "Sehr geehrte/r Herr/Frau $lastRow[vorname] $lastRow[nachname],

Sie haben bislang immer noch nicht Ihre Karten für die Bestellungen $nummern gezahlt.
Bitte holen Sie dies bald möglichst nach!

Bei längerem Versäumen kann Ihre Bestellung gelöscht werden.

Mit freundlichen Grüßen,
$meta[kontoinhaber]


----
$now
$meta[pageName]";

        $htmlBody = preg_replace("~\r?\n?----\r?\n?~", '<hr style="border-top:none; border-left:none; border-right:none; border-bottom: 1px solid #aaaaaa;">', $text);
        $htmlBody = preg_replace("~(\r\n|\r|\n)~", '<br>', $htmlBody);
        $htmlBody = $heading . $htmlBody;

        $text = 'Mahnung

' . $text;

        echo $htmlBody;

        require_once '../mailtemplate.inc.php';

        if (phpmailerSend($lastRow['email'], 'Mahnung', $htmlBody, $text)) {
            echo '<p>Erfolgreich verschickt!</p>
            <a href="bestelluebersicht.php" class="button primary">Zurück</a>';
        } else {
            echo '<p class="errorP">Fehler beim Senden!</p>
            <a href="bestelluebersicht.php">Zurück</a>';
        }
    } else {
        echo '<h1>Alle Rechnungen sind bereits bezahlt</h1>
        <a href="bestelluebersicht.php">Zurück</a>';
    }
}



include 'footer.php';
