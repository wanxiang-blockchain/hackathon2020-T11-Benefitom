<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-02-14
 * Time: 11:48
 */

namespace App\Utils;


use PHPMailer\PHPMailer\PHPMailer;

class EmailUtil
{
    public static function send($subject, $body, $altbody)
    {
        //
        require_once base_path() . '/vendor/autoload.php';

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 1;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->CharSet='utf8';
            $mail->Host = 'smtp.163.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'deyigongpan@163.com';                 // SMTP username
            $mail->Password = '04302211yi';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 25;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('deyigongpan@163.com', 'Mailer');
            $mail->addAddress('553442317@qq.com', 'just like before');     // Add a recipient

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altbody;

            $mail->send();
            echo 'Message has been sent' . PHP_EOL;
        } catch (\Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

}