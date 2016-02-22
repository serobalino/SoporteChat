<?php

require_once ROOT_DIR . '/lib/PHPMailer/class.phpmailer.php';
require_once ROOT_DIR . '/lib/PHPMailer/class.pop3.php';
require_once ROOT_DIR . '/lib/PHPMailer/class.smtp.php';

class Mailer extends Service
{
    public function sendMessage($from, $to, $subject, $body)
    {
        $config = $this->get('config')->data['services']['mailer'];
        $mail   = new PHPMailer;

        // Configure SMTP

        ob_start();

        if($config['smtp'])
        {
            $mail->SMTPDebug = 3;
            $mail->isSMTP();
            $mail->Timeout    = 15;
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = $config['smtpSecure'];
            $mail->Host       = $config['smtpHost'];
            $mail->Port       = $config['smtpPort'];
            $mail->Username   = $config['smtpUser'];
            $mail->Password   = $config['smtpPass'];
        }

        // Prepare the message

        $mail->setFrom($config['smtpUser']);
        $mail->addAddress($to);
        $mail->addReplyTo($from);
        $mail->isHTML(true);

        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $body;

        // Send

        $success = $mail->send();

        $debugInfo = ob_get_contents();
        ob_end_clean();

        if(!$success)
        {
            $this->get('logger')->error($mail->ErrorInfo . "\nDebug information:\n\n" . $debugInfo);
        }

        return $success;
    }
}

?>
