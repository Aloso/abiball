<?php

session_start();
require_once "resources/settings.inc.php";

include "_part1.inc.php";


if (isset($_POST['email']) && isset($_POST['vorname']) && isset($_POST['nachname'])) {

    $email = $_POST['email'];
    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];

    $encVorname = $mysqli->real_escape_string($vorname);
    $encNachname = $mysqli->real_escape_string($nachname);

    $userRes = $mysqli->query("SELECT * FROM user WHERE nachname = '$encNachname' AND vorname = '$encVorname'");

    if (($row = $userRes->fetch_assoc()) != null) {

        if ($row['status'] == 'blocked') {
            header('Location: login.php?error=blocked');
            exit;
        }
        if ($row['status'] == 'inactive') {
            header('Location: login.php?error=notAuthorizedToReset');
            exit;
        }

        if ($row['email'] == $email) {

            $vString = time() . mt_rand(1000, 10000);
            $encVString = $mysqli->real_escape_string($vString);
            $encUserID = $mysqli->real_escape_string($row['id']);

            $success = $mysqli->query("UPDATE user
                    SET passwordHash = '', verificationString = '$encVString'
                    WHERE vorname = '$encVorname' AND nachname = '$encNachname'");
            if (!$success) {
                header('Location: login.php?message=resetEmailFailed');
                exit;
            }

            include 'mailtemplate.inc.php';

            $subject = 'Zurücksetzen Ihres Passwortes';
            $vLink = $meta['url'] . 'resetPassword.php?id=' . $encUserID . '&verificationString=' . urlencode($vString);

            $text = 'Zurücksetzen Ihres Passwortes

Klicken Sie auf folgenden Link, um Ihr Passwort zurückzusetzen: ' . $vLink .'

Danach können Sie sich mit dem voreingestellten Passwort anmelden.';

            $htmlText = '<h1 style="margin-top: 0; font-size: 30px">Zurücksetzen Ihres Passwortes</h1>

Um Ihr Passwort zurückzusetzen, klicken Sie auf folgenden Link:<br><br><br>

<div style="text-align:center;font-size: 19px;"><a href="' . $vLink . '" style="color: white; background-color: #007ae6;text-decoration: none; padding: 10px 12px; border-radius: 3px; display: inline-block; border: 1px solid #06f;">Passwort zurücksetzen</a></div><br><br>

<hr style="border-top:none; border-left:none; border-right:none; border-bottom: 1px solid #aaaaaa;">
' . date('d.m.Y H:i') . '<br>
' . $meta['pageName'];

            if (!phpmailerSend($email, $subject, $htmlText, $text)) {
                header('Location: login.php?message=resetEmailFailed');
                exit;
            }

        }

        header('Location: login.php?message=resetEmailSent');
        exit;

    } else {
        $error = 'Der User existiert nicht.';
        include 'error_message.inc.php';
    }

}

?>

<style>
    form {
        margin: 0 0 0.7em 0;
    }
    .labelText {
        display: inline-block;
        width: 150px;
    }
    input[type=email], input[type=text] {
        border: 1px solid rgb(148, 148, 148);
        border-radius: 3px;
        padding: 4px 8px;
        font-size: 95%;
        font-family: Roboto, 'Open Sans', sans-serif;
    }
    input[type=submit] {
        border-radius: 3px;
        padding: 4px 8px;
        font-size: 95%;
        font-family: Roboto, 'Open Sans', sans-serif;
        border: 1px solid #0063e6;
        background-color: rgb(49, 127, 255);
        color: white;
    }

    @media (max-width: 400px) {
        .labelText {
            display: block;
        }
    }
</style>

<div class="message">
    <div style="text-align: left">
        <h3 style="margin-top: 0">Passwort vergessen</h3>
        <p>
            Wenn Sie sich zum ersten Mal anmelden, brauchen Sie das <b>voreingestellte Passwort</b>.
            Dieses sollten Sie von den Abiball-Organisatoren erhalten haben.
        </p><p>
            Wenn Sie bereits ein Passwort festgelegt haben und dieses vergessen haben,
            geben Sie Ihren Namen und Ihre E-Mailadresse ein, um es zurückzusetzen:
        </p>
        <form action="help.php" method="post">
            <label>
                <span class="labelText">Vorname:</span>
                <input type="text" name="vorname">
            </label>
            <div style="height: 10px;"></div>
            <label>
                <span class="labelText">Nachname:</span>
                <input type="text" name="nachname">
            </label>
            <div style="height: 10px;"></div>
            <label>
                <span class="labelText">E-Mailadresse:</span>
                <input type="email" name="email">
            </label>
            <div style="height: 10px;"></div>
            <span class="labelText"></span> <input type="submit" value="Absenden">
        </form>
        <span class="labelText"></span> <a href="login.php" class="button">Zurück</a>
    </div>
</div>

<?php include "_part2.inc.php"; ?>
