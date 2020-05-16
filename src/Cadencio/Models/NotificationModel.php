<?php

namespace Cadencio\Models;

use Cadencio\Application;
use Cadencio\Services\Utils;
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

            $userMail = $this->getAdapter()->fetchOne('SELECT email FROM users WHERE id = ?', [$datas['id_user']]);

            try {
                Utils::sendMail($userMail , $datas['title'], $datas['content'] );
            } catch (Exception $e) {
                error_log('Caught exception: '.  $e->getMessage());
            }
        }

        return $return;

    }




}

