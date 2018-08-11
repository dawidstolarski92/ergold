<?php
/**
 * 2016 Smart Soft.
 *
 *  @author    Marcin Kubiak
 *  @copyright Smart Soft
 *  @license   Commercial License
 *  International Registered Trademark & Property of Smart Soft
 */

class AdvancedExportClass extends ObjectModel
{
    public $id;
    public $type;
    public $name;
    public $delimiter;
    public $separator;
    public $id_lang;
    public $charset;
    public $add_header;
    public $decimal_separator;
    public $decimal_round;
    public $strip_tags;
    public $image_type;
    public $only_new;
    public $date_from;
    public $date_to;
    public $last_exported_id;
    public $start_id;
    public $end_id;
    public $save_type;
    public $fields;
    public $email;
    public $ftp_user_name;
    public $ftp_hostname;
    public $ftp_user_pass;
    public $filename;

    public static $definition = array(
        'table' => 'advancedexport',
        'primary' => 'id_advancedexport',
    );

    public function copyFromPost()
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (key_exists($key, $this) and $key != 'id_'.$this->table) {
                $this->{$key} = $value;
            }
        }
    }

    public function getFields()
    {
        parent::validateFields();
        $fields = null;
        $fields['id_advancedexport'] = (int) ($this->id);
        $fields['type'] = (string) ($this->type);
        $fields['name'] = (string) ($this->name);
        $fields['delimiter'] = (string) ($this->delimiter);
        $fields['separator'] = (string) ($this->separator);
        $fields['id_lang'] = (int) ($this->id_lang);
        $fields['charset'] = (string) ($this->charset);
        $fields['add_header'] = (int) ($this->add_header);
        $fields['decimal_separator'] = (string) ($this->decimal_separator);
        $fields['decimal_round'] = (int) ($this->decimal_round);
        $fields['strip_tags'] = (int) ($this->strip_tags);
        $fields['only_new'] = (int) ($this->only_new);
        $fields['date_from'] = (string) ($this->date_from);
        $fields['date_to'] = (string) ($this->date_to);
        $fields['last_exported_id'] = (int) ($this->last_exported_id);
        $fields['start_id'] = (int) ($this->start_id);
        $fields['end_id'] = (int) ($this->end_id);
        $fields['save_type'] = (string) ($this->save_type);
        $fields['fields'] = (string) ($this->fields);
        $fields['email'] = (string) ($this->email);
        $fields['image_type'] = (string) ($this->image_type);
        $fields['ftp_hostname'] = (string) ($this->ftp_hostname);
        $fields['ftp_user_pass'] = (string) ($this->ftp_user_pass);
        $fields['ftp_user_name'] = (string) ($this->ftp_user_name);
        $fields['filename'] = (string) ($this->filename);

        return $fields;
    }
}
