<?php
namespace Cadencio\Exceptions;

class ApiRuntimeException extends ApiException {

    public function getResponseHeader() {
        return 'HTTP/1.0 500 Internal Server Error';
    }

}