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
        <th><a href="?sortby=u.id">User<br>ID</a></th>
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

if (isset($_POST['loeschen'])) {
    $id = $mysqli->real_escape_string($_POST['loeschen']);
    $timeNow = time();
    
    $success = $mysqli->query("DELETE FROM bestellungen WHERE id = $id");
    if ($success) {
        echo '<p>Die Bestellung wurde gelöscht.</p>';
    } else {
        echo '<p><b>Fehler</b> beim Löschen der Bestellung!</p>';
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
            $sort =  ' ORDER BY ' . $sortby;
            break;
        case 'b.preis':
        case 'b.bestelltAm':
        case 'b.bezahltAm':
            $sort =  ' ORDER BY ' . $sortby . ' DESC';
    }
}

$alleBestellungen = $mysqli->query('SELECT u.id, u.vorname, u.nachname, b.* FROM bestellungen b LEFT JOIN user u ON b.userID = u.id' . $sort);
if ($alleBestellungen === false) {
    echo '<p><b>Fehler:</b> ' . $mysqli->error . '</p>';
} else {
    
    while (($row = $alleBestellungen->fetch_assoc()) != null) {
        $bezahlt = $row['bezahlt'] ? 'Bezahlt' : '';
        $bezahltAm = $row['bezahltAm'] == null ? '' : date('d.m.y H:i', $row['bezahltAm']);
        
        $bestelltAm = date('d.m.y H:i', $row['bestelltAm']);
        
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
            echo "<form action='bestelluebersicht.php' method='post' style='display:inline'>
                <input type='hidden' name='zahlungRegistrieren' value='$row[id]'>
                <input type='submit' value='Zahlung registrieren'>
            </form>
            <form action='mahnung.php' method='post' style='display:inline'>
                <input type='hidden' name='mahnen' value='$row[userID]'>
                <input type='submit' value='Mahnung versenden' class='secondary'>
            </form><form action='bestelluebersicht.php' method='post' style='display:inline'>
                <input type='hidden' name='loeschen' value='$row[id]'>
                <input type='submit' value='Bestellung löschen' class='secondary'>
            </form>";
        }
        
        echo "</td></tr>";
    }
    
}


echo '</table>
<style>
    .popup {
        position: fixed;
        left: 50%;
        top: 100px;
        width: 280px;
        padding: 20px;
        margin-left: -160px;
        background-color: white;
        box-shadow: 0 0 150px rgba(0, 0, 0, 0.6);
    }
    .popup h2 {
        margin: 0 0 1em 0;
    }
    .popup input[type=submit] {
        display: inline-block;
        text-decoration: none;
        border-radius: 3px;
        padding: 4px 8px;
        font-size: 95%;
        font-family: Roboto, sans-serif;
        outline-offset: -5px;
        border: 1px solid #0063e6;
        background-color: rgb(49, 127, 255);
        color: white;
        margin-bottom: 1em;
    }
    .popup input[type=submit].secondary {
        border: 1px solid #aaaaaa;
        background-color: rgb(255, 255, 255);
        color: black;
    }
    a.button {
        cursor: pointer;
    }
</style>
<script type="text/javascript">

var openTds = [];

var lastTDs = document.querySelectorAll("td:last-child");
for (var i = 0; i < lastTDs.length; i++) {
    var td = lastTDs[i];
    if (td.children[0] && td.children[0].nodeName.toLowerCase() === "form") {
        var newDiv = document.createElement("div");
        newDiv.className = "popup";
        newDiv.innerHTML = "<h2>Bestellungsoptionen</h2>";
        while (td.children[0]) {
            newDiv.appendChild(td.children[0]);
            newDiv.appendChild(document.createElement("br"));
        }
        td.innerHTML = "";
        var text = document.createElement("p");
        text.textContent = "Die Mahnung gilt für alle Bestellungen der Person.";
        newDiv.appendChild(text);
        var button = document.createElement("a");
        button.className = "button primary";
        button.style.padding = "1px 7px";
        button.innerHTML = "Optionen";
        button.onclick = function (e) {
            document.body.appendChild(this._popup);
            openTds.push(this._popup);
            e.stopPropagation();
        };
        button._popup = newDiv;
        td.appendChild(button);
        var button2 = document.createElement("a");
        button2.className = "button primary";
        button2.innerHTML = "Zurück";
        button2.onclick = function () {
            document.body.removeChild(this.parentNode);
            openTds.pop();
        };
        newDiv.appendChild(button2);
        newDiv.onclick = function (e) {
            e.stopPropagation();
        }
    }
}

document.body.onclick = function (e) {
    if (openTds[0]) {
        document.body.removeChild(openTds.pop());
    }
}

</script>';

include 'footer.php';