<?php

session_start();
require_once '../resources/settings.inc.php';
require_once 'verifyAdmin.php';

include 'header.php';

$data = $mysqli->query("SELECT * FROM seitentexte WHERE name = 'Admin-Text'");
if (($row = $data->fetch_assoc()) != null) {
    echo '<div class="pageText">' . $row['htmlText'] . '</div>';
}

echo '<h1>Statistiken</h1>';

$userCount = $mysqli->query("SELECT COUNT(*), status FROM user GROUP BY status");
$countAll = 0;
echo '<ul>';
while (($row = $userCount->fetch_assoc()) != null) {
    $countAll += $row['COUNT(*)'];
    echo "<li><b>{$row['COUNT(*)']}</b> User sind '$row[status]'.</li>";
}
echo "</ul><p><b>Insgesamt</b> sind <b>$countAll</b> User registriert.</p>";


$bestellCount = $mysqli->query("SELECT COUNT(*), bezahlt FROM bestellungen GROUP BY bezahlt");
$countAll = 0;
echo '<ul>';
while (($row = $bestellCount->fetch_assoc()) != null) {
    $countAll += $row['COUNT(*)'];
    $bezahlt = $row['bezahlt'] == '1' ? 'bezahlt' : 'nicht bezahlt';
    echo "<li><b>{$row['COUNT(*)']}</b> Bestellungen sind $bezahlt.</li>";
}
echo "</ul><p><b>Insgesamt</b> wurden <b>$countAll</b> Karten bestellt.</p>";

$minutesAgo = time() - 3600; // 1 Stunde

$lastActiveUser = $mysqli->query("SELECT * FROM user WHERE lastActive > $minutesAgo ORDER BY lastActive DESC");
if ($lastActiveUser->num_rows != 0) {
    echo '<p>In der letzten Stunde waren folgende Nutzer aktiv:</p>
    <table>
        <tr><th>ID</th><th>E-Mail</th><th>Name</th><th>Status</th><th>Zuletzt aktiv um</th></tr>';

    while (($row = $lastActiveUser->fetch_assoc()) != null) {
        $date = date('H:i', $row['lastActive']);
        echo "<tr><td>$row[id]</td><td>$row[email]</td><td><a href='user.php?id=$row[id]'>$row[vorname] $row[nachname]</a></td><td>$row[status]</td><td>$date</td></tr>";
    }

    echo '</table>';

} else {
    echo '<p>In der letzten Stunde waren keine Nutzer aktiv.</p>';
}


include 'footer.php';
