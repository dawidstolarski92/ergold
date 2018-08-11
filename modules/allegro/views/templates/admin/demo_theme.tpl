{* Demo theme in new format *}
<div id="demo-table" style="display: none;">
    <table width="85%" cellpadding="20">
        <tr>
            <td width="50%">
                <h2>{l s='This is demo of new allegro description format.' mod='allegro'}</h2>
                <p>{l s='To follow new rules, use table for description. Table should have 2 or 1 column (use colspan if 1) and up to 100 rows.' mod='allegro'}</p>
            </td>
            <td width="50%">
                <p>{l s='Allowed tags:' mod='allegro'}</p>
                <ul>
                    <li>h1</li>
                    <li>h2</li>
                    <li>p</li>
                    <li>ul</li>
                    <li>ol</li>
                    <li>li</li>
                    <li>b</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p>{l s='To merge columns like here click right mouse button on cell and select "Cell" and "Merge cells".' mod='allegro'}</p>
            </td>
        </tr>
        <tr>
            <td width="50%">
                <p>{l s='To place image on cell use "PHOTO_X" tag. You can place up to 16 images (on private account 10) from gallery, change "X" to number from 1 to 16. For example "PHOTO_1" will display first image from allegro gallery. Examples below.' mod='allegro'}</p>
            </td>
            <td width="50%">
                <p><b>{l s='Any other tags or element after or before table are not allowed. You must follow strict rules to use new format.' mod='allegro'}</b></p>
            </td>
        </tr>
        <tr>
            <td width="50%">
                PHOTO_1
            </td>
            <td width="50%">
                PHOTO_2
            </td>
        </tr>
    </table>
</div>

<script>
    var allegro_lang_tinymce_insertrow1 = "{l s='Insert row' mod='allegro'}";
    var allegro_lang_tinymce_delrow = "{l s='Delete row' mod='allegro'}";
    var allegro_lang_tinymce_setdemo = "{l s='Set demo' mod='allegro'}";

    {literal}
    function setTinyMCEFormat(format) {

        // Remove default editor
        if (typeof tinymce !== 'undefined') {

            if (tinymce.majorVersion < 4) {
                return;
            }
            
            tinymce.EditorManager.execCommand('mceRemoveEditor', true, 'content_html');
        }

        if (format == 1) {
            // Init TinyMCE again with new config
            tinySetup({
                selector: '.rte_light',
                toolbar1 : 'code,|,bold,|,bullist,numlist,|,formatselect,|,insrow1,delrow,setdemo,',
                plugins : 'paste table contextmenu table code',
                menu: {
                    edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
                    insert: {title: 'Insert', items: 'media image link | pagebreak'},
                    view: {title: 'View', items: 'visualaid'},
                    table: {title: 'Table', items: 'inserttable deletetable | cell row column'},
                },
                block_formats: 'Paragraph=p;Header 1=h1;Header 2=h2',
                table_row_limit : 100,
                table_col_limit : 2,

                setup: function (editor) {
                    editor.addButton('insrow1', {
                        text: allegro_lang_tinymce_insertrow1,
                        icon: false,
                        onclick: function () {
                            editor.execCommand('mceTableInsertRowAfter', false, editor);
                        }
                    });
                    editor.addButton('delrow', {
                        text: allegro_lang_tinymce_delrow,
                        icon: false,
                        onclick: function () {
                            editor.execCommand('mceTableDeleteRow', false, editor);
                        }
                    });
                    editor.addButton('setdemo', {
                        text: allegro_lang_tinymce_setdemo,
                        icon: false,
                        onclick: function () {
                            editor.setContent($('#demo-table').html());
                        }
                    });
                }
            });
        } else {
            tinySetup({selector: '.rte_light'});
        }
    }

    $(document).ready(function() {

        // PS 1.6.0.0 - 1.6.1.0 fix
        if(!$('#content_html').hasClass('rte_light') && tinymce.majorVersion >= 4) {
            $('#content_html').addClass('rte_light');
        }

        $('#allegro_theme_form input[name="format"]').change(function(){
            setTinyMCEFormat($(this).val());
        });

        setTinyMCEFormat(parseInt($('#allegro_theme_form input[name="format"]:checked').val()));
    });
    {/literal}
</script>