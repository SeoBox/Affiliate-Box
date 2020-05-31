<?php

use Aepro\Aepro;
use Aepro\Classes\AcfMaster;
use Elementor\Controls_Manager;
use Elementor\Widget_Button;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Repeater_Button extends Widget_Button
{

    public function get_id()
    {
        return 'aff-box-repeater-button';
    }

    public function get_title()
    {
        return __('AB - ACF Repeater Button', 'ab-acf-repeater-button');
    }

    public function get_settings_for_display($setting_key = null)
    {
        $settings = parent::get_settings_for_display($setting_key);

        $field_args = [
            'field_name' => $settings['text_field_name'],
            'field_type' => 'post',
            'is_sub_field' => 'repeater',
            'parent_field' => $settings['parent_field'],
        ];

        # override button text
        $button_text = AcfMaster::instance()->get_field_value($field_args);
        if (!empty($button_text)) {
            $settings['text'] = $button_text;
        }

        $field_args = [
            'field_name' => $settings['link_field_name'],
            'field_type' => 'post',
            'is_sub_field' => 'repeater',
            'parent_field' => $settings['parent_field'],
        ];

        # override button link
        $button_link = AcfMaster::instance()->get_field_value($field_args);
        if (!empty($button_link)) {
            $settings['link']['url'] = $button_link;
        }

        return $settings;
    }

    protected function _register_controls()
    {
        $this->start_controls_section('general', [
            'label' => __('General', 'ae-pro')
        ]);

        $this->add_control(
            'field_type',
            [
                'label' => __('Source', 'ae-pro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'post' => __('Post Field', 'ae-pro'),
                    'term' => __('Term Field', 'ae-pro'),
                    'user' => __('User', 'ae-pro'),
                    'option' => __('Option', 'ae-pro')
                ],
                'default' => 'post'
            ]
        );

        $this->add_control(
            'parent_field',
            [
                'label' => __('Parent Field', 'ae-pro'),
                'type' => Controls_Manager::TEXT,
            ]
        );


        $this->add_control(
            'text_field_name',
            [
                'label' => __('Text Field', 'ae-pro'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'Enter your acf field name',
            ]
        );


        $this->add_control(
            'link_field_name',
            [
                'label' => __('Link Field', 'ae-pro'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'Enter your acf field name',
            ]
        );


        $this->end_controls_section();

        parent::_register_controls();
    }


}

