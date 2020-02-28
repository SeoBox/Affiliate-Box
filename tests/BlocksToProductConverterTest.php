<?php

use PHPUnit\Framework\TestCase;

include_once('public/BlocksToProductConverter.php');


class BlocksToProductConverterTest extends TestCase
{

    public function testConvert()
    {
        $asin = "B01AJJIQQQ";
        $title = "Rockjam Piano";
        $bestCategory = "Best Piano Keyboard for Kids Overall";
        $documents = array(
            array(
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h2><span><a href=\"https://www.amazon.com/dp/B01AJJIQQQ\">$title</a></span><span>&nbsp;— $bestCategory</span></h2>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>The RockJam keyboard has 61 keys</p>"
                ),
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h3><span>Pros</span></h3>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>Great teaching tool.</span></li></ul>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>Cons</span></p>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>The software is complicated for kids.</span></li></ul>"
                )
            ),
            array(
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h2><span><a href=\"https://www.amazon.com/dp/$asin\">$title</a></span><span>&nbsp;— $bestCategory</span></h2>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>The RockJam keyboard has 61 keys</p>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>Pros</span></p>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>Great teaching tool.</span></li></ul>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>Cons</span></p>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>The software is complicated for kids.</span></li></ul>"
                )
            )
        );
        $allExpectedProducts = array(
            array(
                array("asin" => $asin, "title" => $title, "bestCategory"=>$bestCategory)
            ),
            array(
                array("asin" => $asin, "title" => $title, "bestCategory"=>$bestCategory)
            ),
        );

        foreach (array_map(null, $documents, $allExpectedProducts) as list($blocks, $expectedProducts)) {
            $products = BlocksToProductConverter::convert($blocks);
            foreach (array_map(null, $products, $expectedProducts) as list($product, $expectedProduct)) {
                self::assertEquals($product->asin, $expectedProduct['asin']);
                self::assertEquals($product->title, $expectedProduct['title']);
                self::assertEquals($product->bestCategory, $expectedProduct['bestCategory']);
            }
        }
    }
}
