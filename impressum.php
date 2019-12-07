<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

$data = $mysqli->query("SELECT * FROM seitentexte WHERE name = 'Impressum'");
if (($row = $data->fetch_assoc()) != null) {
    echo '<div class="pageText">' . $row['htmlText'] . '</div>';
}

echo '</div>';
include '_part2.inc.php';