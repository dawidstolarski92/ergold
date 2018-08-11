<script>
function tinySetup2(config) {
    if(!config) {
        config = { };
    }

    default_config = {
        selector: '.rte',
        plugins : 'code lists',
        toolbar1: 'code | undo redo | formatselect | bold | bullist numlist | markers',
        toolbar2: '',
        valid_elements : 'h1,h2,p,b,ul,ol,li',
        valid_children: '-h1[b],-h2[b],-ul[h1],-ol[h1],-li[h1],-ul[h2],-ol[h2],-li[h2],-li[p]',
        block_formats: 'Paragraph=p;Header 1=h1;Header 2=h2',
        menubar: false,
        statusbar : false,
        //remove_linebreaks: true,
        force_br_newlines : false,
        force_p_newlines : true,
        forced_root_block : '',
        branding: false,
        min_height: 200,
        formats: {
            bold: { inline: 'b' },  
        },
        setup: function (editor) {
            editor.on("change", function () {
                nfAllegroEditor.propagateChanges(editor, false);
            }).on("blur", function () {
                nfAllegroEditor.propagateChanges(editor, true);
            });

            editor.addButton('markers', {
              type: 'menubutton',
              text: "{l s='Markers' mod='allegro'}",
              icon: false,
              menu: [{
                text: "{l s='Auction title' mod='allegro'}",
                onclick: function() {
                    //editor.setContent('');
                    editor.insertContent('<p>[auction_title]</p>');
                }
                }, {
                    text: "{l s='Auction price' mod='allegro'}",
                    onclick: function() {
                        //editor.setContent('');
                        editor.insertContent('<p>[auction_price]</p>');
                    }
                }, {
                    text: "{l s='Product description' mod='allegro'}",
                    onclick: function() {
                        editor.setContent('');
                        editor.insertContent('[product_description]');
                    }
                }, {
                    text: "{l s='Product description short' mod='allegro'}",
                    onclick: function() {
                        editor.setContent('');
                        editor.insertContent('[product_description_short]');
                    }
                }, {
                    text: "{l s='Product features' mod='allegro'}",
                    onclick: function() {
                        editor.setContent('');
                        editor.insertContent('[product_features]');
                    }
                }, {
                    text: "{l s='Product weight' mod='allegro'}",
                    onclick: function() {
                        //editor.setContent('');
                        editor.insertContent('<p>[product_weight]</p>');
                    }
                }, {
                    text: "{l s='Manufacturer name' mod='allegro'}",
                    onclick: function() {
                        //editor.setContent('');
                        editor.insertContent('<p>[manufacturer_name]</p>');
                    }
                }
            ]
            });
        },
    };

    $.each(default_config, function(index, el) {
        if (config[index] === undefined) {
            config[index] = el
;        }
    });

    tinyMCE.init(config);
}

$(document).ready(function() {
    // Init allegro editor
    nfAllegroEditor = $('#nf-editor')
        .prependTo($('#content_html').parent())
        .nfAllegroEditor({
            default_text:   "{l s='Enter text here...' mod='allegro'}",
            image_text:     "{l s='Image' mod='allegro'}",
            add_row_text:   "{l s='Add row' mod='allegro'}",
            hideInput:      !{$dev_mode|intval},
        })
        .show();
});
</script>

<div class="alert alert-info">
    <p><b>{l s='New theme editor' mod='allegro'}</b></p>
    <ul>
        <li>{l s='You can use this editor in similar way as allegro description editor (new format)' mod='allegro'}</li>
        <li>{l s='Add text custom text or pick image form gallery' mod='allegro'}</li>
        <li><a href="https://addonspresta.com/pl/content/10-integracja-z-allegro#pt6">{l s='Module markers' mod='allegro'}</a> {l s='are allowed' mod='allegro'}</li>
        <li>{l s='Allowed html tags: h1, h2, b, ul, ol, li - avoid pasting html code manually' mod='allegro'}</li>
        <li>{l s='Empty section or section with assigned but not send images will be deleted ' mod='allegro'}</li>
    </ul>
</div>

<div id="nf-editor" style="display: none;"></div>

<input type="hidden" name="format" value="1">
