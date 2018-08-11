<?php
/**
 * 2016 Smart Soft.
 *
 *  @author    Marcin Kubiak <zlecenie@poczta.onet.pl>
 *  @copyright Smart Soft
 *  @license   Commercial License
 *  International Registered Trademark & Property of Smart Soft
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_3_0($module)
{
    $upgrade_version = '4.3.0';

    $module->upgrade_detail[$upgrade_version] = array();
    $result = $module->DbExecuteS('SHOW TABLES LIKE "'._DB_PREFIX_.'advancedexportfield"');

    if (count($result) != 1) {
        if (!$module->createFieldTable()) {
            $module->upgrade_detail[$upgrade_version][] =
                $module->l(sprintf('Can not install %s table', _DB_PREFIX_.'advancedexportfield'));
        }
    }

    $version = $module->DbExecuteS('SELECT version FROM `'._DB_PREFIX_.'module` WHERE name = "advancedexport"');

    if ('4.2.0' == $version[0]['version']) {
        $models = $module->getAllLinks();

        foreach ($models as $model) {
            if (checkIfModelContainsCustomFields($model)) {
                $module->upgrade_detail[$upgrade_version][] =
                    $module->l(sprintf('Wrong field id. Please contact support.'));
            }
        }
        if (count($module->upgrade_detail[$upgrade_version]) == 0) {
            $all = $module->dbExecuteS(
                sprintf("SELECT * FROM `%s`", _DB_PREFIX_.'advancedexportfield')
            );

            $allFields = array();
            foreach ($all as $field) {
                $allFields[$field['tab']][] = $field;
            }

            foreach ($models as $model) {
                $namesFields = changeIdsToFieldNames($model, $allFields);

                $ae = new AdvancedExportClass($model['id_advancedexport']);
                $ae->fields = $namesFields;
                $ae->save();
            }
        }
    } else {
        $module->upgrade_detail[$upgrade_version][] =
        $module->l(sprintf('You can upgrade only from version 4.2.0'));
    }

    return (bool) !count($module->upgrade_detail[$upgrade_version]);
}

/**
 * @param $model
 * @return bool
 */
function checkIfModelContainsCustomFields($model)
{
    $fields = Tools::jsonDecode($model['fields'], true);

    foreach ($fields['fields[]'] as $field) {
        if ($model['type'] == 'products' && $field > 63
            || $model['type'] == 'orders' && $field > 86
            || $model['type'] == 'categories' && $field > 16
            || $model['type'] == 'manufacturers' && $field > 8
            || $model['type'] == 'suppliers' && $field > 7
            || $model['type'] == 'customers' && $field > 26
            || $model['type'] == 'newsletters' && $field > 4) {
            return true;
        }
    }

    return false;
}

/**
 * @param $model
 * @param $allFields
 * @return string
 */
function changeIdsToFieldNames($model, $allFields)
{

    $fields = Tools::jsonDecode($model['fields'], true);
    $fieldNames = array();
    foreach ($fields['fields[]'] as $field) {
        $fieldNames[] = $allFields[$model['type']][$field]['field'];
    }

    $fields['fields[]'] = $fieldNames;

    return Tools::jsonEncode($fields, true);
}
