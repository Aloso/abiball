<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

require_once '../markup.php';

include 'header.php';

echo '<h1>Seitentexte</h1>

<style>
    .pageTextTA {
        min-height: 180px;
        vertical-align:top;
        width: calc(100% - 210px);
    }

    @media (max-width: 647px) {
        .pageTextTA {
            width: 100%;
        }
    }
</style>';

if (isset($_POST['name']) && isset($_POST['htmlText'])) {
    $name = $_POST['name'];

    // Die Funktion markupToHtml ist selbstgeschrieben (siehe markup.php)

    $text = markupToHtml($_POST['htmlText']);

    // Für Datenbank vorbereiten:

    $encName = $mysqli->real_escape_string($name);
    $encText = $mysqli->real_escape_string($text);

    $success = $mysqli->query("UPDATE seitentexte SET htmlText = '$encText' WHERE name = '$encName'");
    if ($success) {
        echo '<div class="success message">Seitentext angepasst!</div>
        Vorschau:
        <div class="pageText">
            ' . $text . '
        </div><br><br>';
    } else {
        echo '<div class="error message">Fehler beim Anpassen des Seitentexts: ' . $mysqli->error . '</div>';
    }
}

$pageTexts = $mysqli->query("SELECT * FROM seitentexte");

while (($row = $pageTexts->fetch_assoc()) != null) {
    // Die Funktion markupToHtml ist selbstgeschrieben (siehe markup.php)

    $text = htmlToMarkup($row['htmlText']);

    echo '<form action="pageTexts.php" method="post">
        <label>
            <span class="labelText">' . $row['name'] . '</span>
            <textarea name="htmlText" class="pageTextTA">' . $text . '</textarea>
            <input type="hidden" name="name" value="' . $row['name'] . '">
        </label>
        <div style="height: 3px"></div>
        <span class="labelText"></span> <input type="submit" value="Speichern">
        <div style="height: 10px"></div>
    </form>';
}

echo '<br><br>
<h2>Formatierung</h2>
<p>Die Seitentexte können Überschriften, fetten und kursiven Text, Links, Listen und sogar Tabellen enthalten. So wird es eingegeben:</p>
<table>
    <tr><th>Eingabe</th><th>Output</th><th>Erklärung</th></tr>
    <tr>
        <td>####Überschrift</td><td><h1>Überschrift</h1></td><td>Danach Zeilenumbruch nicht vergessen!</td>
    </tr><tr>
        <td>###Mittlere Überschrift</td><td><h2>Mittlere Überschrift</h2></td><td>Danach Zeilenumbruch nicht vergessen!</td>
    </tr><tr>
        <td>##Kleine Überschrift</td><td><h3>Kleine Überschrift</h3></td><td>Danach Zeilenumbruch nicht vergessen!</td>
    </tr><tr>
        <td>**fetter Text**</td><td><b>fetter Text</b></td><td>davor muss ein Zeilenanfang oder Leerzeichen sein</td>
    </tr><tr>
        <td>__kursiver Text__</td><td><i>kursiver Text</i></td><td>davor muss ein Zeilenanfang oder Leerzeichen sein</td>
    </tr><tr>
        <td>{{a:http://www.google.com>>Google-<br>Website besuchen}}</td>
        <td><a href="http://www.google.com">Google-Website besuchen</a></td><td>Allgemein:<br>{{a:URL des Links>>Name des Links}}<br><b>Wichtig</b>: http:// oder https://</td>
    </tr><tr>
        <td>{{a:forum.php>>Forum}}</td><td><a href="../forum.php">Forum</a></td><td>Allgemein:<br>{{a:Dateiname>>Name des Links}}</td>
    </tr><tr>
        <td>@@<br>
* Punkt 1<br>
* Punkt 2<br>
mit __mehreren__<br>
Zeilen und **Formatierung**<br>
@@</td><td><ul><li>Punkt 1</li><li>Punkt 2<br>mit <i>mehreren</i><br>Zeilen und <b>Formatierung</b></li></ul></td>
        <td>Vor und nach dem @@ muss ein Zeilenumbruch sein.<br>Nach den Sternchen muss ein Leerzeichen sein.</td>
    </tr><tr>
        <td>++++<br>
++<br>
$ Feld 1<br>
$ Feld 2<br>
$ Feld 3<br>
++<br>
++<br>
$ Feld 4<br>
$ Feld 5 über<br>
mehrere Zeilen<br>
$ Feld 6<br>
++<br>
++++</td><td><table><tr><td>Feld 1</td><td>Feld 2</td><td>Feld 3</td></tr><tr><td>Feld 4</td><td>Feld 5 über<br>
mehrere Zeilen</td><td>Feld 6</td></tr></table></td><td>Eine Tabellenzeile besteht aus<br>
++<br>
[.........]<br>
++<br>
Jedes Feld beginnt mit einer neuen Zeile, einem $ (Dollar) und einem Leerzeichen.</td>
    </tr><tr>
        <td>{{meta:date}}</td><td><span class="date">' . date('d.m.Y') . '</span></td>
    </tr><tr>
        <td>{{meta:time}}</td><td><span class="time">' . date('H:i') . '</span></td>
    </tr><tr>
        <td>{{meta:webmasterMail}}</td><td><span class="webmasterMail">' . $meta['webmasterMail'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:pageName}}</td><td><span class="pageName">' . $meta['pageName'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:pageSubtitle}}</td><td><span class="pageSubtitle">' . $meta['pageSubtitle'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:googleMaps}}</td><td>' . $meta['googleMaps'] . '</td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:webmasterMail}}</td><td><span class="webmasterMail">' . $meta['webmasterMail'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:webmasterAdress}}</td><td><span class="webmasterAdress">' . $meta['webmasterAdress'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr>
    <tr>
        <td>{{meta:currentRound}}</td><td><span class="currentRound">' . $meta['currentRound'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:availableCards}}</td><td><span class="availableCards">' . $meta['availableCards'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:perUser}}</td><td><span class="perUser">' . ($meta['perUser'] ? 'Pro Nutzer' : 'Insgesamt') . '</span></td><td>Siehe "Einstellungen"<br>Mögliche Werte sind "Pro Nutzer" und "Insgesamt"</td>
    </tr><tr>
        <td>{{meta:preis}}</td><td><span class="preis">' . $meta['preis'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:zahlungsFrist}}</td><td><span class="zahlungsFrist">' . $meta['zahlungsFrist'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:kontoinhaber}}</td><td><span class="kontoinhaber">' . $meta['kontoinhaber'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr><tr>
        <td>{{meta:iban}}</td><td><span class="iban">' . $meta['iban'] . '</span></td><td>Siehe "Einstellungen"</td>
    </tr>
</table>';

include 'footer.php';
