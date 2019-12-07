<?php

session_start();
require_once 'resources/settings.inc.php';



include '_part1a.inc.php';
echo '<div id="content">';

if (isset($_GET['error']) && $_GET['error'] == 'sendFailed') {
    
    echo '<div class="error message">Beim Senden der E-Mail ist ein Fehler aufgetreten.<br>
            Bitte rufen Sie die Startseite auf und versuchen Sie es noch einmal.</div>
            <a class="button primary" href="index.php">Startseite</a>';
    
} else if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 'nearly' &&
        isset($_SESSION['userID']) && isset($_SESSION['passwort'])) {
    
    $encUserID = $mysqli->real_escape_string($_SESSION['userID']);
    
    $data = $mysqli->query("SELECT * FROM user WHERE id = $encUserID AND status = 'incomplete'");
    
    if (($row = $data->fetch_assoc()) != null) {
        
        echo '<h1>Ihr Account muss noch verifiziert werden.</h1>
        <p>Überprüfen Sie bitte Ihren Posteingang.</p>
        <p>Wenn Sie Probleme mit der Anmeldung haben, wenden Sie sich bitte an den Webmaster: '
                . $meta['webmasterMail'] . '</p>
        <a class="button primary" href="logout.php">Logout</a>';
        
    } else {
        $error = 'Nutzer nicht gefunden';
        include 'error_message.inc.php';
    }
    
} else {
    header("Location: login.php");
    exit;
}

echo '</div>';
include '_part2.inc.php';