<?php
$template_name = $block['data']['template_name'];
$product_review_templates = get_field('product_review_templates', 'option');
$selected_template = null;
foreach ($product_review_templates as $template) {
    if ($template_name == $template['name']) {
        $selected_template = $template['template'];
        break;
    }
}

if (!$selected_template) {
    return;
}

if (!function_exists("get_customizable_sub_field_object")) {

    /**
     * Improved version of get_sub_field_object. Added override parameter to customize formatting of a subfield.
     */
    function get_customizable_sub_field_object($selector, $format_value = true, $override = array())
    {

        // vars
        $row = acf_get_loop('active');


        // bail early if no row
        if (!$row) return false;


        // attempt to find sub field
        $sub_field = get_row_sub_field($selector);


        // bail early if no sub field
        if (!$sub_field) return false;

        $sub_field['value'] = get_row_sub_value($sub_field['key']);

        if (!empty($override)) {
            $sub_field = array_merge($sub_field, $override);
        }


        // format value
        if ($format_value) {

            // get value for field
            $sub_field['value'] = acf_format_value($sub_field['value'], $row['post_id'], $sub_field);

        }


        // return
        return $sub_field;

    }
}

if (!function_exists("get_customizable_sub_field")) {

    /**
     * Improved version of get_sub_field. Added override parameter to customize formatting of a subfield.
     */
    function get_customizable_sub_field($selector = '', $format_value = true, $override = array())
    {

        // get sub field
        $sub_field = get_customizable_sub_field_object($selector, $format_value, $override);


        // bail early if no sub field
        if (!$sub_field) return false;


        // return
        return $sub_field['value'];

    }
}


$reviews = get_field("reviews", $post_id, false);

if (!function_exists('clean_field_value_cache')) {
    /**
     * ACF has a cache of formatted values.
     * @param $post_id
     */
    function clean_field_value_cache($post_id)
    {
        $specs_field_name = get_row_sub_field("specs")['name'];
        $store = acf_get_store('values');

        if ($store->has("$post_id:$specs_field_name:formatted")) {
            $store->remove("$post_id:$specs_field_name:formatted");
        }
    }
}

if (!empty($reviews)) {
    $products = get_field("template_products");

    while (have_rows("reviews", $post_id)) {
        the_row();
        $index = get_row_index();
        $asin = get_sub_field('asin', false);
        $title = get_sub_field('title', true);
        $title_text = get_sub_field('title', false);

        if (is_string($products) && $products && $products != "All" && $products != $title_text) {
            continue;
        } else if (is_array($products) && $products && !in_array("All", $products) && !in_array($title_text, $products)) {
            continue;
        }

        $best_category = get_sub_field('best_category', true);
        $image = get_sub_field('image', true);
        $checkout = get_sub_field('checkout', true);
        $description = get_sub_field('description', true);
        $short_description = get_sub_field('short_description', true);
        $special_offer = get_sub_field('special_offer', true);
        $best_category_icon_url = get_sub_field('best_category_icon', true);
        $affiliate_links = get_sub_field('affiliate_links', true);
        $ratings = get_sub_field('ratings', true);
        $pros = get_sub_field('pros', true);
        $cons = get_sub_field('cons', true);
        $review_link = get_sub_field('review_link', true);
        $review_anchor = get_sub_field('review_anchor', true);
        $review_anchor = str_replace("$.title", $title_text, $review_anchor);
        $price = get_sub_field('price', true);

        $specs_lines = get_customizable_sub_field("specs", true, array("structure" => 'lines'));
        clean_field_value_cache($post_id);
        $specs_separated = get_customizable_sub_field("specs", true, array("structure" => 'separated'));
        $specs = $specs_lines;
        $specs_plain = explode("\n", get_sub_field('specs', false));

        $pros_color = get_field('pros_settings', 'option')['pros_features_color'] ?? '#000';
        $cons_color = get_field('cons_settings', 'option')['cons_features_color'] ?? '#000';
        $specs_color = get_field('specs_settings', 'option')['specs_color'] ?? '#000';
        $specs_icon = get_field('specs_settings', 'option')['specs_icon'] ?? '';


// code executed by eval() automatically starts in PHP mode, so you don't need to (and shouldn't!) prefix it with <?php.
// If you want to emulate the behavior of include() exactly, you can prefix the string to be evaled  to leave PHP mode
        echo eval('?>' . $selected_template);
    }
}
?>

