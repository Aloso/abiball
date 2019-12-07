<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer-master/src/Exception.php';
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';

function phpmailerSend($adress, $subject, $htmlMailBody, $mailBody = '') {
    global $meta;

    $mail = new PHPMailer(true);

    try {
        if (useSmtp) {
            $mail->SMTPDebug = 0;                                 // debugging: 1 = errors and messages, 2 = messages only
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $meta['webmasterMail'];             // GMail username (email address)
            $mail->Password = WebmasterPassword;                  // GMail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        }

        $mail->setFrom($meta['webmasterMail']);
        $mail->addAddress($adress);
        $mail->addReplyTo($meta['webmasterMail']);

        $mail->isHTML(true);
        $mail->Subject = utf8_decode($subject);
        $mail->Body = '<html>
<head>
    <title>Aktivieren Sie Ihren Account</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700" rel="stylesheet">
</head>
<body>
    <div style="font-family: \'Roboto\', \'Open Sans\', \'Helvetica Neue\', \'Arial\', sans-serif; font-size: 17px; max-width:700px; margin: 15px auto;
    background-color: white; border: 1px solid #d7d7d7; padding: 18px">
    ' . utf8_decode($htmlMailBody) . '
    </div>
</body>
</html>';

        $mail ->AltBody = $mailBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo '<span style="color: red">Email-Fehler: ' . $mail->ErrorInfo . '</span>';
        return false;
    }
}
