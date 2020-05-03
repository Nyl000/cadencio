<?php
namespace Cadencio\Exceptions;

class ApiNotFoundException extends ApiException {

    public function getResponseHeader() {
        return 'HTTP/1.0 404 Not found';
    }

}