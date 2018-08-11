<?php
/**
 * LICENCE
 * 
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 * 
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 * 
 *  @author    Tomasz Dacka (kontakt@tomaszdacka.pl)
 *  @copyright PrestaHelp.com
 *  @license   ALL RIGHTS RESERVED
 */

/**
 * PHPIHelperForm
 */
class PHPIHelperForm
{

    public static $carriers = null;
    public static $carriers_total = null;

    public static function block($start = true, $title = false, $show_border = false)
    {
        return array(
            'type' => $start ? 'blockstart' : 'blockend',
            'name' => $start ? 'blockstart' : 'blockend',
            'title' => $title ? $title : null,
            'show_border' => $show_border,
        );
    }

    private static function base($data, $editable = null, $name = null, $label = null)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $data['form_group_class'] = 'phpi '.$name;
        if (isset($editable) && !$editable) {
            $data['disabled'] = 'disabled';
        }
        if (isset($name) && !empty($name)) {
            $data['name'] = $name;
        }
        if (isset($label) && !empty($label)) {
            $data['label'] = PHPITools::l($label);
        }
        return $data;
    }

    public static function input($editable, $name, $label, $prefix = false, $suffix = false, $desc = false, $size = false)
    {
        $data = array(
            'type' => 'text',
            'desc' => $desc ? $desc : null,
            'prefix' => $prefix ? $prefix : null,
            'suffix' => $suffix ? $suffix : null
        );
        if ($size) {
            $data['size'] = $size;
        }

        return self::base($data, $editable, $name, $label);
    }

    public static function date($editable, $name, $label, $desc = false)
    {
        $data = array(
            'type' => 'date',
            'desc' => $desc ? $desc : null,
        );
        return self::base($data, $editable, $name, $label);
    }

    public static function datetime($editable, $name, $label, $desc = false)
    {
        $data = array(
            'type' => 'datetime',
            'desc' => $desc ? $desc : null,
        );
        return self::base($data, $editable, $name, $label);
    }

    /**
     * 
     * @param string $name
     * @param string $label
     * @param array $values Tablica elementów w postaci wartość=>etykieta
     * @return array
     */
    public static function radio($editable, $name, $label, $values, $switch = false, $desc = false)
    {
        $_values = array();
        $id = 1;
        
        foreach ($values as $key => $value) {
            $_values [] = array(
                'id' => $name.'_'.$id++,
                'value' => $key,
                'label' => $value
            );
        }
        $return = array(
            'type' => ($switch && PHPITools::ps16()) ? 'switch' : 'radio',
            'class' => 't',
            'values' => $_values,
            'is_bool' => $switch,
        );
        if ($desc != false) {
            $return['desc'] = $desc;
            $return['hint'] = $desc;
        }
        return self::base($return, $editable, $name, $label);
    }

    public static function radioSwitch($editable, $name, $label, $values = array(), $desc = false)
    {
        if (empty($values)) {
            $values = array('1' => 'Tak', '0' => 'Nie');
        }
        return self::radio($editable, $name, $label, $values, true, $desc);
    }

    /**
     * 
     * @param type $name
     * @param type $label
     * @param type $options tablica elementów query, id, name
     * @param type $desc
     * @param type $size
     * @param type $multiple
     * @return type
     */
    public static function select($editable, $name, $label, $source, $options = array(), $desc = false, $size = false, $multiple = false)
    {
        if (empty($options)) {
            $options = array(
                'query' => $source,
                'id' => 'id',
                'name' => 'label'
            );
        } else {
            $options['query'] = $source;
        }

        $html = array(
            'type' => PHPITools::ps16() ? 'select16' : 'select',
            'name' => $name.($multiple ? '[]' : ''),
            'options' => $options,
        );

        if ($multiple) {
            $html['multiple'] = 'multiple';
        }
        if ($size) {
            $html['size'] = $size;
        }
        if ($desc) {
            $html['desc'] = $desc;
        }

        return self::base($html, $editable, null, $label);
    }
    
    /**
     * 
     * @param type $name
     * @param type $label
     * @param type $options tablica elementów query, id, name
     * @param type $desc
     * @param type $size
     * @param type $multiple
     * @return type
     */
    public static function selectChosen($editable, $name, $label, $source, $options = array(), $desc = false, $size = false, $multiple = false)
    {
        $normal = self::select($editable, $name, $label, $source, $options, $desc, $size, $multiple);
        $normal['class'] = 'chosen';
        return $normal;
    }
    
    /**
     * 
     * @param type $name
     * @param type $label
     * @param type $options tablica elementów query, id, name
     * @param type $desc
     * @param type $size
     * @param type $multiple
     * @return type
     */
    public static function checkbox($editable, $name, $label, $source, $options = array(), $desc = false, $size = false, $multiple = false)
    {
        if (empty($options)) {
            $options = array(
                'query' => $source,
                'id' => 'id',
                'name' => 'label'
            );
        } else {
            $options['query'] = $source;
        }

        $html = array(
            'type' => 'checkbox',
            'name' => $name,
            'values' => $options
        );

        if ($desc) {
            $html['desc'] = $desc;
        }

        return self::base($html, $editable, null, $label);
    }

    public static function carriers($editable, $label, $list)
    {
        if (!isset(self::$carriers)) {
            self::$carriers = Carrier::getCarriers(Context::getContext()->language->id, true, false, false, null, ALL_CARRIERS);
            self::$carriers_total = count(self::$carriers);
        }
        $_list = array();
        foreach ($list as $key => $value) {
            $_list[] = array(
                'label' => PHPITools::l($value),
                'multiple' => 'true',
                'size' => self::$carriers_total,
                'name' => $key.'[]',
                'options' => array(
                    'query' => self::$carriers,
                    'id' => 'id_reference',
                    'name' => 'name'
            ));
        }
        $data = array(
            'type' => PHPITools::ps16() ? 'carriers16' : 'carriers',
            'hint' => PHPITools::l('Usługa będzie domyślnie wybrana tylko dla wybranych przewoźników. Możesz wybrać kilku trzymając klawisz CTRL'),
            'list' => $_list
        );
        return self::base($data, $editable, 'carriers', $label);
    }

    public static function buttons($list)
    {
        $_list = array();
        foreach ($list as $key => $value) {
            $_list[] = array(
                'link' => isset($value['link']) && $value['link'] ? $key : null,
                'title' => PHPITools::l($value['title']),
                'name' => $key,
                'class' => PHPITools::ps16() ? 'btn btn-'.$value['class'] : 'button'
            );
        }
        return array(
            'type' => 'buttons',
            'name' => 'buttons',
            'label' => '',
            'list' => $_list
        );
    }
}
