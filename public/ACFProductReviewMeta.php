<?php

class ACFProductReviewMeta
{
    public static $asin_regex = "/<a href=\".*amazon\.com.*?(?:[\/dp\/]|$)([A-Z0-9]{10}).*?>(.*?)(<br.*>)*<\/a>/";
    public $asin;
    public $title;
    public $pros = array();
    public $cons = array();
    public $specs = array();
    public $description = '';

    public function isComplete()
    {
        $pros_cons_exist = !empty($this->pros) && !empty($this->cons);

        return isset($this->asin) && isset($this->title) && isset($this->description) && (!empty($this->specs) || $pros_cons_exist);
    }

    public function hasTitle()
    {
        return isset($this->title);
    }

    public static function getMatches(string $html)
    {
        preg_match(ACFProductReviewMeta::$asin_regex, $html, $matches);
        return $matches;
    }

}