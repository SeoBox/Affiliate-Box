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


$reviews = get_field("reviews", $post_id, false);
if (!empty($reviews)) {
    while (have_rows("reviews", $post_id)) {
        the_row();
        $index = get_row_index();
        $asin = get_sub_field('asin', false);
        $title = get_sub_field('title', true);
        $image = get_sub_field('image', true);
        $checkout = get_sub_field('checkout', true);
        $description = get_sub_field('description', true);
        $pros = get_sub_field('pros', true);
        $cons = get_sub_field('cons', true);
        $specs_lines = get_customizable_sub_field("specs", true, array("structure" => 'lines'));
        $specs_separated = get_customizable_sub_field("specs", true, array("structure" => 'separated'));
        $specs = $specs_lines;

// code executed by eval() automatically starts in PHP mode, so you don't need to (and shouldn't!) prefix it with <?php.
// If you want to emulate the behavior of include() exactly, you can prefix the string to be evaled  to leave PHP mode
        echo eval('?>' . $selected_template);
    }
}
?>
