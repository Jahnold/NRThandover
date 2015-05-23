<?php

function tbs_atts_to_string($attributes) {

    $atts = '';

    if (count($attributes) > 0 ) {
        foreach ($attributes as $key => $val)
        {
            $atts .= ' '.$key.'="'.$val.'"';
        }
    }

    return $atts;
}

/**
 * Checkbox Field
 *
 * Extends the checkbox field from CI with the necessary HTML-Markup for Twitter Bootstrap.
 * Added one parameter for the label. Otherwise use it the same way as form_checkbox()
 *
 * @access public
 * @param mixed
 * @param string
 * @param string
 * @param bool
 * @param string
 * @return string
 */
if ( ! function_exists('tbs_form_checkbox') )
{
    function tbs_form_checkbox($data = '', $label = '', $value = '', $checked = FALSE, $extra = '')
    {

        $tpl = '<div class="checkbox"><label>'
            .'%1$s' // will be replaced by the input
            .'%2$s' // will be replaced by the label
            .'</label></div>';

        $input = form_checkbox($data, $value, $checked, $extra);

        return sprintf($tpl, $input, $label);
    }
}

function tbs_form_password($data) {

    $output  = '<div class="form-group">';

    if (isset($data['label'])) {
        $output .= form_label($data['label'], $data['id']);
        unset($data['label']);
    }

    $output .= form_password($data);

    $output .= '</div>';

    return $output;
}

function tbs_form_input($data, $type="form") {

    $output  = '<div class="'.$type.'-group">';

    if (isset($data['label'])) {
        $output .= form_label($data['label'], $data['id']);
        unset($data['label']);
    }

    $output .= form_input($data);

    $output .= '</div>';

    return $output;

}

function tbs_form_textarea($data) {

    $output  = '<div class="form-group">';

    if (isset($data['label'])) {
        $output .= form_label($data['label'], $data['id']);
        unset($data['label']);
    }

    $output .= form_textarea($data);

    $output .= '</div>';

    return $output;

}

function tbs_form_dropdown($data) {

    $output  = '<div class="form-group">';
    if (isset($data['label'])) {
        $output .= form_label($data['label'], $data['id']);
        unset($data['label']);
    }

    $options = $data['options'];
    unset($data['options']);
    $selected = $data['selected'];
    unset($data['selected']);
    $name = $data['name'];
    unset($data['name']);

    $attribute_string = "";
    foreach ($data as $attribute => $value) {
        $attribute_string = $attribute . '="' . $value . '" ';
    }

    $output .= form_dropdown($name,$options,$selected, $attribute_string);

    $output .= '</div>';

    return $output;
}

/**
 *  Builds the HTML for a dropdown button.  Takes a settings array
 *
 *  id                  => string   The id of the button
 *  input_group         => bool     Whether this button is nested inside an input group
 *  class               => string   Any classes which need to be applied to the button
 *  attributes          => array    Any other attributes the button should have
 *  label               => string   The label for the button
 *  menu_items          => array    An array with the settings for the menu items as below:
 *
 *  label               => string   The text for the drop down item
 *  anything else will be treated as an attribute for the <a> tag
 *
 *  @param array
 *  @return string
 *
 * */
function tbs_ddbutton($data) {

    // set defaults for the button
    $btn['id']  = '';
    $btn['input_group'] = 'btn-group';
    $btn['class'] = 'btn-default';
    $btn['label'] = 'Hello';
    $btn['ul_class'] = '';

    if (isset($data['id'])) {
        $btn['id']  = ' id="' . $data['id'] . '" ';
        unset($data['id']);
    }

    if (isset($data['input_group'])) {
        $btn['input_group'] ='input-group-btn';
        unset($data['input_group']);
    }

    if (isset($data['class'])) {
        $btn['class'] = $data['class'];
        unset($data['class']);
    }

    if (isset($data['label'])) {
        $btn['label'] = $data['label'];
        unset($data['label']);
    }

    if (isset($data['ul_class'])) {
        $btn['ul_class'] = $data['ul_class'];
        unset($data['ul_class']);
    }

    // get the menu items array
    $menu_items = $data['menu_items'];
    unset($data['menu_items']);

    // anything else is presumed to be an extra attribute for the button
    $btn['attributes'] = tbs_atts_to_string($data);

    $output = "<div class='{$btn['input_group']}'>";
    $output .= "<button type='button' {$btn['id']} class='btn dropdown-toggle {$btn['class']}' {$btn['attributes']} data-toggle='dropdown'>";
    $output .= '<span class="dropdown-label">' . $btn['label'] . '</span> <span class="caret"></span>';
    $output .= '</button>';
    $output .= '<ul class="dropdown-menu ' . $btn['ul_class'] . '" role="menu">';

    foreach ($menu_items as $label => $attributes) {

        // anything else is an attribute for the <a> tag
        $att_string = tbs_atts_to_string($attributes);

        $output .= '<li><a ' . $att_string . '>' . $label . '</a></li>';
    }

    $output .= '</ul></div>';

    return $output;

}

/**
 * Builds Markup for a form input with a dropdown button
 *
 * */
function tbs_input_ddbutton($data) {

    $output = '<div class="input-group">';

    if (isset($data['label'])) {
        $output .= form_label($data['label'], $data['id']);
        unset($data['label']);
    }

    $btn = $data['button'];
    unset($data['button']);

    $output .= form_input($data);

    $output .= tbs_ddbutton($btn);

    $output .= '</div>';

    return $output;

};