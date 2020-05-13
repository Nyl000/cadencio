<?php

namespace Cadencio\Services;

use Cadencio\Models\SettingModel;

class Utils {

    public static function sendWithSendgrid($to) {

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("test@example.com", "Example User");
        $email->setSubject("Sending with SendGrid is Fun");
        $email->addTo("test@example.com", "Example User");
        $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
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