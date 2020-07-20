<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('affiliate_box_field_template_products')) :


    class affiliate_box_field_template_products extends acf_field
    {
        function __construct()
        {

            /*
            *  name (string) Single word, no spaces. Underscores allowed
            */

            $this->name = 'template_products';


            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */

            $this->label = __('Review Templates', 'acf-template-products');


            /*
            *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
            */

            $this->category = 'Product Review';


            /*
            *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
            */

            $this->defaults = array();


            // do not delete!
            parent::__construct();

        }

        function render_field($field)
        {
            $post_id = $_POST['post_id'];
            $choices = array('All' => "All");
            while (have_rows("reviews", $post_id)) {
                the_row();
                $title_text = get_sub_field('title', false);
                $choices[$title_text] = $title_text;
            }

            // vars
            $select = array(
                'id' => $field['id'],
                'class' => $field['class'],
                'name' => $field['name'],
            );


            $select['value'] = $field['value'];
            $select['choices'] = $choices;

            acf_select_input($select);
        }
    }


// initialize
    new affiliate_box_field_template_products();


// class_exists check
endif;

?>