/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

(function ( $ ) {
    $.fn.nfAllegroEditor = function(options) {
        var settings = $.extend({
            outputInput:    '#content_html',
            imagesList:     [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],
            default_text:   'Enter text here...',
            image_text:     'Image',
            add_row_text:   'Add row',
            hideInput:      true,
        }, options);

        var myThis = this;
 
        try {
            var themeJSON = JSON.parse($(settings.outputInput).val());
        } catch(e) {
            themeJSON = [];
        } 

        if (themeJSON) {
            myThis.append(`
                <div class="nf-sections"></div>
                <section class="nf-add-row">
                    `+settings.add_row_text+` 
                    <span class="fn-add-btn" data-type="1">
                        <i class="nf-text"></i>
                    </span>
                    <span class="fn-add-btn" data-type="2">
                        <i class="nf-picture"></i>
                    </span>
                    <span class="fn-add-btn" data-type="3">
                        <i class="nf-picture-text"></i>
                    </span>
                    <span class="fn-add-btn" data-type="4">
                        <i class="nf-text-picture"></i>
                    </span>
                    <span class="fn-add-btn" data-type="5">
                        <i class="nf-picture-picture"></i>
                    </span>
                </section>`
            );
        }

        var addSection = function(row, index) {
            var sections = myThis.find('.nf-sections');
            var html = ``;
            var twoColumns = typeof row[1] !== 'undefined';

            if (row.length <= 2) {
                html += `<section class="nf-section index-`+index+`" data-index="`+index+`">
                            <div class="fn-controlls">
                                <span class="fn-controll-delete">
                                    <i class="nf-btn-delete"></i>
                                </span>
                                <span class="fn-controll-up">
                                    <i class="nf-btn-up"></i>
                                </span>
                                <span class="fn-controll-down">
                                    <i class="nf-btn-down"></i>
                                </span>
                            </div>`;
                row.forEach(function(col, colIndex) {
                    html += `<div class="items item-`+(twoColumns ? `6` : `12`)+`">`;
                    // Photo
                    var isPhoto = row[colIndex].match(/PHOTO_\d{1,2}/g);
                    if (isPhoto) {
                        html += `
                            <div class="photo">
                                <select class="nv-val" data-row="`+index+`">`;
                        for (i = 1; i <= settings.imagesList.length; i++) {
                            html += `<option `+(row[colIndex] == 'PHOTO_'+i ? `selected="selected"` : ``)+` value="PHOTO_`+i+`">`+settings.image_text+` `+i+`</option>`;
                        }
                        html += `
                                </select>
                            </div>`;

                    } else 
                    // Text
                    {
                        html += `
                            <div class="editor">
                                <textarea data-row="`+index+`" class="nv-val editor-index-`+index+`">`+row[colIndex]+`</textarea>
                            </div>`;
                    }

                    html += `
                            <div class="item`+(isPhoto ? ' hide' : '')+`">`+row[colIndex]+`</div>
                        </div>`;

                });
                html += `</section>`;

                sections.append(html);
            }
        }

        // Add all saved sections
        themeJSON.forEach(function(row, index) {
            addSection(row, index);
        });

        // Hide textarea
        if (settings.hideInput) {
            $(settings.outputInput).hide();
        }

        // Handle deleting section
        myThis.on('click', '.fn-controll-delete', function(){
            removeAllEditors();
            $(this).closest('section').remove();
            setJsonToTextarea();
        });

        // Handle move up/down section
        myThis.on('click', '.fn-controll-up', function(e){
            e.preventDefault();
            var section = $(this).closest('section');
            tinyMCE.remove('textarea');
            var html = section.get(0).outerHTML;
            var prev = section.prev();
            section.remove();
            $(html).insertBefore(prev);

            tinySetup2({selector: '.editor-index-'+section.find('textarea').data('row')});
            setJsonToTextarea();
        }).on('click', '.fn-controll-down', function(e){
            e.preventDefault();
            var section = $(this).closest('section');
            tinyMCE.remove('textarea');
            var html = section.get(0).outerHTML;
            var next = section.next();
            section.remove();
            $(html).insertAfter(next);

            tinySetup2({selector: '.editor-index-'+section.find('textarea').data('row')});
            setJsonToTextarea();
        });

        // Handle adding section
        var addBtn = myThis.find('.fn-add-btn');
        addBtn.click(function(){
            removeAllEditors();

            // Section type
            var sType = $(this).data('type');

            // Nex index
            var index = myThis.find('.nf-section').length;

            var default_text = '<p>'+settings.default_text+'</p>';

            switch(sType) {
                case 1:
                    addSection([default_text], index);
                    break;
                case 2:
                    addSection(["PHOTO_1"], index);
                    break;
                case 3:
                    addSection(["PHOTO_1", default_text], index);
                    break;
                case 4:
                    addSection([default_text, "PHOTO_1"], index);
                    break;
                case 5:
                    addSection(["PHOTO_1", "PHOTO_2"], index);
                    break;
                default:
                    addSection([default_text], index);
            } 

            setJsonToTextarea(); 
        });

        // Handle focus section
        myThis.on('click', '.nf-section', function(){
            var section = $(this);
            if (!section.hasClass('visible')) {
                var index = section.data('index');
                removeAllEditors();
                section.addClass('visible');

                setJsonToTextarea();

                tinySetup2({selector: '.editor-index-'+index});
            }
        });   

        // Handle change image
        myThis.on('change', 'select.nv-val', function(){
            setJsonToTextarea();
        });   

        var removeAllEditors = function(){
            tinyMCE.remove('.nv-val');
            myThis.find('section').removeClass('visible');
        }

        var setJsonToTextarea = function(){
            var data = [];
            $('.nf-section').each(function(rowIndex, row){
                $(row).find('.nv-val').each(function(index, input){
                    // Remove linebraks
                    var html = $(input).val().replace(/(?:\r\n|\r|\n)/g, '');

                    // Clean
                    html = clean(html);
                    
                    if (!data[rowIndex]) 
                        data[rowIndex] = [];

                    data[rowIndex][index] = html;
                    $(input).parent().parent().find('.item').html(html);
                });
            });

            $(settings.outputInput).val(JSON.stringify(data));
        }

        var clean = function(str){
            return String(str).replace(/"/g, '&quot;');
        }

        this.propagateChanges = function(editor, hideEditorAfter) {
            editor.save();
            setJsonToTextarea();
            
            if (hideEditorAfter) {
                removeAllEditors();
            }
        };

        return myThis;
    };
}(jQuery));
