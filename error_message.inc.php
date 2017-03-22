<?php

ob_clean();

@include '_part1.inc.php';

echo '<div class="message error">
    <h3 style="margin-top: 0">Ein Fehler ist aufgetreten:</h3>
    ' . (isset($error) ? $error : 'unbekannt') . '<br><br><br><br>
    <a class="button" href="index.php">Zur Startseite</a>';

if (isset($meta)) {
    echo "<br><br>Webmaster kontaktieren: <i>$meta[webmasterMail]</i>";
}

echo '</div>';

include '_part2.inc.php';

exit;

?>