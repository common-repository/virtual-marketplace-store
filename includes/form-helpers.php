<?php namespace vwformtools;

class FormHelpers
{
    public static function gen_field($label, $name, $value, $type="text", $options=array())
    {
        $output = "";
        switch ($type) {
            case 'editor':
                $output .= FormHelpers::gen_editor($label, $name, $value);
                break;
            case 'textarea':
                $output .= FormHelpers::gen_textarea($label, $name, $value);
                break;
            case 'multiinput':
                $output .= FormHelpers::gen_multi_input($label, $name, $value);
                break;
            case 'multifaq':
                $output .= FormHelpers::gen_multi_faq($label, $name, $value);
                break;
            case 'color':
                $output .= FormHelpers::gen_color_input($label, $name, $value);
                break;
            case 'toggle':
                $output .= FormHelpers::gen_toggle($label, $name, $value);
                break;
            case 'select':
                $output .= FormHelpers::gen_select($label, $name, $value, $options);
                break;
            case 'text':
            default:
                $output .= FormHelpers::gen_input($label, $name, $value);
                break;
        }

        return $output;
    }

    public static function _gen_label($label)
    {
        return sprintf('<label class="control-label">%s</label>', $label);
    }

    public static function gen_select($label, $name, $selectedKey, $options)
    {
        extract( merge_options(
            array("class" => "", "placeholder" => "", "note" => "", "data" => array(), "isMultiple" => false, "addBlank" => false, "updateRegion" => false), $options)
        );
        $output = "No Data Available";
        $linkTemplate = '<a target="blank" href="post.php?post=%s&action=edit">%s</a> ';
        if (count($data) > 0)
        {
            $selectedKeys = array();
            $links = "";
        
            if ($selectedKey != "")
            {
                $selectedKeys = explode(",", $selectedKey);
            }
            
            //If it's a multi select then flag it as such and explode the key into keys
            if ($isMultiple)
            {
                $output = sprintf('<input style="display: none" type="text" id="%1$s" name="%1$s" value="%2$s" />', $name, $selectedKey);
                $output .= sprintf('<select id="vmstore-select-%s" class="%s vmstore-select-multi" multiple>', $name, $class, $label);
            }
            else
            {
                $output = sprintf('<select id="%s" class="%s vmstore-select" name="%s">', $name, $class, $label);
                
            }
            if ($addBlank)
            {
                $output .= FormHelpers::gen_select_option("", "", $placeholder);
            }
            foreach ($selectedKeys as $key) {
                $output .= FormHelpers::gen_select_option($key, $data[$key], true);
                $links .= sprintf($linkTemplate, $key, $data[$key]);
                unset($data[$key]);
            }
            foreach ($data as $key => $text)
            {
                $output .= FormHelpers::gen_select_option($key, $text);
            }
            
            $output .= '</select>';
            if ($updateRegion == true) {
                $output .= sprintf('<div class="vmstore-update-region"><label class="control-label">Product Links</label><div class="controls"><div id="%s-update" class="vmstore-update-content">%s</div></div></div>', $name, $links);
            }
            if ($note != "") {
                $output .= sprintf('<p class="help-block">%s</p>', $note);
            }
        }
        return $output;
    }
    public static function gen_select_option($key, $text, $selected = false)
    {
        $optionTemplate = '<option value="%s"%s>%s</option>\n';
        $output = "";
        if ($selected)
        {
            $output .= sprintf($optionTemplate, $key, ' selected', $text);
        }
        else
        {
            $output .= sprintf($optionTemplate, $key, '', $text);
        }
        return $output;
    }

    public static function gen_multi_input($label, $name, $value)
    {
        $wrapper = '<div id="vmp_input_item_list_%s">%s</div>';
        $additem_action = '<a style="display:none;" href="javascript:void(0);">Add</a>';
        $template = '<template id="vmp_template_%s">' .
                    FormHelpers::gen_input($label, $name . "{ID}", "") .
                    '</template>';
        $output = "";



        if (is_array($value))
        {
            for ($i=0; $i<count($value);$i++) {
                $output .= FormHelpers::gen_input($label . " #" . $i, $name . $i, $value[$i]);
            }
        }

        return sprintf($wrapper, $name, $output) . sprintf($template, "vmp_input_template_" . $name, $label, $name, "");
    }

    public static function gen_multi_faq($label, $name, $value)
    {
        $wrapper = '<div id="vmp_faq_item_list_%s">%s</div>';
        $additem_action = '<a style="display:none;" href="javascript:void(0);">Add</a>';
        $template = '<template id="vmp_faq_template_%s">' .
                    FormHelpers::gen_faq($label, $name . "{ID}", "{ID}", "", "", "") .
                    '</p></div></template>';
        $output = "";

        if (is_array($value))
        {
            for ($i=0; $i<count($value);$i++) {
                $qa = $value[$i];
                $output .= FormHelpers::gen_faq($label . " #" . $i . "<br />", $i, $name, $qa->question, $qa->answer, $value[$i]);
            }
        }

        return sprintf($wrapper, $name, $output) . sprintf($template, "vmp_faq_template_" . $name, $label, $name, "");
    }

    public static function gen_faq($label, $name, $id, $question, $answer, $json)
    {
        $template = '<p>' . $label . '</p>' . 
                    '<div class="control-group">' .
                    FormHelpers::_gen_label('Question') .
                    '<input type="text" name="%1$s_q_%2$s" value="%3$s" class="vmstore-widefat"></div>' .
                    '<div class="control-group">' .
                    FormHelpers::_gen_label('Answer') .
                    '<textarea type="text" name="%1$s_a_%2$s" class="vmstore-widefat">%4$s</textarea>' .
                    '<input type="hidden" name="%1$s%2$s" value="" class="vmstore-widefat"></div>';
        return sprintf($template, $name, $id, $question, $answer);
    }

    public static function gen_input($label, $name, $value)
    {
        $template = '<div class="control-group">'.FormHelpers::_gen_label($label).'<input type="text" name="%s" value="%s" class="vmstore-widefat"></div>';
        return sprintf($template, $name, esc_textarea($value));
    }

    public static function gen_color_input($label, $name, $value)
    {
        $template = '<div class="control-group">'.FormHelpers::_gen_label($label).'<input type="color" name="%s" value="%s" class="vmstore-widefat"></div>';
        return sprintf($template, $name, esc_textarea($value));
    }

    public static function gen_toggle($label, $name, $value)
    {
        $template = '<div class="control-group">'.FormHelpers::_gen_label($label).'<input type="checkbox" name="%s" value="%s" class="vmstore-widefat"%s></div>';

        $checked = ($value==1)?' checked="checked"':"";

        return sprintf($template, $name, $value, $checked);
    }

    public static function gen_textarea($label, $name, $value)
    {
        $template = '<div class="control-group">'.FormHelpers::_gen_label($label).'<textarea type="text" name="%s" class="vmstore-widefat">%s</textarea></div>';
        return sprintf($template, $name, esc_textarea($value));
    }

    public static function gen_editor($label, $name, $value)
    {
        $template = '<div class="control-group">'.FormHelpers::_gen_label($label).'%s</div>';
        ob_start( );
        wp_editor ( 
           htmlspecialchars_decode( $value ), 
           $name,
           array ( "media_buttons" => true ) 
          );
        $editor = ob_get_clean( );

        return sprintf($template, $editor);
    }
}