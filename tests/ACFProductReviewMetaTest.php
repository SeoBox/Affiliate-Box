<?php

use PHPUnit\Framework\TestCase;

include_once('public/ACFProductReviewMeta.php');


class ACFProductReviewMetaTest extends TestCase
{
    public function testAsinRegex()
    {
        $links = array(
            "<a href=\"http://www.amazon.com/Kindle-Wireless-Reading-Display-Generation/dp/B0015T963C\">title</a>" => array("B0015T963C", "title"),
            "<a href=\"http://www.amazon.com/dp/B0015T963C\">title</a>" => array("B0015T963C", "title"),
            "<a href=\"https://www.titan.fitness/strength/specialty-machines/upper-body/outdoor-power-tower/400614.html\">Titan</a>" => array("https://www.titan.fitness/strength/specialty-machines/upper-body/outdoor-power-tower/400614.html", "Titan"),
            "<a href=\"http://www.amazon.com/gp/product/B0015T963C\">title</a>" => array("B0015T963C", "title"),
            "<a href=\"http://www.amazon.com/gp/product/glance/B0015T963C\">title</a>" => array("B0015T963C", "title"),
            "<a href=\"https://www.amazon.com/Spacious-5-Person-Multiple-Roll-Back-Backpacking/dp/B07C9XT5MY/ref=mp_s_a_1_2?dchild=1&keywords=Ozark+suv+tent&qid=1580471788&sr=8-2\">title</a>" => array("B07C9XT5MY", "title"),
            "<a href=\"https://www.amazon.com/ALPS-Mountaineering-Lynx-1-Person-Tent/dp/B00BMKD1DU/ref=as_li_ss_tl\" target=\"_blank\" rel=\"noopener noreferrer\">&nbsp;</a>" => array("B00BMKD1DU", "&nbsp;"),
            "<a href=\"https://www.amazon.com/dp/B01N76IL0K/ref=dp_cerb_1\" target=\"_blank\" rel=\"noopener noreferrer\">&nbsp;</a>" => array("B01N76IL0K", "&nbsp;"),
            "<a href=\"https://www.amazon.com/ALPS-Mountaineering-Lynx-1-Person-Tent/dp/B00BMKD1DU/ref=as_li_ss_tl\" target=\"_blank\" rel=\"noopener noreferrer\">ALPS Mountaineering Lynx 1-Person Tent</a>" => array("B00BMKD1DU", "ALPS Mountaineering Lynx 1-Person Tent")
        );

        foreach ($links as $link => $expected) {
            $matches = ACFProductReviewMeta::getMatches($link);
            if (is_array($expected)) {
                list($asin, $title) = $expected;
                self::assertEquals($asin, $matches[1], $link);
                self::assertEquals($title, $matches[2]);
            } else {
                self::assertEquals($expected, $matches[1], $link);
            }
        }
    }

    public function testGetBestCategory()
    {
        $htmls = array(
            "<a href=\"http://www.amazon.com/Kindle-Wireless-Reading-Display-Generation/dp/B0015T963C\">title</a>" => "",
            "<h3>1. <a href=\"https://www.amazon.com/dp/B00S5ETZKY\">Casio Privia PX-860</a> — Top Pick</h3>" => "Top Pick",
            "<h3>1. <a href=\"https://www.amazon.com/dp/B00S5ETZKY\" >Casio</a>&nbsp;— Top Pick</h3>" => "Top Pick",
            "<h3>1. <a href=\"https://www.amazon.com/dp/B00S5ETZKY\" >Casio</a>&nbsp;— Best Any-Genre Piano Under $500</h3>" => "Best Any-Genre Piano Under $500",
            "<h2>1. <a href=\"https://www.amazon.com/dp/B000BK717O\">Bam France 2002XL</a>&nbsp;— Best Any-Genre</h2>" => "Best Any-Genre",
            "<h2><span><a href=\"https://www.amazon.com/dp/B01AJJIQQQ\">RockJam 61 Key Electronic Keyboard Piano</a></span><span>&nbsp;— Best Keyboard</span></h2>" => "Best Keyboard",
            "<h2><span><a href=\"https://www.titan.fitness/strength/specialty-machines/upper-body/outdoor-power-tower/400614.html\">Titan Fitness Outdoor Power Tower</a></span><span>&nbsp;</span><span>- Best Outdoor Power Tower</span></h2>" => "Best Outdoor Power Tower"
        );

        foreach ($htmls as $html => $expected) {
            self::assertEquals($expected, ACFProductReviewMeta::getBestCategory($html));

        }

    }
}