<?php

namespace Cadencio\Models;

use Cadencio\Application;
use SendGrid\Mail\Mail;

class NotificationModel extends AbstractModel
{

    protected $modelName = 'notifications';
    protected $resourceName = 'notifications';

    public function getPublicFields()
    {
        return ['id','date','id_user','title','content','seen'];
    }

    public function getSearchField() {
        return ['id','title','content'];
    }

    public function getMyUnseen() {
        return $this->getAdapter()->fetchAll('SELECT * FROM '.$this->modelName.' WHERE seen = 0 AND id_user = ?', [Application::$instance->getCurrentUserId()]);
    }

    public function createOrUpdate($datas)
    {
        if (is_object($datas)) {
            $datas = (array) $datas;
        }

        $return = parent::createOrUpdate($datas);

        if (isset($datas['send_email']) && $datas['send_email']) {

            $email = new Mail();
            $email->setFrom("notification@cadencio.com", "Cadencio Notification");
            $email->setSubject($datas['title']);

            $userMail = $this->getAdapter()->fetchOne('SELECT email FROM users WHERE id = ?', [$datas['id_user']]);
            $email->addTo($userMail);
            $email->addContent(
                "text/plain",  strip_tags($datas['content'])
            );
            $email->addContent(
                "text/html",  $datas['content']
            );
            $sendgrid = new \SendGrid(SENGRID_KEY);
            try {
                $response = $sendgrid->send($email);
            } catch (Exception $e) {
                error_log('Caught exception: '.  $e->getMessage());
            }
        }

        return $return;

    }




}

