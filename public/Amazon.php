<?php


class Amazon
{

    public static function get_amazon_url($asin)
    {
        $affiliate_settings = get_field('amazon_affiliate_settings', 'option');
        $tag = $affiliate_settings['associate_id'] ?? '';

        return "http://www.amazon.com/dp/" . $asin . "/ref=nosim?tag=" . $tag;
    }

}