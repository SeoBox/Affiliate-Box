<?php

class ACFProductReviewMeta
{
    public static $asinRegex = "/<a href=\".*amazon\.com.*?(?:[\/dp\/]|$)([A-Z0-9]{10}).*?>(.*?)(<br.*>)*<\/a>/";
    public static $linkRegex = "/<a href=\"(.*?)\".*?>(.*?)(<br.*>)*<\/a>/";
    public static $bestCategoryRegex = "/<a.*<\/a>\s*\-\s*([$\w -]+)/";
    public $asin;
    public $title;
    public $pros = array();
    public $cons = array();
    public $specs = array();
    public $bestCategory = '';
    public $description = '';
    private $parsing_logic;

    /**
     * ACFProductReviewMeta constructor.
     * @param array $parsing_logic array consisting of 3 elements: description, pros_cons, features
     */
    public function __construct($parsing_logic = [])
    {
        $this->parsing_logic = $parsing_logic;
    }


    public function isComplete()
    {
        $pros_cons_exist = !empty($this->pros) && !empty($this->cons);

        if ($this->parsing_logic) {
            $require_description = $this->parsing_logic['description'];
            $require_pros_cons = $this->parsing_logic['pros_cons'];
            $require_features = $this->parsing_logic['features'];

            $parsing_logic_condition = True;
            if ($require_description) {
                $parsing_logic_condition = $parsing_logic_condition && isset($this->description);
            }

            if ($require_pros_cons) {
                $parsing_logic_condition = $parsing_logic_condition && $pros_cons_exist;
            }

            if ($require_features) {
                $parsing_logic_condition = $parsing_logic_condition && isset($this->specs);
            }

            return isset($this->asin) && isset($this->title) && $parsing_logic_condition;
        }


        return isset($this->asin) && isset($this->title) && isset($this->description) && (!empty($this->specs) || $pros_cons_exist);
    }

    public function hasTitle()
    {
        return isset($this->title);
    }

    public static function getMatches(string $html)
    {
        # check for an amazon product and return matches if they are present
        preg_match(ACFProductReviewMeta::$asinRegex, $html, $matches);
        if ($matches) {
            return $matches;
        }

        preg_match(ACFProductReviewMeta::$linkRegex, $html, $matches);
        return $matches;
    }

    public function assignBestCategory(string $html)
    {
        $this->bestCategory = ACFProductReviewMeta::getBestCategory($html);
        return $this->bestCategory;
    }

    public static function getBestCategory(string $html)
    {
        $html = str_replace(array("\x0B", "\xC2", "\xA0"), " ", html_entity_decode($html));
        # normalize long dash to an ascii dash
        $html = str_replace(array("—"), "-", $html);
        $html = strip_tags($html, "<a>");
        preg_match(ACFProductReviewMeta::$bestCategoryRegex, $html, $matches);
        if ($matches and sizeof($matches) == 2) {
            return $matches[1];
        }
        return "";
    }
}