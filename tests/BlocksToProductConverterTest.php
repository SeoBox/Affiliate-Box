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
        $description = "<p>The RockJam keyboard has 61 keys</p>";
        $documents = array(
            array(
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h2><span><a href=\"https://www.amazon.com/dp/B01AJJIQQQ\">$title</a></span><span>&nbsp;— $bestCategory</span></h2>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "$description"
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
                    "innerHTML" => "<p><span>What we don't like</span></p>"
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
                    "innerHTML" => "$description"
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
                ),
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h3><span>Usage</span></h3>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>If you’re a family looking for ...</span></p>"
                ),
            ),

            array(
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h3><span><a href=\"https://www.amazon.com/dp/$asin\">$title</a></span><span>&nbsp;- $bestCategory</span></h3>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>What We Like:</span></p>"
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
                ),
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h3><span>Description</span></h3>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "$description"
                ),
            )
        );
        $allExpectedProducts = array(
            array(
                array("asin" => $asin, "title" => $title, "bestCategory" => $bestCategory, "description" => $description)
            ),
            array(
                array("asin" => $asin, "title" => $title, "bestCategory" => $bestCategory, "description" => $description)
            ),
            array(
                array("asin" => $asin, "title" => $title, "bestCategory" => $bestCategory, "description" => $description)
            ),
        );

        foreach (array_map(null, $documents, $allExpectedProducts) as list($blocks, $expectedProducts)) {
            $products = BlocksToProductConverter::convert($blocks);
            foreach (array_map(null, $products, $expectedProducts) as list($product, $expectedProduct)) {
                self::assertEquals($product->asin, $expectedProduct['asin']);
                self::assertEquals($product->title, $expectedProduct['title']);
                self::assertEquals($product->bestCategory, $expectedProduct['bestCategory']);
                self::assertEquals($product->description, $expectedProduct['description']);
            }
        }
    }
}
