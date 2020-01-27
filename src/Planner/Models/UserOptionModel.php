<?php

namespace Planner\Models;

class UserOptionModel extends AbstractModel
{

    protected $modelName = 'users_options';
    protected $resourceName = 'users';

    public function getPublicFields()
    {
        return ['id','key','value'];
    }

    public function getSearchField() {
        return ['id','key'];
    }

    public function getByUser($id_user) {
        return $this->getAdapter()->fetchPairs('SELECT `key`,`value` FROM '.$this->modelName.' WHERE id_user = ?', [$id_user]);
    }
}