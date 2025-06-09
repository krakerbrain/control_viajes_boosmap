<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/config/ConfigUrl.php';
$baseUrl = ConfigUrl::get();


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $_ENV['DIRECCIONCORREO'];                     //SMTP username
    $mail->Password   = $_ENV['MAILPASS'];                              //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($_ENV['DIRECCIONCORREO'], 'Registro de viajes Boosmap');
    $mail->addAddress($correo, '');     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo($_ENV['DIRECCIONCORREO'], 'Cambio Clave');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Control de Viajes - Cambio de clave';
    $mail->Body    = '<div style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5;">
	                    <p>Para poder cambiar tu clave debes hacer click en el siguiente enlace</p>
	                    <a href="' . $baseUrl . 'login/cambio_clave.php' . '?correo=' . urlencode($correo) . '&clave=' . urlencode($clave_recuperacion) . '">Cambio de clave</a>
	                    <p>Saludos</p>
                    </div>';
    $mail->AltBody = 'Para cambiar tu clave, copia y pega la siguiente URL en tu navegador web:
                    ' . $baseUrl . 'login/cambio_clave.php' .  '?correo=' . urlencode($correo) . '&clave=' . urlencode($clave_recuperacion) . '
                    Saludos';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    $errorMessage = date('Y-m-d H:i:s') . " - Error al enviar correo a {$correo}: {$mail->ErrorInfo}" . PHP_EOL;
    file_put_contents(__DIR__ . '/logs/email_errors.log', $errorMessage, FILE_APPEND);
}
