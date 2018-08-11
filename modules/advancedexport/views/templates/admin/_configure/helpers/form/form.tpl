{*
*  @author Marcin Kubiak
*  @copyright  Smart Soft
*  @license    Commercial license
*  International Registered Trademark & Property of Smart Soft
*}

{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type == 'bsmselect'}
        <select name="{$input.name|escape:'htmlall':'UTF-8'}"
                class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                {if isset($input.multiple)}multiple="multiple" {/if}
                {if isset($input.size)}size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                {if isset($input.onchange)}onchange="{$input.onchange|escape:'htmlall':'UTF-8'}"{/if}>
            {if isset($input.options.default)}
                <option value="{$input.options.default.value|intval}">{$input.options.default.label|escape:'htmlall':'UTF-8'}</option>
            {/if}
            {if isset($input.options.optiongroup)}
                {foreach $input.options.optiongroup.query AS $optiongroup}
                    <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'htmlall':'UTF-8'}">
                        {foreach name=option from=$optiongroup[$input.options.options.query] item="option"}
                            <option value="{$option[$input.options.options.id]|escape:'htmlall':'UTF-8'}"
                                    {if isset($input.multiple)}
                                        {foreach name=field_value from=$fields_value[$input.name] item="field_value"}
                                            {if $field_value == $option[$input.options.options.id]}
                                                data-sortby="{$smarty.foreach.field_value.iteration|intval}"
                                                selected="selected"
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                    {/if}
                                    >{$option[$input.options.options.name]|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </optgroup>
                {/foreach}
            {else}
                {foreach name=test from=$input.options.query item="option"}
                    {if is_object($option)}
                        <option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}"
                                {if isset($input.multiple)}
                                    {foreach name=field_value from=$fields_value[$input.name] item="field_value"}
                                        {if $field_value == $option->$input.options.id}
                                            data-sortby="{$smarty.foreach.field_value.iteration|intval}"
                                            selected="selected"
                                        {/if}
                                    {/foreach}
                                {else}
                                    {if $fields_value[$input.name] == $option->$input.options.id}
                                        selected="selected"
                                    {/if}
                                {/if}
                                >{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
                    {elseif $option == "-"}
                        <option value="">-</option>
                    {else}
                        <option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}"
                                {if isset($input.multiple)}
                                    {foreach name=field_value from=$fields_value[$input.name] item="field_value"}
                                        {if $field_value == $option[$input.options.id]}
                                            data-sortby="{$smarty.foreach.field_value.iteration|intval}"
                                            selected="selected"
                                        {/if}
                                    {/foreach}
                                {else}
                                    {if $fields_value[$input.name] == $option[$input.options.id]}
                                        selected="selected"
                                    {/if}
                                {/if}
                                >{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>

                    {/if}
                {/foreach}
            {/if}
        </select>
        {if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint|escape:'htmlall':'UTF-8'}<span class="hint-pointer">&nbsp;</span></span>{/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
