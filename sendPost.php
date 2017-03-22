<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';

if (isset($_POST['text'])) {
    
    $text = $_POST['text'];
    $text = str_replace('&', '&amp;', $text);
    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);
    $text = str_replace("\n", '<br>', $text);
    $text = str_replace("\r", '', $text);
    
    // parse **fetter Text** und __kursiver Text__
    
    $newtext = preg_filter('/(^|\.| |<br>)\*\*(.+?)\*\*( |\.|,|:|;|!|\?|\.|<br>|$)/m', '${1}<b>${2}</b>${3}', $text);
    if ($newtext != null) $text = $newtext;
    
    $newtext = preg_filter('/(^|\.| |<br>)__(.+?)__( |\.|,|:|;|!|\?|\.|<br>|$)/m', '${1}<i>${2}</i>${3}', $text);
    if ($newtext != null) $text = $newtext;
    
    $len = strlen($text);
    
    if ($len == 0) {
        header('Location: forum.php?error=emptyString');
        exit;
    } else if ($len < 8) {
        header('Location: forum.php?error=shortString');
        exit;
    } else if ($len > 2000) {
        header('Location: forum.php?error=longString');
        exit;
    } else {
        
        $encText = $mysqli->real_escape_string($text);
        $encAutor = $mysqli->real_escape_string($vorname . ' ' . $nachname);
        $now = time();
        
        $success = $mysqli->query("INSERT INTO forum (autor, text, datum)
                VALUES ('$encAutor', '$encText', $now)");
        if ($success) {
            header('Location: forum.php?message=sent');
            exit;
        } else {
            header('Location: forum.php?error=databaseError');
            exit;
        }
        
    }
    
} else {
    header('Location: forum.php');
    exit;
}