<?php

namespace Cadencio\Models;

use Cadencio\Models\AbstractModel;


abstract class AbstractReadableModel extends AbstractModel
{

    public function massEdit($idArray, $key,$value) {
       $this->throwReadableError();
    }

    public function createOrUpdate($datas)
    {
        $this->throwReadableError();
    }

    public function patch($id, $datas, $uniqueFieldname = 'id') {
        $this->throwReadableError();

    }

    public function delete($id) {
        $this->throwReadableError();
    }

    private function throwReadableError() {
        throw new \Exception('This is a readable Model, writing is not allowed here');
    }

}