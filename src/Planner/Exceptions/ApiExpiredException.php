<?php
namespace Planner\Exceptions;

class ApiExpiredException extends ApiException {

    public function getResponseHeader() {
        return 'HTTP/1.0 498 Token expired/invalid';
    }

}