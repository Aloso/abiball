<?php

session_start();
require_once 'resources/settings.inc.php';
require_once 'verifyLogin.inc.php';


$entriesPerPage = 20;
$me = $vorname . ' ' . $nachname;


include '_part1a.inc.php';
echo '<div id="content">

<h1>Forum</h1>
<style>
    .postAuthor {
        color: #d44400;
        font-weight: bold;
        margin-right: 10px;
    }
    .postDate {
        color: #616161;
    }
    .pageSelect {
        margin: 20px;
        text-align: center;
    }
    .message.error {
        padding: 7px !important;
        font-size: 100% !important;
    }
    .message.success {
        padding: 5px !important;
        font-size: 97% !important;
        display: inline-block;
        margin: 0 0 -11px 0 !important;
        float: right !important;
    }
</style>';


if (isset($_GET['delete'])) {
    $deleteID = intval($_GET['delete']);
    $data = $mysqli->query("SELECT * FROM forum WHERE id = $deleteID");
    if (($row = $data->fetch_assoc()) != null) {
        $autor = $row['autor'];
        if ($autor == $me || $status == 'admin') {
            // authorized to delete
            $success = $mysqli->query("DELETE FROM forum WHERE id = $deleteID");
            if ($success) {
                echo '<div class="message success" onclick="this.parentNode.removeChild(this)"
                        style="cursor:pointer">Der Post wurde gelöscht.</div>';
            } else {
                echo '<div class="error message"><b>Fehler</b> beim Löschen des Posts</div>';
            }
        }
    }
}

if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch ($error) {
        case 'databaseError':
            $error = 'Der Post konnte nicht gespeichert werden. Es liegt ein Problem in der
                    Datenbank vor. Bitte informieren Sie darüber den Webmaster: ' . $meta['webmasterMail'];
            break;
        
        case 'emptyString':
            $error = 'Der Post konnte nicht gespeichert werden. Es muss ein Text eingegeben werden.';
            break;
        case 'shortString':
            $error = 'Der Post konnte nicht gespeichert werden. Der angegebene Text ist zu kurz.';
            break;
        case 'longString':
            $error = 'Der Post konnte nicht gespeichert werden. Der angegebene Text ist zu lang
                    (länger als 2000 Zeichen).';
            break;
            
        default:
            $error = 'Der Post konnte nicht gespeichert werden.';
    }
    
    echo '<div class="error message"><b>Fehler:</b> ' . $error . '</div>';
}

if (isset($_GET['message']) && $_GET['message'] == 'sent') {
    echo '<div class="message success" onclick="this.parentNode.removeChild(this)"
            style="cursor:pointer">Der Post wurde abgesendet.</div>';
}

echo '<form action="sendPost.php" method="post">
    Dein Post:<br>
    <textarea class="fullWidthTA" name="text" placeholder="Hervorhebung:    **fetter Text**     __kursiver Text__"></textarea>
    <input type="submit" value="Senden"> &ndash; als ' . $vorname . ' ' . $nachname . '
</form>';

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
    if ($page < 1) $page = 1;
} else {
    $page = 1;
}

$limit1 = ($page - 1) * $entriesPerPage;
$limit2 = $limit1 + $entriesPerPage;

$posts = $mysqli->query("SELECT * FROM forum ORDER BY datum DESC LIMIT $limit1, $entriesPerPage");
while (($row = $posts->fetch_assoc()) != null) {
    $datum = date("d. m. Y  H:i", $row['datum']);
    
    if ($row['autor'] == $me || $status == 'admin') {
        $deleteLink = '<a href="forum.php?page=' . $page . '&delete=' . $row['id'] .
                '" style="margin-left:20px">Löschen</a>';
    } else {
        $deleteLink = '';
    }
    
    echo '<div style="height:15px"></div>
            <span class="postAuthor">' . $row['autor'] . '</span>
            <span class="postDate">' . $datum . $deleteLink . '</span>
            <div style="height: 5px"></div>
            ' . $row['text'];
}

echo '<div class="pageSelect">';

if ($page > 1) {
    echo '<a class="button primary" href="?page=' . ($page - 1) . '">Neuer</a>';
}

$testForNewer = $mysqli->query("SELECT id FROM forum ORDER BY datum DESC LIMIT $limit2, 1");
if ($testForNewer->num_rows > 0) {
    echo ' <a class="button primary" href="?page=' . ($page + 1) . '">Älter</a>';
}

echo '</div>';

echo '</div>';
include '_part2.inc.php';