<?php

namespace Cadencio\Models;

use Cadencio\Application;
use Cadencio\Services\Utils;
use SendGrid\Mail\Mail;

class LogModel extends AbstractModel
{

    protected $modelName = 'logs';
    protected $resourceName = 'logs';

    public function init()
    {
        $this->addRelation('users', 'id_user','id','LEFT JOIN');
    }

    public function getPublicFields()
    {
        return array_merge(['users.email'],  parent::getPublicFields());
    }

    public function getSearchField() {
        return ['id','type','comment'];
    }




}

