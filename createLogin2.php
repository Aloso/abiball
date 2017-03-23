<?php

session_start();
require_once 'resources/settings.inc.php';

include '_part1a.inc.php';
echo '<div id="content">';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 'nearly' &&
        isset($_SESSION['passwort']) && $_SESSION['passwort'] == DefaultPassword) {
    
    $encUserID = $mysqli->real_escape_string($_SESSION['userID']);
    
    $data = $mysqli->query("SELECT * FROM user WHERE id = $encUserID");
    
    if (($row = $data->fetch_assoc()) != null) {
    
        if ($row['status'] != 'inactive' && $row['status'] != 'incomplete') {
            $error = 'Ungültige Anfrage';
            include 'error_message.inc.php';
        }
        
        if (isset($_POST['email']) && isset($_POST['passwort']) && isset($_POST['passwort2']) &&
                $_POST['email'] != '' && $_POST['passwort'] != '' && $_POST['passwort2'] != '') {
    
            $email = $_POST['email'];
            $passwort = $_POST['passwort'];
            $passwort2 = $_POST['passwort2'];
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                
                if (strlen($passwort) > 7) {
                    
                    if ($passwort == $passwort2) {
    
                        $encEmail = $mysqli->real_escape_string($email);
                        $encPasswort = $mysqli->real_escape_string(password_hash($passwort, PASSWORD_BCRYPT));
                        $encEmail = $mysqli->real_escape_string($email);
                        
                        $vString = time() . mt_rand(1000, 10000);
                        $verificationLink = $meta['url'] . 'verifyAccount.php?id=' . $encUserID . '&verificationString=' . urlencode($vString);
                        
                        $success = $mysqli->query("UPDATE user SET email = '$encEmail', status = 'incomplete',
                                passwortHash = '$encPasswort', verificationString = $vString
                                WHERE id = $encUserID");
                        
                        if ($success) {
                            $_SESSION['passwort'] = $passwort;
                            $_SESSION['loggedin'] = 'nearly';
                            
                            
                            require_once 'PHPMailer-master/class.phpmailer.php';
                            require_once 'PHPMailer-master/class.smtp.php';
                            require_once 'PHPMailer-master/phpmailer.lang-de.php';
                            
                            $mail = new PHPMailer;
    
                            $mail->SMTPDebug = 0;                                 // debugging: 1 = errors and messages, 2 = messages only
                            $mail->isSMTP();                                      // Set mailer to use SMTP
                            $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
                            $mail->SMTPAuth = true;                               // Enable SMTP authentication
                            $mail->Username = $meta['webmasterMail'];             // SMTP username
                            $mail->Password = WebmasterPassword;                  // SMTP password
                            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                            $mail->Port = 465;
                            
                            $mail->setFrom($meta['webmasterMail']);
                            $mail->addAddress($email);
                            $mail->addReplyTo($meta['webmasterMail']);
                            
                            $mail->isHTML(true);                                  // Set email format to HTML
    
                            $mail->Subject = 'Aktivieren Sie Ihren Account';
                            // This is a really cool html E-Mail template!
                            $mail->Body = '<html>
<head>
    <title>Aktivieren Sie Ihren Account</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700" rel="stylesheet">
</head>
<body>
<div style="font-family: \'Roboto\', \'Open Sans\', \'Helvetica Neue\', \'Arial\', sans-serif; font-size: 17px; max-width:700px; margin: 15px auto; background-color: white; border: 1px solid #d7d7d7; padding: 18px">

    <h1 style="margin-top: 0; font-size: 30px"><span style="display:inline-block;border-bottom:5px solid #f23c00">Willkommen</span> <span style="display:inline-block;border-bottom:5px solid white">beim Abiball</span> <span style="display:inline-block;border-bottom:5px solid white">Kartenbestellsystem.</span></h1>

    Sie sind nur noch einen Klick von Ihrem Account entfernt!<br><br><br>

    <div style="text-align:center;font-size: 19px;"><a href="' . $verificationLink . '" style="color: white; background-color: #007ae6;text-decoration: none; padding: 10px 12px; border-radius: 3px; display: inline-block; border: 1px solid #06f;">Account aktivieren</a></div><br><br>

    Sie haben eine Frage an uns? Schreiben Sie uns an unter
    <a href="mailto:' . $meta['webmasterMail'] . '">' . $meta['webmasterMail'] . '</a><br><br>

    <hr style="border-top:none; border-left:none; border-right:none; border-bottom: 1px solid #aaaaaa;">
    Sie erhalten diese Nachricht, weil mit Ihrer E-Mailadresse ein Account
    auf ' . $meta['url'] . ' erstellt wurde.

</div>
</body>
</html>';
                            
                            $mail->AltBody = 'Aktivieren Sie Ihren Account

Sie sind nur noch einen Klick von Ihrem Account entfernt!

Öffnen Sie folgenden Link, um den Account zu aktivieren: ' . $verificationLink . '

Sie haben eine Frage an uns? Schreiben Sie uns an unter ' . $meta['webmasterMail'] . '

Sie erhalten diese Nachricht, weil mit Ihrer E-Mailadresse ein Account auf
' . $meta['url'] . ' erstellt wurde.';

                            if(!$mail->send()) {
                                header('Location: unverifiedLanding.php?error=sendFailed');
                                exit;
                            } else {
                                header('Location: unverifiedLanding.php');
                                exit;
                            }
                            
                        } else {
                            $error = 'Datenbankfehler: ' . $mysqli->error;
                            include 'error_message.inc.php';
                        }
                        
                    } else {
                        header('Location: createLogin.php?error=passwordsDontMatch');
                        exit;
                    }
                    
                } else {
                    header('Location: createLogin.php?error=weakPassword');
                    exit;
                }
                
            } else {
                header('Location: createLogin.php?error=invalidEmail');
                exit;
            }
            
        } else {
            header('Location: createLogin.php?error=missingData');
            exit;
        }
        
    } else {
        $error = 'Nutzer nicht gefunden';
        include 'error_message.inc.php';
    }
    
} else {
    $error = 'Ungültige Anfrage';
    include 'error_message.inc.php';
}

echo '</div>';
include '_part2.inc.php';