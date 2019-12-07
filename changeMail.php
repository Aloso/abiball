<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';
echo '<h1>E-Mailadresse ändern</h1>';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<p>Die E-Mailadresse wurde aktualisiert. Sie lautet nun ' . $email . '</p>
        <p><b>Achtung:</b> Bitte vergewissern Sie sich, dass Sie über diese Adresse E-Mails
        empfangen können. Andernfalls können Sie nicht über wichtige Details zu Ihren Bestellungen
        informiert werden.</p>';

        $encEmail = $mysqli->real_escape_string($email);

        $success = $mysqli->query("UPDATE user SET email = '$encEmail' WHERE id = $userID");
        if (!$success) {
            echo '<p>Beim Speichern ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.</p>';
        }

    } else {
        echo '<p class="errorP"><b>Fehler:</b> Die angegebene Adresse ist ungültig.</p>';
    }
}

echo '<p>Geben Sie hier die neue E-Maiadresse ein:</p>
<form action="changeMail.php" method="post">
    <input type="email" name="email">
    <input type="submit" value="Absenden">
</form>

<p><a href="profil.php">Zurück zum Profil</a></p>';


echo '</div>';
include '_part2.inc.php';
