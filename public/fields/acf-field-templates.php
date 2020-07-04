<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('affiliate_box_field_review_templates')) :


    class affiliate_box_field_review_templates extends acf_field
    {
        function __construct()
        {

            /*
            *  name (string) Single word, no spaces. Underscores allowed
            */

            $this->name = 'templates';


            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */

            $this->label = __('Review Templates', 'acf-review-template');


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
            $product_review_templates = get_field('product_review_templates', 'option');
            $choices = [];
            foreach ($product_review_templates as $template) {
                $choices[$template['name']] = $template['name'];
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
    new affiliate_box_field_review_templates();


// class_exists check
endif;

?>