<?php

class ACFProductReviewMeta
{
    public static $asinRegex = "/<a href=\".*amazon\.com.*?(?:[\/dp\/]|$)([A-Z0-9]{10}).*?>(.*?)(<br.*>)*<\/a>/";
    public static $bestCategoryRegex = "/<a.*<\/a>\s*\—\s*([\w ]+)/";
    public $asin;
    public $title;
    public $pros = array();
    public $cons = array();
    public $specs = array();
    public $bestCategory = '';
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
        preg_match(ACFProductReviewMeta::$asinRegex, $html, $matches);
        return $matches;
    }

    public function assignBestCategory(string $html)
    {
        $this->bestCategory = ACFProductReviewMeta::getBestCategory($html);
        return $this->bestCategory;
    }

    public static function getBestCategory(string $html)
    {
        preg_match(ACFProductReviewMeta::$bestCategoryRegex, $html, $matches);
        if ($matches and sizeof($matches) == 2) {
            return $matches[1];
        }
        return "";
    }
}