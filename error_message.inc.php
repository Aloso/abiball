<?php

ob_clean();

@include '_part1.inc.php';

if (isset($error) && $error === 'Nicht initialisiert') {
    echo '<div class="message">
    <h3 style="margin-top: 0">Willkommen im Abiball-Bestellsystem!</h3>
    Anscheinend ist die Seite noch nicht konfiguriert. Hole dies jetzt nach! Du benötigst
    <ul style="text-align: left">
        <li>Schreibrechte auf dem Server mit PHP</li>
        <li>Eine MySQL- oder MariaDB-Datenbank</li>
        <li>Einen Google-Account</li>
    </ul>
    <br>
    <a class="button" href="resources/create.php" target="_blank">Konfigurieren</a>';
} else {
    echo '<div class="message error">
    <h3 style="margin-top: 0">Ein Fehler ist aufgetreten:</h3>
    ' . (isset($error) ? $error : 'unbekannt') . '<br><br><br><br>
    <a class="button" href="index.php">Zur Startseite</a>';
}

if (isset($meta)) {
    echo "<br><br>Webmaster kontaktieren: <i>$meta[webmasterMail]</i>";
}

echo '</div>';

include '_part2.inc.php';

exit;
