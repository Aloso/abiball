<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

// Kurzinfo
$encID = $mysqli->real_escape_string($_SESSION['userID']);
$anz = $mysqli->query('SELECT COUNT(*) FROM bestellungen WHERE userID = ' . $encID);
$bestellungen = $anz->fetch_assoc()['COUNT(*)'];

if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch ($error) {
        case 'invalidRequest':
            $error = 'Die Bestellung ist fehlgeschlagen! Die Anfrage war fehlerhaft.';
            break;
        case 'orderingDisabled':
            $error = 'Die Bestellung ist fehlgeschlagen! Die Kartenbestellung ist deaktiviert.';
            break;
        case 'databaseError':
            $error = 'Die Bestellung ist fehlgeschlagen! Es liegt ein Problem in der Datenbank vor.
                    Bitte informieren Sie darüber den Webmaster: ' . $meta['webmasterMail'];
            break;
    
        case 'emptyString':
            $error = 'Die Bestellung ist fehlgeschlagen! Es muss ein Name eingegeben werden.';
            break;
        case 'shortString':
            $error = 'Die Bestellung ist fehlgeschlagen! Der angegebene Name ist zu kurz.';
            break;
        case 'longString':
            $error = 'Die Bestellung ist fehlgeschlagen! Der angegebene Name ist zu lang
                    (länger als 50 Zeichen).';
            break;
            
        case 'noCardsAvailablePerUser':
            $error = 'Die Bestellung ist fehlgeschlagen! Es sind keine Karten mehr verfügbar.';
            break;
        case 'noCardsAvailablePerAll':
            $error = 'Die Bestellung ist fehlgeschlagen! Es sind keine Karten mehr verfügbar.
                    Womöglich war jemand anderes schneller.';
            break;
        default:
            $error = 'Die Bestellung ist fehlgeschlagen!';
    }
    
    echo '<div class="error message"><b>Fehler:</b> ' . $error . '</div>';
}

if (isset($_GET['ordered'])) {
    $name = $_GET['ordered'];
    echo '<div class="message success">Eine Karte für ' . $name . ' wurde bestellt.</div>';
}


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
        $orderedInThisRound = $mysqli->query("SELECT COUNT(*) FROM bestellungen
                WHERE round = $meta[currentRound] AND userID = $userID");
        $orderedInThisRound = $orderedInThisRound->fetch_assoc()['COUNT(*)'];
        $anz = $meta['availableCards'] - $orderedInThisRound;
        
        if ($anz > 0) {
            
            $orderedTotal = $mysqli->query("SELECT COUNT(*) FROM bestellungen WHERE userID = $userID");
            $orderedTotal = $orderedTotal->fetch_assoc()['COUNT(*)'];
            
            if ($orderedTotal == '0') {
                $fullName = $vorname . ' ' . $nachname;
                
                $inputField = '<input type="text" value="' . $fullName . '" disabled
                style="cursor: not-allowed"
                title="Sie müssen zuerst eine Karte für sich selbst bestellen.">
                <input type="hidden" name="name" value="' . $fullName . '">';
            } else {
                $inputField = '<input type="text" name="name" value="">';
            }
            
            echo '<p>Sie dürfen in dieser Bestellrunde noch <b>' . $anz . ' Karten</b> bestellen.</p>
            
            <form action="bestellung3.php" method="post">
                <fieldset>
                    <p>Wer soll diese Karte erhalten?</p>
                    <label>
                        <span class="labelText">Vor- und Nachname:</span>
                        ' . $inputField . '
                    </label>
                    <div style="height:10px"></div>
                    <input type="submit" value="Karte für ' . $meta['preis'] . ' Euro bestellen">
                    <a class="button" href="bestellung.php">Abbrechen</a>
                    <div style="height: 10px"></div>
                    Warnung: Dies kann nicht rückgängig gemacht werden.
                </fieldset>
            </form><br><br>';
            
        } else {
            echo '<p>Sie dürfen in dieser Bestellrunde keine Karten mehr bestellen.</p>';
        }
        
    } else {
        $orderedInThisRound = $mysqli->query("SELECT COUNT(*) FROM bestellungen
                WHERE round = $meta[currentRound]");
        $orderedInThisRound = $orderedInThisRound->fetch_assoc()['COUNT(*)'];
        $anz = $meta['availableCards'] - $orderedInThisRound;
    
        if ($anz > 0) {
    
            $orderedTotal = $mysqli->query("SELECT COUNT(*) FROM bestellungen WHERE userID = $userID");
            $orderedTotal = $orderedTotal->fetch_assoc()['COUNT(*)'];
    
            if ($orderedTotal == '0') {
                $fullName = $vorname . ' ' . $nachname;
        
                $inputField = '<input type="text" value="' . $fullName . '" disabled
                style="cursor: not-allowed"
                title="Sie müssen zuerst eine Karte für sich selbst bestellen.">
                <input type="hidden" name="name" value="' . $fullName . '">';
            } else {
                $inputField = '<input type="text" name="name" value="">';
            }
        
            echo '<p>In dieser Bestellrunde sind noch <b>' . $anz . ' Karten</b> verfügbar.</p>
            
            <form action="bestellung3.php" method="post">
                <fieldset>
                    <p>Wer soll diese Karte erhalten?</p>
                    <label>
                        <span class="labelText">Vor- und Nachname:</span>
                        ' . $inputField . '
                    </label>
                    <div style="height:10px"></div>
                    <input type="submit" value="Karte für ' . $meta['preis'] . ' Euro bestellen">
                    <a class="button" href="bestellung.php">Abbrechen</a>
                    <div style="height: 10px"></div>
                    Warnung: Dies kann nicht rückgängig gemacht werden.
                </fieldset>
            </form><br><br>';
        
        } else {
            echo '<p>Sie dürfen in dieser Bestellrunde keine Karten mehr bestellen.</p>';
        }
    }
    
}

echo '</div>';
include '_part2.inc.php';