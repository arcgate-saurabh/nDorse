<?php

App::uses('AppHelper', 'View/Helper');

class TinymceHelper extends AppHelper {

    // Take advantage of other helpers
    public $helpers = array('Js', 'Html', 'Form');
    // Check if the tiny_mce.js file has been added or not
    public $_script = false;

    /**
     * Adds the tiny_mce.js file and constructs the options
     *
     * @param string $fieldName Name of a field, like this "Modelname.fieldname"
     * @param array $tinyoptions Array of TinyMCE attributes for this textarea
     * @return string JavaScript code to initialise the TinyMCE area
     */
    function _build($fieldName, $tinyoptions = array()) {
//        pr($tinyoptions);exit;
        if (!$this->_script) {
            // We don't want to add this every time, it's only needed once
            $this->_script = true;
            $this->Html->script('tinymce/tinymce.min', array('inline' => false));
//            echo $this->Html->script('tinymce/tinymce', array('inline' => false));
        }

        // Ties the options to the field
        $tinyoptions['mode'] = 'exact';
        $tinyoptions['elements'] = $this->domId($fieldName);
        $tinyoptions['forced_root_block'] = "";
        $tinyoptions['force_br_newlines'] = true;
        $tinyoptions['force_p_newlines'] = false;
        $tinyoptions['menubar'] = false;
        //$tinyoptions['toolbar'] = array('save,newdocument,bold,italic,underline,fontselect,fontsizeselect');
        $tinyoptions['toolbar'] = array('undo,redo,|,formatselect,styleselect,|,bold,italic,|,alignleft,aligncenter,alignright,alignjustify,|,numlist,bullist,|,outdent,indent,|,fontselect,fontsizeselect');
//        $tinyoptions['toolbar'] = true;
        $tinyoptions['toolbar1'] = 'fontselect';
        $tinyoptions['theme_advance_path'] = false;
        $tinyoptions['elementpath'] = false;
//        $tinyoptions['font_formats'] = "Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n";
        // List the keys having a function
        $value_arr = array();
        $replace_keys = array();
        foreach ($tinyoptions as $key => &$value) {
            // Checks if the value starts with 'function ('
            if (!is_array($value)) {
                if (strpos($value, 'function(') === 0) {
                    $value_arr[] = $value;
                    $value = '%' . $key . '%';
                    $replace_keys[] = '"' . $value . '"';
                }
            }
        }

        // Encode the array in json
        $json = $this->Js->object($tinyoptions);
//        pr($replace_keys); exit;
        // Replace the functions
        $json = str_replace($replace_keys, $value_arr, $json);
        $this->Html->scriptStart(array('inline' => false));
//        pr($json); exit;
        echo 'tinyMCE.init(' . $json . ');';

        $this->Html->scriptEnd();
    }

    /**
     * Creates a TinyMCE textarea.
     *
     * @param string $fieldName Name of a field, like this "Modelname.fieldname"
     * @param array $options Array of HTML attributes.
     * @param array $tinyoptions Array of TinyMCE attributes for this textarea
     * @param string $preset
     * @return string An HTML textarea element with TinyMCE
     */
    function textarea($fieldName, $options = array(), $tinyoptions = array(), $preset = null) {
        // If a preset is defined
        if (!empty($preset)) {
            $preset_options = $this->preset($preset);

            // If $preset_options && $tinyoptions are an array
            if (is_array($preset_options) && is_array($tinyoptions)) {
                $tinyoptions = array_merge($preset_options, $tinyoptions);
            } else {
                $tinyoptions = $preset_options;
            }
        }
        return $this->Form->textarea($fieldName, $options) . $this->_build($fieldName, $tinyoptions);
    }

    /**
     * Creates a TinyMCE textarea.
     *
     * @param string $fieldName Name of a field, like this "Modelname.fieldname"
     * @param array $options Array of HTML attributes.
     * @param array $tinyoptions Array of TinyMCE attributes for this textarea
     * @return string An HTML textarea element with TinyMCE
     */
    function input($fieldName, $options = array(), $tinyoptions = array(), $preset = null) {
        // If a preset is defined
        if (!empty($preset)) {
            $preset_options = $this->preset($preset);

            // If $preset_options && $tinyoptions are an array
            if (is_array($preset_options) && is_array($tinyoptions)) {
                $tinyoptions = array_merge($preset_options, $tinyoptions);
            } else {
                $tinyoptions = $preset_options;
            }
        }
        $options['type'] = 'textarea';
        return $this->Form->input($fieldName, $options) . $this->_build($fieldName, $tinyoptions);
    }

    /**
     * Creates a preset for TinyOptions
     *
     * @param string $name
     * @return array
     */
    private function preset($name) {
        // Full Feature

        if ($name == 'full') {
            return array(
                'theme' => 'modern',
                'plugins' => 'pagebreak,layer,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,template',
//                'plugins' => 'print,preview,fullpage,powerpaste ,searchreplace ,autolink ,directionality ,advcode ,visualblocks ,visualchars ,fullscreen ,image ,link ,media ,template ,codesample ,table ,charmap ,hr ,pagebreak ,nonbreaking ,anchor ,insertdatetime ,advlist ,lists ,textcolor ,wordcount ,imagetools ,contextmenu ,colorpicker ,textpattern',
                'theme_advanced_buttons1' => "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                'theme_advanced_buttons3' => "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//                'theme_advanced_buttons1' => 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,|,formatselect,|,fontselect,fontsizeselect',
//                'theme_advanced_buttons2' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
//                'theme_advanced_buttons3' => 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
                'theme_advanced_buttons4' => 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
                'theme_advanced_toolbar_location' => 'bottom',
                'theme_advanced_toolbar_align' => 'left',
                'theme_advanced_statusbar_location' => 'bottom',
                'theme_advanced_resizing' => true,
                'theme_advanced_resize_horizontal' => false,
                'convert_fonts_to_spans' => true,
                'valid_elements' => 'font[face|size|color|style',
                'file_browser_callback' => 'ckfinder_for_tiny_mce'
            );
        }

        // Basic
        if ($name == 'basic') {
            return array(
                'theme' => 'modern',
                'plugins' => 'safari,advlink,paste',
                'theme_advanced_buttons1' => 'code,|,copy,pastetext,|,bold,italic,underline,|,link,unlink,|,bullist,numlist',
                'theme_advanced_buttons2' => '',
                'theme_advanced_buttons3' => '',
                'theme_advanced_toolbar_location' => 'top',
                'theme_advanced_toolbar_align' => 'center',
                'theme_advanced_statusbar_location' => 'none',
                'theme_advanced_resizing' => false,
                'theme_advanced_resize_horizontal' => false,
                'convert_fonts_to_spans' => false
            );
        }

        // Simple
        if ($name == 'simple') {
            return array(
                'theme' => 'simple',
            );
        }

        // BBCode
        if ($name == 'bbcode') {
            return array(
                'theme' => 'modern',
                'plugins' => 'bbcode',
//                'theme_advanced_buttons1' => 'bold,italic,underline,undo,redo,link,unlink,image,forecolor,styleselect,removeformat,cleanup,code',
                'theme_advanced_buttons2' => '',
                'theme_advanced_buttons3' => '',
                'theme_advanced_toolbar_location' => 'top',
                'theme_advanced_toolbar_align' => 'left',
                'theme_advanced_styles' => 'Code=codeStyle;Quote=quoteStyle',
                'theme_advanced_statusbar_location' => 'bottom',
                'theme_advanced_resizing' => true,
                'theme_advanced_resize_horizontal' => false,
                'entity_encoding' => 'raw',
                'add_unload_trigger' => false,
                'remove_linebreaks' => false,
                'inline_styles' => false
            );
        }
        return null;
    }

}
