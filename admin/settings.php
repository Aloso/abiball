<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

include 'header.php';

$changed = false;

if (isset($_POST['pageName'])) {
    $pageName = $_POST['pageName'];
    $pageName = $mysqli->real_escape_string($pageName);
    $success = $mysqli->query("UPDATE meta SET pageName = '$pageName'");
    
    if ($success) {
        $meta['pageName'] = $pageName;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten des Seitennamens!</b></p>';
}

if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $url = $mysqli->real_escape_string($url);
    $success = $mysqli->query("UPDATE meta SET url = '$url'");
    
    if ($success) {
        $meta['url'] = $url;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der URL!</b></p>';
} else if ($meta['url'] == '') {
    $arr = explode('admin/settings.php', $_SERVER['REQUEST_URI']);
    $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . $arr[0];
    $url = $mysqli->real_escape_string($url);
    $success = $mysqli->query("UPDATE meta SET url = '$url'");
    
    if ($success) {
        $meta['url'] = $url;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der URL!</b></p>';
}

if (isset($_POST['pageSubtitle'])) {
    $pageSubtitle = $_POST['pageSubtitle'];
    $pageSubtitle = $mysqli->real_escape_string($pageSubtitle);
    $success = $mysqli->query("UPDATE meta SET pageSubtitle = '$pageSubtitle'");
    
    if ($success) {
        $meta['pageSubtitle'] = $pageSubtitle;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten des Seitenuntertitels!</b></p>';
}

if (isset($_POST['googleMaps'])) {
    $googleMaps = $_POST['googleMaps'];
    $encGoogleMaps = $mysqli->real_escape_string($googleMaps);
    $success = $mysqli->query("UPDATE meta SET googleMaps = '$encGoogleMaps'");
    
    if ($success) {
        $meta['googleMaps'] = $googleMaps;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten des Google Maps Embed Links!</b></p>';
}

if (isset($_POST['webmasterMail'])) {
    $webmasterMail = $_POST['webmasterMail'];
    $webmasterMail = $mysqli->real_escape_string($webmasterMail);
    $success = $mysqli->query("UPDATE meta SET webmasterMail = '$webmasterMail'");
    
    if ($success) {
        $meta['webmasterMail'] = $webmasterMail;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Webmaster E-Mailadresse!</b></p>';
}

if (isset($_POST['webmasterAdress'])) {
    $webmasterAdress = $_POST['webmasterAdress'];
    $webmasterAdress = str_replace("\r", '', $webmasterAdress);
    $webmasterAdress = str_replace("\n", '<br>', $webmasterAdress);
    $webmasterAdress = $mysqli->real_escape_string($webmasterAdress);
    $success = $mysqli->query("UPDATE meta SET webmasterAdress = '$webmasterAdress'");
    
    if ($success) {
        $meta['webmasterAdress'] = $webmasterAdress;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Webmaster Post-Adresse!</b></p>';
}

if (isset($_POST['loginTimeout'])) {
    $loginTimeout = $_POST['loginTimeout'];
    $loginTimeout = $mysqli->real_escape_string($loginTimeout);
    $success = $mysqli->query("UPDATE meta SET loginTimeout = $loginTimeout");
    
    if ($success) {
        $meta['loginTimeout'] = $loginTimeout;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten des Session-Timeouts!</b></p>';
}

echo '<h1>Einstellungen</h1>

<h2>Allgemeines</h2>
<form action="settings.php" method="post">
    <label>
        <span class="labelText">URL:</span>
        <input type="text" name="url" value="' . $meta['url'] . '"><br>
        <span class="labelText"></span> URL der Startseite mit http:// oder https://<br>
        <span class="labelText"></span> und einem Slash am Ende.<br>
        <span class="labelText"></span> Sollte bereits korrekt eingetragen sein!
    </label>
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Name der Seite:</span>
        <input type="text" name="pageName" value="' . $meta['pageName'] . '"><br>
        <span class="labelText"></span> Erscheint in der Überschrift und im Tab<br>
        <span class="labelText"></span> So kurz wie möglich wählen, da auf der mobilen<br>
        <span class="labelText"></span> Seite wenig Platz für die Überschrift ist
    </label>
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Untertitel:</span>
        <input type="text" name="pageSubtitle" value="' . $meta['pageSubtitle'] . '"><br>
        <span class="labelText"></span> Erscheint unter der Überschrift
    </label>
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Google Maps Link:</span>
        <textarea name="googleMaps" style="vertical-align:top;font-family:Consolas">' . str_replace('"', '&quot;', $meta['googleMaps']) . '</textarea>
    </label><br>
        <span class="labelText"></span> Erscheint unter "Location".<br>
        <span class="labelText"></span> Eine Anleitung, wie du den Link erzeugst,<br>
        <span class="labelText"></span> findest du <a class="button primary" href="https://support.google.com/maps/answer/144361?co=GENIE.Platform%3DDesktop&hl=de" target="_blank">hier</a>. Verwende die<br>
        <span class="labelText"></span> <b>mittelgroße Karte</b>. Ersetze danach<br>
        <span class="labelText"></span> <span style="font-family:Consolas;color:red">width="600"</span> durch <span style="font-family:Consolas;color:red">style="width:100%"</span>
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Webmaster E-Mail:</span>
        <input type="email" name="webmasterMail" value="' . $meta['webmasterMail'] . '"><br>
        <span class="labelText"></span> Erscheint unter anderem im Impressum
    </label>
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Webmaster Adresse:</span>
        <textarea name="webmasterAdress" style="vertical-align: top">' . str_replace('<br>', "\n", $meta['webmasterAdress']) . '</textarea><br>
        <span class="labelText"></span> Erscheint im Impressum
    </label>
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Session-Timeout:</span>
        <input type="text" name="loginTimeout" value="' . $meta['loginTimeout'] . '"><br>
        <span class="labelText"></span> ' . $meta['loginTimeout'] . ' Sekunden = ' . ($meta['loginTimeout'] / 60) . ' Minuten = ' . ($meta['loginTimeout'] / 3600) . ' Stunden
        <br>
        <span class="labelText"></span> Sekunden, bis man automatisch ausgeloggt wird,<br>
        <span class="labelText"></span> weil man zu lange inaktiv war
    </label>
    <div style="height: 10px"></div>
    <input type="submit" value="Speichern">
</form>';






if (isset($_POST['currentRound'])) {
    $currentRound = $_POST['currentRound'];
    $currentRound = $mysqli->real_escape_string($currentRound);
    $success = $mysqli->query("UPDATE meta SET currentRound = $currentRound");
    
    if ($success) {
        $meta['currentRound'] = $currentRound;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Bestellrunde!</b></p>';
}

if (isset($_POST['availableCards'])) {
    $availableCards = $_POST['availableCards'];
    $availableCards = $mysqli->real_escape_string($availableCards);
    $success = $mysqli->query("UPDATE meta SET availableCards = $availableCards");
    
    if ($success) {
        $meta['availableCards'] = $availableCards;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Kartenzahl!</b></p>';
}

if (isset($_POST['perUser'])) {
    $perUser = $_POST['perUser'];
    $perUser = $mysqli->real_escape_string($perUser);
    $success = $mysqli->query("UPDATE meta SET perUser = $perUser");
    
    if ($success) {
        $meta['perUser'] = $perUser;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Auswahl (Pro Nutzer / Insgesamt)!</b></p>';
}

if (isset($_POST['preis'])) {
    $preis = $_POST['preis'];
    $preis = $mysqli->real_escape_string($preis);
    $success = $mysqli->query("UPDATE meta SET preis = $preis");
    
    if ($success) {
        $meta['preis'] = $preis;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten des Einzelpreises!</b></p>';
}

if (isset($_POST['zahlungsFrist'])) {
    $zahlungsFrist = $_POST['zahlungsFrist'];
    $zahlungsFrist = $mysqli->real_escape_string($zahlungsFrist);
    $success = $mysqli->query("UPDATE meta SET zahlungsFrist = '$zahlungsFrist'");
    
    if ($success) {
        $meta['zahlungsFrist'] = $zahlungsFrist;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Zahlungsfrist!</b></p>';
}

if (isset($_POST['kontoinhaber'])) {
    $kontoinhaber = $_POST['kontoinhaber'];
    $kontoinhaber = $mysqli->real_escape_string($kontoinhaber);
    $success = $mysqli->query("UPDATE meta SET kontoinhaber = '$kontoinhaber'");
    
    if ($success) {
        $meta['kontoinhaber'] = $kontoinhaber;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten des Kontoinhabers!</b></p>';
}

if (isset($_POST['iban'])) {
    $iban = $_POST['iban'];
    $iban = $mysqli->real_escape_string($iban);
    $success = $mysqli->query("UPDATE meta SET iban = '$iban'");
    
    if ($success) {
        $meta['iban'] = $iban;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der IBAN!</b></p>';
}

if (isset($_POST['bic'])) {
    $bic = $_POST['bic'];
    $bic = $mysqli->real_escape_string($bic);
    $success = $mysqli->query("UPDATE meta SET bic = '$bic'");
    
    if ($success) {
        $meta['bic'] = $bic;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der BIC!</b></p>';
}

if (isset($_POST['kontonr'])) {
    $kontonr = $_POST['kontonr'];
    $kontonr = $mysqli->real_escape_string($kontonr);
    $success = $mysqli->query("UPDATE meta SET kontonr = '$kontonr'");
    
    if ($success) {
        $meta['kontonr'] = $kontonr;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Kontonummer!</b></p>';
}

if (isset($_POST['blz'])) {
    $blz = $_POST['blz'];
    $blz = $mysqli->real_escape_string($blz);
    $success = $mysqli->query("UPDATE meta SET blz = '$blz'");
    
    if ($success) {
        $meta['blz'] = $blz;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Bankleitzahl!</b></p>';
}


$perUser = $meta['perUser'] == 1 ? 'checked' : '';
$notPerUser = $meta['perUser'] == 0 ? 'checked' : '';

echo '<h2>Kartenbestellung</h2>
<form action="settings.php" method="post">
    <label>
        <span class="labelText">Aktuelle Bestellrunde:</span>
        <input type="text" name="currentRound" value="' . $meta['currentRound'] . '"><br>
        <span class="labelText"></span> Setze den Wert auf 0, um die Bestellung zu deaktivieren.<br>
        <span class="labelText"></span> Während einer Bestellrunde darf keiner der folgenden<br>
        <span class="labelText"></span> Werte verändert werden!
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Verfügbare Karten:</span>
        <input type="text" name="availableCards" value="' . $meta['availableCards'] . '">
    </label><br>
    <span class="labelText"></span><label><input type="radio" name="perUser" value="1" ' . $perUser . '> Pro Nutzer</label><br>
    <span class="labelText"></span><label><input type="radio" name="perUser" value="0" ' . $notPerUser . '> Insgesamt</label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Preis pro Karte:</span>
        <input type="text" name="preis" value="' . $meta['preis'] . '"> Euro<br>
        <span class="labelText"></span> Bei Kommazahlen bitte Punkt statt Komma verwenden
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Zahlungsfrist:</span>
        <input type="text" name="zahlungsFrist" value="' . $meta['zahlungsFrist'] . '"><br>
        <span class="labelText"></span> Frist, bis zu der alle Überweisungen eingegangen sein<br>
        <span class="labelText"></span> sollen (z.B. 1 Woche vor dem Abiball)
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Kontoinhaber:</span>
        <input type="text" name="kontoinhaber" value="' . $meta['kontoinhaber'] . '"><br>
        <span class="labelText"></span> Name der Person, auf dessen Konto überwiesen werden soll
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">IBAN:</span>
        <input type="text" name="iban" value="' . $meta['iban'] . '"><br>
        <span class="labelText"></span> IBAN des Kontos, auf das überwiesen werden soll
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">BIC:</span>
        <input type="text" name="bic" value="' . $meta['bic'] . '"><br>
        <span class="labelText"></span> BIC des Kontos, auf das überwiesen werden soll
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Kontonummer:</span>
        <input type="text" name="kontonr" value="' . $meta['kontonr'] . '"><br>
        <span class="labelText"></span> Kontonummer des Kontos, auf das überwiesen werden soll
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">BLZ:</span>
        <input type="text" name="blz" value="' . $meta['blz'] . '"><br>
        <span class="labelText"></span> Bankleitzahl des Kontos, auf das überwiesen werden soll
    </label>
    
    <div style="height: 10px"></div>
    <input type="submit" value="Speichern">
</form>';




if (isset($_POST['reservierungAktiviert'])) {
    $reservierungAktiviert = $_POST['reservierungAktiviert'];
    $reservierungAktiviert = $mysqli->real_escape_string($reservierungAktiviert);
    $success = $mysqli->query("UPDATE meta SET reservierungAktiviert = '$reservierungAktiviert'");
    
    if ($success) {
        $meta['reservierungAktiviert'] = $reservierungAktiviert;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Aktivierung der Sitzplanfunktion!</b></p>';
}

if (isset($_POST['reservierungsPunkte'])) {
    $reservierungsPunkte = $_POST['reservierungsPunkte'];
    $reservierungsPunkte = $mysqli->real_escape_string($reservierungsPunkte);
    $success = $mysqli->query("UPDATE meta SET reservierungsPunkte = '$reservierungsPunkte'");
    
    if ($success) {
        $meta['reservierungsPunkte'] = $reservierungsPunkte;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der Punkte für Sitzplatzwünsche</b></p>';
}

if (isset($_POST['maxReservierungen'])) {
    $maxReservierungen = $_POST['maxReservierungen'];
    $maxReservierungen = $mysqli->real_escape_string($maxReservierungen);
    $success = $mysqli->query("UPDATE meta SET maxReservierungen = '$maxReservierungen'");
    
    if ($success) {
        $meta['maxReservierungen'] = $maxReservierungen;
        $changed = true;
    }
    else echo '<p><b>Fehler beim Updaten der maximalen Anzahl der Sitzplatzwünsche</b></p>';
}

echo '<h2>Sitzplätze</h2>
<form action="settings.php" method="post">
    <label>
        <span class="labelText">Freigeschaltet:</span>
        <input type="text" name="reservierungAktiviert" value="' . $meta['reservierungAktiviert'] . '"><br>
        <span class="labelText"></span> 1 = ja, 0 = nein
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Punkte:</span>
        <input type="text" name="reservierungsPunkte" value="' . $meta['reservierungsPunkte'] . '"><br>
        <span class="labelText"></span> User können diese Anzahl Punkte für Wünsche vergeben
    </label>
    
    <div style="height: 10px"></div>
    <label>
        <span class="labelText">Wünsche möglich:</span>
        <input type="text" name="maxReservierungen" value="' . $meta['maxReservierungen'] . '"><br>
        <span class="labelText"></span> Je höher die Zahl, desto schwieriger wird die Einteilung!
    </label>
    
    <div style="height: 10px"></div>
    <input type="submit" value="Speichern">
</form>';


if ($changed) {
    
    // Alle Seitentexte müssen geupdatet werden
    
    require_once '../markup.php';
    
    $failure = false;
    
    $pageTexts = $mysqli->query("SELECT * FROM seitentexte");
    while (($row = $pageTexts->fetch_assoc()) != null) {
        
        // Durch die Übersetzung in Markup und die Rückübersetzung werden die Variablen
        // neu eingefügt. Veränderte Werte werden also übernommen.
        
        $oldText = htmlToMarkup($row['htmlText']);
        $newText = markupToHtml($oldText);
        $encText = $mysqli->real_escape_string($newText);
        $encName = $mysqli->real_escape_string($row['name']);
        
        $success = $mysqli->query("UPDATE seitentexte SET htmlText = '$encText' WHERE name = '$encName'");
        if (!$success) {
            $failure = true;
            echo '<p>Fehler beim Updaten des Seitentextes ' . $row['name'] . '!</p>';
        }
    }
    
    if (!$failure) {
        echo '<p>Alle Seitentexte wurden aktualisiert.</p>';
    }
}


include 'footer.php';