<?php

namespace Modules\Plannings\Models;

use Planner\Application;
use Planner\Models\AbstractModel;
use Planner\Models\NotificationModel;

class PlanningEntryModel extends AbstractModel
{

    protected $modelName = 'planning_entry';
    protected $resourceName = 'planning_entry';

    public function getPublicFields()
    {
        return ['planning_entry.id','planning_entry.title','description','id_status','id_planning','date_start','date_end','id_creator','id_assigned_to','IF(f.id_user IS NULL, 0, 1) as followed, status.color, plannings.title as planning_title'];
    }

    public function getSearchField() {
        return ['id','title','description'];
    }

    public function getFilters() {
        return ['id_status','id_planning'];
    }

    public function init() {
        $this->addRelation('planning_entry_followers','id','id_planning_entry','LEFT JOIN','f', ' AND id_user = ?');
        $this->addQueryParameter(Application::$instance->getCurrentUserId());

        $this->addRelation('planning_status','id_status','id','LEFT JOIN','status');
        $this->addRelation('plannings','id_planning','id','LEFT JOIN');

    }

    public function createOrUpdate($datas)
    {
        $datas = (array)$datas;
        $old = (!isset($datas['id']) || empty($datas['id'])) ? null : $this->getOne($datas['id']);

        if (!$old) {
            $datas['id_creator'] = Application::$instance->getCurrentUserId();
            $return = parent::createOrUpdate($datas);
            $this->addFollower($return, $datas['id_creator']);
        }

        else {
            $return = parent::createOrUpdate($datas);

        }
        $datas['id'] = $return;
        
        $this->notifyEntryWatchers($datas,$old);

        return $return;
    }

    public function patch($id, $datas, $uniqueFieldname = 'id')
    {

        $old =  $this->getOne($id , $uniqueFieldname);
        parent::patch($id, $datas, $uniqueFieldname);
        $new =  $this->getOne($id , $uniqueFieldname);

        $this->notifyEntryWatchers($new,$old);

    }

    public function getByPlanningQuery($options, $countOnly = false)
    {
        if (!isset($options['id_planning'])) { throw new \RuntimeException(('No planning ID Given'));}

        $queryParts = $this->prepareQuery($options, $countOnly);

        $queryParts['where'] .= ' AND id_planning = ? ';
        $queryParts['params'][] = $options['id_planning'];

        return [
            'query' => $queryParts['select'] . $queryParts['from'] . $queryParts['where'] . $queryParts['order'] . $queryParts['paging'],
            'params' => $queryParts['params']
        ];
    }

    public function addFollower($idEntry, $idUser ) {
        $this->getAdapter()->query('INSERT IGNORE INTO planning_entry_followers(id_planning_entry,id_user) VALUES(?,?) ', [$idEntry,$idUser]);
    }

    public function removeFollower($idEntry,$idUser) {
        $this->getAdapter()->query('DELETE FROM planning_entry_followers WHERE id_planning_entry = ? AND id_user = ?', [$idEntry,$idUser]);
    }

    public function userFollowEntry($idEntry,$idUser) {
        $count = $this->getAdapter()->fetchOne('SELECT count(*) FROM planning_entry_followers WHERE id_planning_entry = ? AND id_user = ?', [$idEntry,$idUser]);
        return $count > 0;
    }

    public function userOwnEntry($idEntry,$idUser) {
        $count = $this->getAdapter()->fetchOne('SELECT count(id) FROM planning_entry WHERE id_creator = ? and id = ?', [$idUser,$idEntry]);
        return $count > 0;
    }

    public function translateField($fieldname) {

        switch ($fieldname) {
            case 'title' :
                return 'Title';
            case 'description' :
                return 'Description';
            case 'id_status' :
                return 'Status';
            case 'id_planning' :
                return 'Project';
            case 'date_start':
                return 'Start Date';
            case 'date_end' :
                return 'Deadline' ;
            case 'id_creator':
                return 'Creator';
            case 'id_assigned_to':
                return 'Assigned to';
            default:
                return $fieldname;
        }

    }

    public function getEntryHtml($entryArray) {

        $content = '<p>';

        unset($entryArray['id']);
        unset($entryArray['color']);
        unset($entryArray['followed']);
        unset($entryArray['planning_title']);

        foreach ($entryArray as $key => $val) {

            if ($key == 'id_status') {
                $val = $this->getAdapter()->fetchOne('SELECT title FROM planning_status WHERE id = ? ', [$val]);
            }
            if ($key == 'id_planning') {
                $val = $this->getAdapter()->fetchOne('SELECT title FROM plannings WHERE id = ? ', [$val]);
            }
            if ($key == 'id_creator') {
                $val = $this->getAdapter()->fetchOne('SELECT email FROM users WHERE id = ? ', [$val]);
            }
            if ($key == 'id_assigned_to') {
                $val = $this->getAdapter()->fetchOne('SELECT email FROM users WHERE id = ? ', [$val]);
            }
            $content .= '<strong>'.$this->translateField($key).' : </strong>'.$val.' <br />';
        }

        $content .='</p>';

        return $content;


    }

    public function getAllByPlanning($idPlanning,$options = []) {

        $select = '
            SELECT planning_entry.*, planning_status.title as status_name, planning_status.color as color, IF(f.id_user IS NULL, 0, 1) as followed
            FROM planning_entry 
            JOIN planning_status on planning_status.id = id_status 
            LEFT JOIN planning_entry_followers as f ON f.id_planning_entry = planning_entry.id AND id_user = ?
            
            WHERE ';
        $whereParams = [Application::$instance->getCurrentUserId()];

        $where = 'id_planning = ? AND date_start IS NOT NULL AND date_end IS NOT NULL AND date_start > \'0000-00-00 00:00:00\' AND date_end > \'0000-00-00 00:00:00\'';
        $whereParams[]  = $idPlanning;

        if (isset($options['id_status'])) {

            if (is_array($options['id_status'])) {
                $subwhere = ' AND ( 1=0 ';
                foreach ($options['id_status'] as $option) {
                    $subwhere .= ' OR id_status = ? ';
                    $whereParams[] = $option;

                }
                $subwhere .= ')';
                $where .= $subwhere;
            }
            else {
                $where.= ' AND id_status = ?';
                $whereParams[] = $options['id_status'];
            }
        }
        $select .= $where;
        $select .= ' ORDER BY date_start ASC';

        return $this->getAdapter()->fetchAll($select,$whereParams);
    }

    public function getFirstDateInPlanning($idPlanning,$options=[]) {
        $select = 'SELECT date_start FROM '.$this->modelName.' WHERE 1=1';
        $whereParams = [];

        $where = ' AND id_planning = ? AND date_start IS NOT NULL AND date_end IS NOT NULL AND date_start > \'0000-00-00 00:00:00\' AND date_end > \'0000-00-00 00:00:00\'';
        $whereParams[] = $idPlanning;

        if (isset($options['id_status'])) {

            if (is_array($options['id_status'])) {
                $subwhere = ' AND ( 1=0 ';
                foreach ($options['id_status'] as $option) {
                    $subwhere .= ' OR id_status = ? ';
                    $whereParams[] = $option;

                }
                $subwhere .= ')';
                $where .= $subwhere;
            }
            else {
                $where.= ' AND id_status = ?';
                $whereParams[] = $options['id_status'];
            }
        }

        $select.=$where;
        $select.='ORDER BY date_start ASC LIMIT 0, 1';

        return $this->getAdapter()->fetchOne($select, $whereParams);
    }

    public function getLastDateInPlanning($idPlanning,$options = []) {
        $select = 'SELECT date_end FROM '.$this->modelName.' WHERE 1=1';
        $whereParams = [];

        $where = ' AND id_planning = ? AND date_start IS NOT NULL AND date_end IS NOT NULL AND date_start > \'0000-00-00 00:00:00\' AND date_end > \'0000-00-00 00:00:00\'';
        $whereParams[] = $idPlanning;

        if (isset($options['id_status'])) {

            if (is_array($options['id_status'])) {
                $subwhere = ' AND ( 1=0 ';
                foreach ($options['id_status'] as $option) {
                    $subwhere .= ' OR id_status = ? ';
                    $whereParams[] = $option;

                }
                $subwhere .= ')';
                $where .= $subwhere;
            }
            else {
                $where.= ' AND id_status = ?';
                $whereParams[] = $options['id_status'];
            }
        }

        $select.=$where;
        $select.='ORDER BY date_end DESC LIMIT 0, 1';

        return $this->getAdapter()->fetchOne($select, $whereParams);

    }

    private function notifyEntryWatchers($newEntry,$oldEntry = null) {

        $notificationModel = new NotificationModel();

        $followers = $this->getAdapter()->fetchAll('SELECT id_user FROM planning_entry_followers WHERE id_planning_entry = ?', [$newEntry['id']]);

        foreach($followers as $follower) {

            $currentUserId = Application::$instance->getCurrentUserId();

            //Don't notify logged user
            if ($currentUserId != $follower['id_user']) {
                if (empty($oldEntry)) {
                    //New entity
                    $data = [
                        'title' => 'New planning entry : ' . $newEntry['title'],
                        'content' => '<div><h2>New entry</h2> ' . $this->getEntryHtml($newEntry) . '</div>',
                        'date' => date('Y-m-d H:i:m'),
                        'id_user' => $follower['id_user'],
                        'send_email' => true
                    ];

                } else {
                    $data = [
                        'title' => 'Planning entry updated : ' . $newEntry['title'],
                        'content' => '
                                <div><h2>Old entry</h2> ' . $this->getEntryHtml($oldEntry) . '</div>
                                <div><h2>New entry</h2> ' . $this->getEntryHtml($newEntry) . '</div>
                                ',
                        'date' => date('Y-m-d H:i:m'),
                        'id_user' => $follower['id_user'],
                        'send_email' => true
                    ];
                }
            }
            $notificationModel->createOrUpdate($data);

        }
    }



}