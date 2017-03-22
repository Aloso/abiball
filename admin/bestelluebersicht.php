<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

include 'header.php';

echo '<h1>Bestellübersicht</h1>

<p>Klicke auf einen Spaltennamen, um die Tabelle danach zu sortieren.</p>
<style>
    .redBg {
        background-color: red;
    }
</style>
<table>
    <tr>
        <th><a href="?sortby=b.id">ID</a></th>
        <th><a href="?sortby=b.round">Runde</a></th>
        <th><a href="?sortby=u.id">User-ID</a></th>
        <th><a href="?sortby=u.nachname">User</a></th>
        <th><a href="?sortby=b.name">Inh. der Karte</a></th>
        <th><a href="?sortby=b.bezahlt">Bezahlt?</a></th>
        <th><a href="?sortby=b.bestelltAm">Bestellt am</a></th>
        <th><a href="?sortby=b.bezahltAm">Bezahlt am</a></th>
        <th><a href="?sortby=b.preis">Preis</a></th>
        <th></th>
    </tr>';

if (isset($_POST['zahlungRegistrieren'])) {
    $id = $mysqli->real_escape_string($_POST['zahlungRegistrieren']);
    $timeNow = time();
    
    $success = $mysqli->query("UPDATE bestellungen SET bezahlt = 1, bezahltAm = $timeNow
            WHERE id = $id");
    if ($success) {
        echo '<p>Die Zahlung wurde registriert.</p>';
    } else {
        echo '<p><b>Fehler</b> beim Registrieren der Zahlung!</p>';
    }
}

$sort = '';
if (isset($_GET['sortby'])) {
    $sortby = $_GET['sortby'];
    switch ($sortby) {
        case 'b.id':
        case 'b.round':
        case 'u.id':
        case 'u.nachname':
        case 'b.name':
        case 'b.bezahlt':
        case 'b.status':
            $sort =  'ORDER BY ' . $sortby;
            break;
        case 'b.preis':
        case 'b.bestelltAm':
        case 'b.bezahltAm':
            $sort =  'ORDER BY ' . $sortby . ' DESC';
    }
}

$alleBestellungen = $mysqli->query('SELECT u.id, u.vorname, u.nachname, b.* FROM bestellungen b LEFT JOIN user u ON b.userID = u.id ' . $sort);
if ($alleBestellungen === false) {
    echo '<p><b>Fehler:</b> ' . $mysqli->error . '</p>';
} else {
    
    while (($row = $alleBestellungen->fetch_assoc()) != null) {
        $bezahlt = $row['bezahlt'] ? 'Bezahlt' : '';
        $bezahltAm = $row['bezahltAm'] == null ? '' : date('d.m.Y H:i', $row['bezahltAm']);
        
        $bestelltAm = date('d.m.Y H:i', $row['bestelltAm']);
        
        echo "<tr>
            <td>{$row['id']}</td>
            <td>$row[round]</td>
            <td " . ($row['nachname'] == null ? 'class="redBg" title="Dieser Nutzer existiert nicht"' : '') . ">$row[userID]</td>
            <td>$row[vorname] $row[nachname]</td>
            <td>$row[name]</td>
            <td>$bezahlt</td>
            <td>$bestelltAm</td>
            <td>$bezahltAm</td>
            <td>$row[preis] &euro;</td>
            <td>";
        
        if ($bezahlt == '') {
            echo "<form action='bestelluebersicht.php' method='post'>
                <input type='hidden' name='zahlungRegistrieren' value='$row[id]'>
                <input type='submit' value='Zahlung registr.' style='padding: 1px 8px'>
            </form>";
        }
        
        echo '</td></tr>';
    }
    
}


echo '</table>';

include 'footer.php';