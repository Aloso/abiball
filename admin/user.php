<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

include 'header.php';

if (isset($_GET['id'])) {

    $u_id = $mysqli->real_escape_string($_GET['id']);

    $result = $mysqli->query('SELECT * FROM user WHERE id = ' . $u_id);

    if (($row = $result->fetch_assoc()) != null) {

        $u_vorname = $row['vorname'];
        $u_nachname = $row['nachname'];
        $u_email = $row['email'];
        $u_status = $row['status'];

        if (isset($_POST['email'])) {
            $email = $mysqli->real_escape_string($_POST['email']);
            $success = $mysqli->query("UPDATE user SET email = '$email' WHERE id = $u_id");
            if ($success) {
                echo '<p>Die E-Mailadresse wurde verändert.</p>';
                $u_email = $email;
            } else {
                echo '<p>Fehler beim Ändern der E-Mailadresse!</p>';
            }
        }

        if (isset($_POST['status'])) {

            $status = $mysqli->real_escape_string($_POST['status']);
            if ($status == 'admin' || $status == 'blocked' || $status == 'member' ||
                    $status == 'inactive' || $status == 'incomplete') {

                $success = $mysqli->query("UPDATE user SET status = '$status' WHERE id = $u_id");
                if ($success) {
                    echo '<p>Der Status wurde verändert.</p>';
                    $u_status = $status;
                } else {
                    echo '<p>Fehler beim Ändern des Status!</p>';
                }

            } else {
                echo '<p>Dieser Wert ist nicht erlaubt!</p>';
            }

        }

        if (isset($_POST['password']) && $_POST['password'] == 'reset') {

            $success = $mysqli->query("UPDATE user SET passwordHash = '' WHERE id = $u_id");

            if ($success) {
                echo '<p>
                    Das Passwort wurde zurückgesetzt. Beim nächsten Login muss die Person
                    das voreingestellte Passwort eingeben und wird dann aufgefordert, ein neues
                    Passwort zu erstellen. Bestellungen bleiben jedoch erhalten.
                </p>';
            } else {
                echo '<p>Fehler beim Zurücksetzen des Passwortes!</p>';
            }
        }

        if (isset($_POST['account']) && $_POST['account'] == 'reset') {

            $success = $mysqli->query("UPDATE user SET status = 'inactive', passwordHash = '',
                    email = '', lastActive = 0 WHERE id = $u_id");

            if ($success) {
                echo '<p>
                    Der Account wurde zurückgesetzt. Der Nutzer kann sich nun mit dem
                    voreingestellten Passwort anmelden.
                </p>';
                $u_status = 'inactive';
                $u_email = '';

                $success = $mysqli->query("DELETE FROM bestellungen WHERE userID = $u_id");
                if ($success) {
                    echo '<p>Die Kartenbestellungen der Person wurden gelöscht.</p>';
                } else {
                    echo '<p>Fehler beim Löschen der Kartenbestellungen der Person.</p>';
                }

            } else {
                echo '<p>Fehler beim Zurücksetzen des Accounts!</p>';
            }
        }

        if (isset($_POST['account']) && $_POST['account'] == 'delete') {

            $success = $mysqli->query("DELETE FROM user WHERE id = $u_id");

            if ($success) {
                echo '<p>
                    Der Account wurde <b>gelöscht</b>.
                </p>';

                $success = $mysqli->query("DELETE FROM bestellungen WHERE userID = $u_id");
                if ($success) {
                    echo '<p>Die Kartenbestellungen der Person wurden gelöscht.</p>';
                } else {
                    echo '<p>Fehler beim Löschen der Kartenbestellungen der Person!</p>';
                }

            } else {
                echo '<p>Fehler beim Löschen des Accounts!</p>';
            }

            exit;
        }

        echo "<h1>Profil von $u_vorname $u_nachname</h1>
        <p>
            <form action='user.php?id=$u_id' method='post'>
                <span class='labelText'>E-Mailadresse:</span>
                <input type='text' value='$u_email' placeholder='$u_email' name='email'>
                <input type='submit' value='Ändern'>
            </form>
        </p>
        <p>
            <form action='user.php?id=$u_id' method='post'>
                <span class='labelText'>Status:</span>
                <input type='text' value='$u_status' placeholder='$u_status' name='status'>
                <input type='submit' value='Ändern'>
                <div style='margin-left: 185px'>
                    Mögliche Werte:
                    <ul>
                        <li><b>admin</b> (Zugriff auf Admin-Bereich)</li>
                        <li><b>member</b> (normales Mitglied)</li>
                        <li><b>blocked</b> (kann sich nicht anmelden)</li>
                        <li><b>inactive</b> (noch nie angemeldet, voreingestelltes Passwort wird benötigt)</li>
                        <li><b>incomplete</b> (der Account wurde noch nicht verifiziert)</li>
                    </ul>
                    <b>inactive</b> und <b>incomplete</b> sollten nicht verwendet werden!
                </div>
            </form>
        </p>
        <p>
            <form action='user.php?id=$_GET[id]' method='post'>
                <span class='labelText'>Passwort:</span>
                <input type='hidden' value='reset' name='password'>
                <input type='submit' value='Zurücksetzen'>
            </form>
        </p>
        <p>
            <form action='user.php?id=$_GET[id]' method='post' style='display: inline-block;'>
                <span class='labelText'>Account:</span>
                <input type='hidden' value='reset' name='account'>
                <input type='submit' value='Zurücksetzen'>
            </form>
            <form action='user.php?id=$_GET[id]' method='post' style='display: inline-block;'>
                <input type='hidden' value='delete' name='account'>
                <input type='submit' value='Löschen'>
            </form><br>
            <span class='labelText'></span> <b>Warnung:</b> Das Zurücksetzen / Löschen löscht auch die zugehörigen
            Kartenbestellungen.
        </p>";

        echo '<h2>Bestellte Karten</h2>';

        $data = $mysqli->query("SELECT * FROM bestellungen WHERE userID = $u_id ORDER BY bezahlt");
        if ($data->num_rows == 0) {
            echo '<p>Keine Bestellungen gefunden.</p>';
        } else {
            $mahnungMoeglich = false;

            echo '<table>
    <tr><th>Name</th><th>Preis</th><th>Bestellt am</th><th>Bezahlt</th><th></th></tr>';

            while (($row = $data->fetch_assoc()) != null) {
                $bestelltAm = date("d. m. Y  H:m", $row['bestelltAm']);

                echo '<tr>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['preis'] . ' Euro</td>
                    <td>' . $bestelltAm . ' Uhr</td>
                    <td>' . ($row['bezahlt'] ? 'Ja' : 'Nein') . '</td>
                    <td>';

                if (!$row['bezahlt']) {
                    $mahnungMoeglich = true;

                    echo "<form action='bestelluebersicht.php' method='post' style='display:inline'>
                        <input type='hidden' name='zahlungRegistrieren' value='$row[id]'>
                        <input type='submit' value='Zahlung registrieren'>
                    </form>
                    <form action='bestelluebersicht.php' method='post' style='display:inline'>
                        <input type='hidden' name='loeschen' value='$row[id]'>
                        <input type='submit' value='Bestellung löschen' class='secondary'>
                    </form>";
                }


                echo '</td></tr>';
            }

            echo '</table>';

            if ($mahnungMoeglich) {
                echo "<p><form action='mahnung.php' method='post' style='display:inline'>
                    <input type='hidden' name='mahnen' value='$row[userID]'>
                    <input type='submit' value='Mahnung versenden' class='secondary'>
                </form></p>";
            }
        }

    } else {
        $error = 'User existiert nicht';
        include __DIR__.'/error_message.inc.php';
    }

} else {
    $error = 'Fehlerhafte Anfrage';
    include __DIR__.'/error_message.inc.php';
}

include 'footer.php';
