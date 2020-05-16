<?php

namespace Cadencio\Services;

use Cadencio\Models\SettingModel;
use PHPMailer\PHPMailer\PHPMailer;

class Utils {


    public static function sendMail($to,$subject,$body) {
        $mail = new PHPMailer(true);
        $settingsModel = new SettingModel();
        $settings = $settingsModel->getSettings('mail_smtp_user','mail_smtp_port','mail_smtp_password','mail_smtp_host','mail_smtp_fromname','mail_smtp_frommail');

        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $settings['mail_smtp_host'];
        $mail->Port = $settings['mail_smtp_port'];
        $mail->Username = $settings['mail_smtp_user'];
        $mail->Password = $settings['mail_smtp_password'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($settings['mail_smtp_frommail'],$settings['mail_smtp_fromname']);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        $mail->send();
    }

    public static function generateOAuthChallenge() {
        $random_bytes = substr(md5(microtime() + rand(1,9999)),0,64);
        $codeverifier = rtrim(strtr(base64_encode($random_bytes), "+/","-_"), "=");
        $challenge_bytes = hash("sha256", $codeverifier, true);
        $codechallenge = rtrim(strtr(base64_encode($challenge_bytes), "+/", "-_"), "=");


        return [
            'code_challenge' => $codechallenge,
            'code_verifier' => $codeverifier,
        ];
    }

    public static function setAppSetting($name,$val) {
        $model = new SettingModel();
        $model->createOrUpdate(['name' => $name,'val' => $val]);
    }

    public static function getAppSetting($name) {
        $model = new SettingModel();
        $data = $model->getOne($name,'name');
        return $data['val'];
    }
}