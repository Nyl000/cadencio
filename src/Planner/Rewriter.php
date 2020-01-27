<?php
namespace Planner;

class Rewriter {

    protected $queryString;
    protected $parsedUrl;
    protected $rewriteRules = array();

    public function __construct() {

        $this->queryString = $_SERVER['REQUEST_URI'];
        $this->parsedUrl = parse_url($this->queryString);
    }

    public function addRewrite($regex, $url) {

        $this->rewriteRules[] = array(
            'regex' => $regex,
            'url' => $url
        );

    }

    public function rewrite() {
        foreach ($this->rewriteRules as $rule) {

            $url = preg_replace('#'.$rule['regex'].'#', BASE_URL.$rule['url'],$this->queryString);
            if (preg_match('#'.$rule['regex'].'#',$this->queryString)){
                $this->parsedUrl = parse_url($url);
                parse_str($this->parsedUrl['query'],$this->parsedUrl['query']);
                return;
            }
        }
        //if nothing is caught by rewriter, it's a 404.
        header("HTTP/1.0 404 Not Found");
        die();

    }

    public function getParsedUrl() {
        return $this->parsedUrl;
    }

}