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
        $description = "<p><span>The RockJam keyboard has 61 keys</span></p>";
        $pro = "Great teaching tool.";
        $con = "The software is complicated for kids.";
        $documents = array(
            array(
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h3><span><a href=\"https://www.amazon.com/dp/B01AJJIQQQ\">$title</a></span><span>&nbsp;— $bestCategory</span></h3>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "$description"
                ),
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h4><span>Pros</span></h4>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>$pro</span></li></ul>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>What we don't like</span></p>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>$con</span></li></ul>"
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
                    "innerHTML" => "<ul><li><span>$pro</span></li></ul>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<p><span>CONS</span></p>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>$con</span></li></ul>"
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
                    "innerHTML" => "<ul><li><span>$pro</span></li></ul>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "<h4><span>What We Don’t Like</span></h4>"
                ),
                array(
                    "blockName" => "core/list",
                    "innerHTML" => "<ul><li><span>$con</span></li></ul>"
                ),
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h3><span>Description</span></h3>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "$description"
                ),
            ),
            array(
                array(
                    "blockName" => "core/heading",
                    "innerHTML" => "<h3 id=\"tab-con-2\"><a href=\"https://www.amazon.com/dp/$asin\">$title</a></h3>"
                ),
                array(
                    "blockName" => "core/paragraph",
                    "innerHTML" => "$description"
                ),
            )
        );
        $allExpectedProducts = array(
            array(
                array("asin" => $asin, "title" => $title, "bestCategory" => $bestCategory, "description" => $description, 'pros' => array($pro), 'cons' => array($con))
            ),
            array(
                array("asin" => $asin, "title" => $title, "bestCategory" => $bestCategory, "description" => $description, 'pros' => array($pro), 'cons' => array($con))
            ),
            array(
                array("asin" => $asin, "title" => $title, "bestCategory" => $bestCategory, "description" => $description, 'pros' => array($pro), 'cons' => array($con))
            ),
            array(
                array("asin" => $asin, "title" => $title, "bestCategory" => null, "description" => $description, 'pros' => [], 'cons' => [])
            ),
        );

        $parsingLogics = array(
            [],
            [],
            [],
            array("description" => true, "pros_cons" => false, "features" => false)
        );


        foreach (array_map(null, $documents, $allExpectedProducts, $parsingLogics) as list($blocks, $expectedProducts, $parsingLogic)) {
            $products = BlocksToProductConverter::convert($blocks, $parsingLogic);
            foreach (array_map(null, $products, $expectedProducts) as list($product, $expectedProduct)) {
                self::assertEquals($product->asin, $expectedProduct['asin']);
                self::assertEquals($product->title, $expectedProduct['title']);
                self::assertEquals($product->bestCategory, $expectedProduct['bestCategory']);
                self::assertEquals($product->description, $expectedProduct['description']);
                self::assertEquals($product->pros, $expectedProduct['pros']);
            }
        }
    }
}
