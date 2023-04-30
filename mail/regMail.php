<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'SMTP.titan.email';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $_ENV['DIRECCIONCORREO'];                     //SMTP username
    $mail->Password   = $_ENV['MAILPASS'];                              //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($_ENV['DIRECCIONCORREO'], 'Registro de viajes Boosmap');
    $mail->addAddress($correo, $usuario_registro);     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo($_ENV['DIRECCIONCORREO'], 'Registro');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Gracias por registrarte '.$usuario_registro;
    $mail->Body    = '<div style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5;">
	                    <h1>¡Gracias por registrarte!</h1>
	                    <p>Estimado/a '.$usuario_registro.',</p>
	                    <p>¡Gracias por usar la app! La idea de esta aplicación es proporcionar una forma de organizar tus viajes</p>
	                    <p>ya que la app de Boosmap por los momentos no lo hace.</p>
	                    <p>Cualquier falla o mejora que consideres debería agregarse no dudes en comunicarme</p>
	                    <p>Saludos cordiales,</p>
	                    <p>M.M</p>
                    </div>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}