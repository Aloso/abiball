<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">
<h1>Sitzplätze</h1>';

$encUserID = $mysqli->real_escape_string($userID);

$eigeneKarten = $mysqli->query("SELECT COUNT(*) FROM bestellungen WHERE userID = $encUserID");
if ($eigeneKarten->num_rows == 0) {
    $error = 'Fehler bei einer Datenbankabfrage';
    include 'error_message.inc.php';
}
$eigeneKarten = intval($eigeneKarten->fetch_assoc()['COUNT(*)']);

if (isset($_POST['delete'])) {
    $id = intval($_POST['delete']);
    if ($id != 0) {
        $success = $mysqli->query("DELETE FROM reservierung WHERE rID = $id");
        if ($success) {
            echo '<p class="message">Eintrag wurde gelöscht.</p>
            <script>
               setTimeout(function() {
                 window.location.href = window.location.href;
               }, 1700);
            </script>';
        } else {
            echo '<p class="errorP"><b>Fehler:</b> Eintrag konnte nicht gelöscht werden.</p>';
        }
    }
}

if (isset($_GET['error'])) {
    $errormessage = $_GET['error'];
    switch ($errormessage) {
        case 'disabled':
            $errormessage = 'Die Sitzplatzfunktion ist deaktiviert.';
            break;
        case 'emptyString':
            $errormessage = 'Bitte füllen Sie alle Felder aus!';
            break;
        case 'boughtNoCards':
            $errormessage = 'Sie können sich nur Sitzplätze wünschen, wenn Sie Karten bestellt haben.';
            break;
        case 'selbst':
            $errormessage = 'Ihr eigener Name ist nicht erlaubt.';
            break;
        case 'notparticipating':
            $errormessage = 'Diese Person ist nicht Abiturient/in oder hat bisher keine Karten für den Abiball bestellt.';
            break;
        case 'schonVorhanden':
            $errormessage = 'Für diese Person haben Sie schon einen Wunsch geäußert.<br>
            Löschen Sie den Wunsch zuerst, wenn Sie ihn ändern wollen.';
            break;
        case 'reservierungenAufgebraucht':
            $errormessage = 'Sie haben Ihre Wünsche aufgebraucht.';
            break;
        case 'punkteAufgebraucht':
            $errormessage = 'Sie haben Ihre Punkte aufgebraucht.';
            break;
        case 'zuVielePunkteAngegeben':
            $errormessage = 'Sie haben dafür nicht genügend Punkte übrig.';
            break;
        case 'dbError':
            $errormessage = 'Es liegt ein Problem in der Datenbank vor. Bitte informieren Sie den Webmaster.';
            break;
    }
    echo '<p class="errorP"><b>Fehler:</b> ' . $errormessage . '</p>';
}

if (isset($_GET['success'])) {
    $success = $_GET['success'];
    echo "<p class='message'>$success wurde gespeichert.</p>";
    echo '<script>
       setTimeout(function() {
         window.location.href = window.location.href.split("?")[0];
       }, 1700);
    </script>';
}

if ($eigeneKarten > 0) {
    
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
    
    if ($data->num_rows > 0) {
        echo '
        <table>
            <tr><th>Abiturient</th><th style="cursor:help" title="Anzahl der Karten, die die Person bestellt hat
(die eigene Karten inbegriffen)">Karten bestellt</th><th>Punkte vergeben</th><th></th></tr>';
            
            while (($row = $data->fetch_assoc()) != null) {
                $availablePoints -= $row['prioritaet'];
    
                $bullets = '';
                $rowPunkte = intval($row['prioritaet']);
                while ($rowPunkte--) {
                    $bullets .= '● &nbsp; ';
                }
    
                echo '<tr>
                <td>' . $row['vn'] . ' ' . $row['nn'] . '</td>
                <td>' . $row['personenAnz'] . '</td>
                <td>' . $bullets . '</td>
                <td><form action="reservierung.php" method="post">
                    <input type="hidden" name="delete" value="' . $row['id'] . '">
                    <input type="submit" value="Löschen" style="padding: 1px 6px">
                </form></td>
            </tr>';
            }
            echo '</table>';
    }
    
    if ($meta['reservierungAktiviert'] == '1') {
        
        if ($availablePoints > 0 && $availableWishes > 0) {
        
            echo '
            <p>
                Sie können Wünsche äußern, mit welchen Abiturienten Sie an einem Tisch sitzen
                möchten. Bestellungen der gleichen Person werden normalerweise an einen Tisch gesetzt.
            </p><p>
                Sie dürfen noch <b>' . $availablePoints . ' Punkt' . ($availablePoints > 1 ? 'e' : '') . '</b> auf
                <b>' . $availableWishes . ' ' . ($availablePoints > 1 ? 'Wünsche' : 'Wunsch') . '</b> verteilen.
            </p>
            
            <form action="reservierung2.php" method="post">
                <fieldset style="margin-bottom:1em">
                    <label>
                        <span class="labelText">Abiturient/in:</span>
                        <input type="text" name="vorname" placeholder="Vorname" value="" style="max-width:40%; max-width:calc(50% - 22px)">
                        <input type="text" name="nachname" placeholder="Nachname" value="" style="max-width:40%; max-width:calc(50% - 22px)">
                    </label>
                    <div style="height: 10px;"></div>
                    <label>
                        <span class="labelText">Punkte:</span>
                        <input type="number" name="punkte" value="1" min="1" max="' . $availablePoints . '">
                    </label><br>
                    <span style="font-size:95%">
                        <span class="labelText"></span> Je mehr Punkte die Person bekommt,<br>
                        <span class="labelText"></span> desto eher wird der Wunsch berücksichtigt.
                    </span>
                    <div style="height: 10px;"></div>
                    <input type="submit" value="Speichern">
                </fieldset>
            </form> ';
    
        } else if ($availablePoints <= 0) {
            echo '<p>
                Sie haben Ihre ' . $meta['reservierungsPunkte'] . ' Punkte aufgebraucht.
            </p>';
        } else {
            echo '<p>
                Sie haben Ihre ' . $meta['maxReservierungen'] . ' Wünsche aufgebraucht.
            </p>';
        }
        
    } else {
        echo '<p>Die Sitzplatzfunktion ist momentan deaktiviert.</p>';
    }
    
} else {
    echo '<p>Diese Seite ist nicht verfügbar, da Sie noch keine Karten bestellt haben.</p>';
}



echo '</div>';
include '_part2.inc.php';