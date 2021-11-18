<?php namespace Cadencio\Controllers;

class Index extends AbstractController {

    public function getIndex() : array {
        return ['message' => 'Api Homeland'];
    }

}