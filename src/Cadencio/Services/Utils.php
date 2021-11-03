<?php

namespace Cadencio\Services;

use Cadencio\Application;
use Cadencio\Models\LogModel;
use Cadencio\Models\SettingModel;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Utils {


    public static function logRecorder($type,$message,$idUser = false) {
        $date = date('Y-m-d H:i:s');
        $idUser = $idUser ?: Application::$instance->getCurrentUserId();
        $model = new LogModel();
        $model->createOrUpdate([
            'date_log' => $date,
            'id_user' => $idUser,
            'type' => $type,
            'comment' => $message,
            'signature' => md5($date.$idUser.$type.$message)
        ]);
    }

    public static function sendMail($to,$subject,$body,$files=null) {
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

        if ( $files ) {
            foreach ($files as $key => $file) {
                $mail->AddAttachment($file, basename($file));
                
            }
        }

        $mail->send();
    }

    public static function generateOAuthChallenge() {
        $random_bytes = substr(md5(microtime() . rand(1,9999)),0,64);
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

    public static function isPdf($filePath) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        return $mime == 'application/pdf';
    }

    public static function isJson($string) {
        return json_decode($string) !== null;
    }

    /**
     * Convert date format.
     *
     * Use regular expressions to detect dates and convert the detected dates into a MySQL-compatible DATE format.
     *
     * @link https://imelgrat.me/php/date-formatting-detect-convert/
     */
    public static function convertDateFormat($old_date = '')
    {
        $old_date = trim($old_date);

        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $old_date)) // MySQL-compatible YYYY-MM-DD format
        {
            $new_date = $old_date;
        }
        elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/', $old_date)) // DD-MM-YYYY format
        {
            $new_date = substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-' . substr($old_date, 0, 2);
        }
        elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{2}$/', $old_date)) // DD-MM-YY format
        {
            $new_date = substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-20' . substr($old_date, 0, 2);
        }
        elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/', $old_date)) // DD/MM/YYYY format
        {
            $new_date = substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-' . substr($old_date, 0, 2);
        }
        else // Any other format. Set it as an empty date.
        {
            $new_date = '0000-00-00';
        }
        return $new_date;
    }

    public static function renderTwigTemplate($templateFilePath,$vars) {

        $path = realpath($templateFilePath);
        if (!is_file($path)) {
            throw new \Exception($templateFilePath. 'is not a file');
        }

        $dir = dirname($path);
        $file = basename($path);

        $twigLoader = new FilesystemLoader($dir);
        $twig = new Environment($twigLoader);

        $template = $twig->load($file);
        return $template->render($vars);

    }
}