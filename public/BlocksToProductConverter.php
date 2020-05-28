<?php


class BlocksToProductConverter
{
    public static function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public static function getListElements(string $html): array
    {
        $values = array();

        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $points = $dom->getElementsByTagName('ul');
        foreach ($points->item(0)->getElementsByTagName('li') as $points) {
            array_push($values, $points->nodeValue);
        }
        return $values;
    }


    public static function convert($blocks, $parsing_logic = [])
    {
        $products = array();
        $current_product = new ACFProductReviewMeta();
        $prev_block = "";

        foreach ($blocks as $block) {
            $blockName = $block['blockName'];
            if (is_null($blockName)) {
                continue;
            }

            if (!in_array($blockName, array(
                "core/heading",
                "core/list",
                "core/image",
                "core/paragraph",
                "core-embed/youtube",
                "core/image"
            ))) {
                $prev_block = '';
                continue;
            }


            $html = trim($block['innerHTML']);
            $asinTitleMatches = ACFProductReviewMeta::getMatches($html);
            if ($asinTitleMatches and $blockName == "core/heading") {
                if ($current_product->isComplete()) {
                    array_push($products, $current_product);
                }

                $current_product = new ACFProductReviewMeta($parsing_logic);

                // extract asin and title from the link
                $current_product->asin = $asinTitleMatches[1];
                $current_product->title = $asinTitleMatches[2];
                $current_product->assignBestCategory($html);
                $prev_block = "title";
                continue;
            }

            if (!$current_product->hasTitle()) {
//        	skip blocks until we have a title; title indicates that a product review has been started
                continue;
            }

//          You might wonder why trim(html_entity_decode('&nbsp;')); doesn't reduce the string to an empty string,
//          that's because the '&nbsp;' entity is not ASCII code 32 (which is stripped by trim())
//          but ASCII code 160 (0xa0) in the default ISO 8859-1 characterset.
            $stripedHtml = str_replace(array("\x0B", "\xC2", "\xA0"), " ", html_entity_decode(strip_tags($html)));
            # normalize single quote
            $stripedHtml = str_replace("’", "'", $stripedHtml);
//          extend default trim to include :
            $stripedHtml = ucwords(trim($stripedHtml, $charlist = " :\t\n\r\0\x0B"));
            if (in_array($stripedHtml, array("Pros", "Pro", "Advantages", "What We Like"))) {
                $prev_block = "pros";
                continue;
            }

            if (in_array($stripedHtml, array("Cons", "Con", "Shortcomings", "What We Don't Like"))) {
                $prev_block = "cons";
                continue;
            }

            if (in_array($stripedHtml, array("Specs", "Features", "Tech Specs", "Specifications"))) {
                $prev_block = "specs";
                continue;
            }

            if ($blockName == "core/list" && $prev_block == "pros") {
                $current_product->pros = self::getListElements($html);
                $prev_block = "core/list";
                continue;
            }

            if ($blockName == "core/list" && $prev_block == "cons") {
                $current_product->cons = self::getListElements($html);
                $prev_block = "core/list";
                continue;
            }

            if ($blockName == "core/list" && $prev_block == "specs") {
                $current_product->specs = self::getListElements($html);
                $prev_block = "core/list";
                continue;
            }

            if (in_array($stripedHtml, array("Description"))) {
                $prev_block = "desc";
                continue;
            }


//          if we see a header after we checked for specs, cons and pros, it should mean that a product review ended
            if ($blockName == "core/heading") {
                if ($current_product->isComplete()) {
                    array_push($products, $current_product);
                }
                $current_product = new ACFProductReviewMeta();
            }

//	    TODO: figure out how to parse articles with only titles and descriptions.
//      TODO: test  https://youtu.be/l1WyyLPw0ew
            if (in_array($blockName, array("core/paragraph", "core-embed/youtube")) && in_array($prev_block, array("title", "core/list", "desc"))) {
                # embed youtube url
                $pattern = '@(https?://)?(?:www\.)?(youtu(?:\.be/([-\w]+)|be\.com/watch\?v=([-\w]+)))\S*@im';
                if (preg_match($pattern, $html, $matches)) {
                    $url = "https://youtube.com/embed/" . trim($matches[4], '"');
                    $html = "<div class='embed-container'><iframe src='$url' frameborder='0' allowfullscreen></iframe></div>";
                }
                $current_product->description .= $html;
                continue;
            }

            if ($blockName == "core/image" && in_array($prev_block, array("title", "core/list"))) {
                $current_product->description .= $html;
                continue;
            }
        }

        if ($current_product->isComplete()) {
            array_push($products, $current_product);
        }

        return $products;
    }
}