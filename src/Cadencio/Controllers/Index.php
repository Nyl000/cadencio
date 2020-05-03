<?php namespace Cadencio\Controllers;

class Index extends AbstractController {

    public function getIndex() {
        return ['message' => 'Api Homeland'];
    }

}