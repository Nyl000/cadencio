<?php
namespace Planner\Exceptions;

class ApiForbiddenException extends ApiException {

    public function getResponseHeader() {
        return 'HTTP/1.0 403 Forbidden';
    }

}