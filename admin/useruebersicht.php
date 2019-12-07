<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

include 'header.php';

if (isset($_POST['text'])) {
    $text = $_POST['text'];
    $text = str_replace("\r", '', $text);
    $array = explode("\n", $text);

    $len = count($array);
    for ($i = 0; $i < $len; $i++) {
        $x = $array[$i];
        if (strlen($x) == 0) continue;

        $names = explode(' ', $x);
        $namesLen = count($names);
        $vorname = '';
        $nachname = '';
        $istVorname = true;
        for ($j = 0; $j < $namesLen; $j++) {
            if ($names[$j] == '') continue;
            if ($istVorname) {
                $vorname = $names[$j];
                $istVorname = false;
            } else {
                $nachname = $names[$j];
                break;
            }
        }
        if ($vorname != '' && $nachname != '') {
            $vorname = $mysqli->real_escape_string($vorname);
            $nachname = $mysqli->real_escape_string($nachname);

            $success = $mysqli->query("INSERT INTO user (email, vorname, nachname, passwordHash)
                    VALUES('', '$vorname', '$nachname', '')");
            if ($success) {
                echo 'User "' . $vorname . ' ' . $nachname . '" eingefügt.<br>';
            } else {
                echo '<b>Fehler</b> beim Einfügen von "' . $vorname . ' ' . $nachname . '"!';
                echo $mysqli->error;
            }
        } else {
            echo '<b>Fehler</b> beim Einfügen von "' . $x . '" (Falsches Format)';
        }
    }

    echo '<script type="text/javascript">
        setTimeout(function() {
            window.location.href = window.location.href;
        }, 4000);
    </script>';
}

echo '<h1>Userübersicht</h1>

<h2>User erstellen</h2>
<p>
    Gib einen User pro Zeile ein. Schreib erst den Vornamen, dann ein Leerzeichen,
    dann den Nachnamen.
</p>
<form action="useruebersicht.php" method="post">
    <textarea class="fullWidthTA" name="text"></textarea>
    <input type="submit" value="Erstellen">
</form>

<h2>Alle User</h2>
<p>
    Klicke auf einen Spaltennamen, um die Tabelle danach zu sortieren. Klicke auf einen Nachnamen,
    um zum Profil zu wechseln.
</p>
<table>
    <tr>
        <th><a href="?sortby=id">ID</a></th>
        <th><a href="?sortby=vorname">Vorname</a></th>
        <th><a href="?sortby=nachname">Nachname</a></th>
        <th><a href="?sortby=email">E-Mail</a></th>
        <th><a href="?sortby=status">Status</a></th>
        <th><a href="?sortby=lastActive">Zuletzt aktiv</a></th>
    </tr>';

$sort = '';
if (isset($_GET['sortby'])) {
    $sortby = $_GET['sortby'];
    switch ($sortby) {
        case 'id':
        case 'vorname':
        case 'nachname':
        case 'status':
            $sort =  'ORDER BY ' . $sortby;
            break;
        case 'lastActive':
            $sort =  'ORDER BY ' . $sortby . ' DESC';
            break;
        case 'email':
            // sortiere alphabetisch, aber Leute mit keiner E-Mail
            // sollen ganz unten stehen
            $sort = 'ORDER BY CASE WHEN email = \'\' THEN 2 ELSE 1 END, email';
    }
}

$allUser = $mysqli->query('SELECT * FROM user ' . $sort);

while (($row = $allUser->fetch_assoc()) != null) {
    $lastActive = date('d.m.Y - H:i', $row['lastActive']);

    echo "<tr>
        <td>$row[id]</td>
        <td>$row[vorname]</td>
        <td><a href='user.php?id=$row[id]' title='Profil zeigen'>$row[nachname]</a></td>
        <td>$row[email]</td>
        <td>$row[status]</td>
        <td>$lastActive</td>
    </tr>";
}

echo '</table>';

include 'footer.php';
