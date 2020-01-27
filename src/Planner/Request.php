<?php
namespace Planner;

class Request {

    protected $post;
    protected $urlParams;
    protected $body;

    public function __construct() {

        $this->post = (object) $_POST;
        $this->urlParams = (object) $_GET;
        $this->body = file_get_contents('php://input');
    }

    public function getPost() {
        return $this->post;
    }

    public function getUrlParams() {
        return $this->urlParams;
    }

    public function getBody() {
        return $this->body;
    }

    public function getJsonBody() {
        return json_decode($this->body);
    }

}