<?php

session_start();
require_once 'resources/settings.inc.php';
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
}



echo '<h1>Kartenbestellung</h1>';

if ($meta['currentRound'] == 0) {
    echo '<p>
        Die Kartenbestellung ist momentan deaktiviert.
        Aktuelle Informationen zur Kartenbestellung finden Sie auf der Startseite.
    </p>';
} else {
    
    if ($meta['perUser'] == 1) {
        echo '<p>
            Momentan läuft die ' . $meta['currentRound'] . '. Bestellrunde. Hierbei darf jeder
            Nutzer bis zu ' . $meta['availableCards'] . ' Karten bestellen.
        </p>';
    
        $orderedInThisRound = $mysqli->query("SELECT COUNT(*) FROM bestellungen
                WHERE round = $meta[currentRound] AND userID = $userID");
        $anz = $meta['availableCards'] - $orderedInThisRound->fetch_assoc()['COUNT(*)'];
        
        if ($anz > 0) {
            
            echo '<p>Sie dürfen in dieser Bestellrunde noch <b>' . $anz . ' Karten</b> bestellen.</p>';
    
            $data = $mysqli->query("SELECT * FROM seitentexte WHERE name = 'Bestellung'");
            if (($row = $data->fetch_assoc()) != null) {
                echo '<div class="pageText">' . $row['htmlText'] . '</div>';
            }
    
            echo '<form action="bestellung2.php">
                <input type="submit" value="Akzeptieren und fortfahren">
            </form><br><br>';
            
        } else {
            echo '<p>Sie dürfen in dieser Bestellrunde keine Karten mehr bestellen.</p>';
        }
        
    } else {
        $orderedInThisRound = $mysqli->query("SELECT COUNT(*) FROM bestellungen
                WHERE round = $meta[currentRound]");
        $anz = $meta['availableCards'] - $orderedInThisRound->fetch_assoc()['COUNT(*)'];
        
        if ($anz > 0) {
            echo '<p>
                Momentan läuft die ' . $meta['currentRound'] . '. Bestellrunde. Hierbei dürfen
                so lange Karten bestellt werden, bis der Vorrat aufgebraucht ist.
            </p><p>
                Es sind noch <b>' . $anz . ' Karten</b> übrig.
            </p>';
    
            $data = $mysqli->query("SELECT * FROM seitentexte WHERE name = 'Bestellung'");
            if (($row = $data->fetch_assoc()) != null) {
                echo '<div class="pageText">' . $row['htmlText'] . '</div>';
            }
            
            echo '<form action="bestellung2.php">
                <input type="submit" value="Akzeptieren und fortfahren">
            </form><br><br>';
            
        } else {
            echo '<p>
                Momentan läuft die ' . $meta['currentRound'] . '. Bestellrunde.
            </p><p>
                <b>Es sind keine Karten mehr verfügbar.</b>
            </p><p>
                Vielleicht bekommen Sie in der nächsten Bestellrunde noch Karten.
                Aktuelle Informationen finden Sie auf der Startseite.
            </p>';
        }
    }
    
}

echo '</div>';
include '_part2.inc.php';