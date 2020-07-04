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

$reviews = get_field("reviews", $post_id, false);
if (!empty($reviews)) {
    while (have_rows("reviews", $post_id)) {
        the_row();
        $asin = get_sub_field('asin', false);
        $title = get_sub_field('title', true);
        $image = get_sub_field('image', true);
        $checkout = get_sub_field('checkout', true);
        $description = get_sub_field('description', true);
        $pros = get_sub_field('pros', true);
        $cons = get_sub_field('cons', true);

        $pros_color = get_field("pros_settings", 'option')["pros_features_color"] ?? '';
        $pros_icon = get_field("pros_settings", 'option')["pros_icon"] ?? '';


// code executed by eval() automatically starts in PHP mode, so you don't need to (and shouldn't!) prefix it with <?php.
// If you want to emulate the behavior of include() exactly, you can prefix the string to be evaled  to leave PHP mode
        echo eval('?>' . $selected_template);
    }
}
?>
