<?php

namespace Planner\Models;

use Planner\Application;
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


    public function notifyEntryWatchers($newEntry,$oldEntry = null) {

        $entryModel = new PlanningEntryModel();

        $followers = $this->getAdapter()->fetchAll('SELECT id_user FROM planning_entry_followers WHERE id_planning_entry = ?', [$newEntry['id']]);

        foreach($followers as $follower) {

            $currentUserId = Application::$instance->getCurrentUserId();

            //Don't notify logged user
            if ($currentUserId != $follower['id_user']) {
                if (empty($oldEntry)) {
                    //New entity
                    $data = [
                        'title' => 'New planning entry : ' . $newEntry['title'],
                        'content' => '<div><h2>New entry</h2> ' . $entryModel->getEntryHtml($newEntry) . '</div>',
                        'date' => date('Y-m-d H:i:m'),
                        'id_user' => $follower['id_user'],
                        'send_email' => true
                    ];

                } else {
                    $data = [
                        'title' => 'Planning entry updated : ' . $newEntry['title'],
                        'content' => '
                                <div><h2>Old entry</h2> ' . $entryModel->getEntryHtml($oldEntry) . '</div>
                                <div><h2>New entry</h2> ' . $entryModel->getEntryHtml($newEntry) . '</div>
                                ',
                        'date' => date('Y-m-d H:i:m'),
                        'id_user' => $follower['id_user'],
                        'send_email' => true
                    ];
                }
            }
            $this->createOrUpdate($data);

        }
    }


}

