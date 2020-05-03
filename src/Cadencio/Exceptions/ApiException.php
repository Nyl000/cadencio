<?php
namespace Cadencio\Exceptions;

abstract class ApiException extends \Exception {

    public function getResponseHeader() {
        return 'HTTP/1.0 400 Bad Request';
    }

}