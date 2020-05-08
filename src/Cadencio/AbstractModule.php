<?php
namespace Cadencio;

use Cadencio\Adapters\MysqlAwareTrait;
use Cadencio\Services\HookHandler;

abstract class AbstractModule {

    use MysqlAwareTrait;

    private $name;

    public function __construct() {

        HookHandler::getInstance()->setHook('register_module', function () {
            return [$this->getName() => $this];
        });
    }

    protected function getDbVersion() {
        return $this->getAdapter()->fetchOne('SELECT db_version FROM modules WHERE name = ?', [$this->getName()]);
    }

    protected function incrementDbVersion() {
        $this->getAdapter()->query('UPDATE modules SET db_version = db_version + 1  WHERE name = ?', [$this->getName()]);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }


    public function onActivate() {

    }

}


