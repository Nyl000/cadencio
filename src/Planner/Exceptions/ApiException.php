<?php
namespace Planner\Exceptions;

abstract class ApiException extends \Exception {

    public function getResponseHeader() {
        return 'HTTP/1.0 400 Bad Request';
    }

}