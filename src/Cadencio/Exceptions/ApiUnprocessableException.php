<?php
namespace Cadencio\Exceptions;

class ApiUnprocessableException extends ApiException {

    public function getResponseHeader() {
        return 'HTTP/1.0 422 Unprocessable Entity';
    }

}