/**
 * Adds Koumpounophobia buttons to the HTML Editor button bar, and controls
 * its modal input dialogs
 *
 * This file is part of Koumpounophobia. Please see the Kphobia.php
 * file for copyright and license information.
 *
 * @author Michael Toppa
 * @version 0.5
 * @package Koumpounophobia
 *
 */

jQuery(document).ready(function($) {
    // need to convert the kphobia data strings back to arrays
    var kbHandles = kphobiaButtons.handles.split(',');
    var kbTags = kphobiaButtons.tags.split(',');
    var kbTitles = kphobiaButtons.titles.split(',');
    var kbIds = kphobiaButtons.ids.split(',');
    var kbSelfClose = kphobiaButtons.self_close.split(',');
    var kbShortcodes = kphobiaButtons.shortcodes.split(',');
    var kbInputDialogs = kphobiaButtons.input_dialogs.split(',');

    $.each(kbHandles, function(index, handle) {
        if (kbShortcodes[index] == 'y') {
            var tagOpen = '[';
            var tagClose = ']';
        }

        else {
            var tagOpen = '<';

            if (kbSelfClose[index] == 'y') {
                var tagClose = ' />';
            }

            else {
                var tagClose = '>';
            }
        }

        if (kbInputDialogs[index] == 'y') {
            var inputs = $('#kphobia_' + handle
                + '_form select, #kphobia_' + handle
                + '_form input, #kphobia_' + handle
                + '_form textarea');

            // need to set to explicitly set height and width
            // to auto - probably because of the abstraction
            $('#kphobia_' + handle + '_dialog').dialog({
                autoOpen:false,
                modal:true,
                height: 'auto',
                width: 'auto',
                overlay: { "background-color": "#000", "opacity": "0.75" }
            });
        }

        var ed_button = '<input type="button" value="' + handle
            + '" title="' + kbTitles[index]
            + '" class="ed_button" id="' + kbIds[index] + '" />';

        if ($('#' + kbIds[index]).length) {
            $('#' + kbIds[index]).after(ed_button).remove();
        }

        else {
            $('#ed_toolbar').append(ed_button);
        }

        // handle dialog form submit
        $('#kphobia_' + handle + '_form').ajaxForm({
            beforeSubmit: function() {
                // remind IE of the cursor position/selected text
                if (window.kphobiaRange) {
                    window.kphobiaRange.select();
                }

                // remind other browsers of the scroll position
                if (window.kphobiaScrollTop) {
                    edCanvas.scrollTop = window.kphobiaScrollTop;
                }

                var tag = tagOpen + kbTags[index];

                inputs.each(function(i, el) {
                    var clean_val = $(el).val().replace(/\"/g, "&quot;");

                    if (clean_val != "" && el.type != 'submit') {
                        tag = tag + ' ' + el.name + '="' + clean_val + '"';
                    }
                });

                tag = tag + tagClose;

                if (kbSelfClose[index] == 'y') {
                    edInsertContent(edCanvas, tag);
                }

                // If the tag isn't self-closing then we need to add it to the
                // edButtons array, so the button can be updated to provide
                // the closing tag. Note we painted the button to the screen
                // without using edButtons (since we don't have runtime access
                // to it), but as long as we have consistent IDs, we can use
                // edButtons now to update the button.
                else {
                    var i = edButtons.length;
                    edButtons[i] = new edButton(kbIds[index], handle, tag, tagOpen + '/' + kbTags[index] + tagClose);
                    edInsertTag(edCanvas,i);
                    // removing the button causes unpredictable behavior - leaving it seems harmless
                    //edButtons.pop();
                }

                $('#kphobia_' + handle + '_dialog').dialog('close');
                $('#kphobia_' + handle + '_form').resetForm();

                // return false to prevent normal browser submit
                return false;
            }
        });

        // click handler
        $('#' + kbIds[index]).click(function() {
            // insert a closing tag
            if ($('#' + kbIds[index]).val() == '/' + handle) {
                // non-IE browsers lose the scroll position, so save it
                if (!$.browser.msie) {
                    window.kphobiaScrollTop = edCanvas.scrollTop;
                }

                edInsertContent(edCanvas, tagOpen + '/' + kbTags[index] + tagClose);
                $('#' + kbIds[index]).val(handle);

                // restore scroll position
                if (!$.browser.msie) {
                    edCanvas.scrollTop = window.kphobiaScrollTop;
                }
            }

            // push a modal dialog
            else if (kbInputDialogs[index] == 'y') {
                // IE will forget the selected text/cursor position after the
                // dialog gains focus, so save it - need to force focus first
                if (document.selection) {
                    document.getElementById('content').focus();
                    window.kphobiaRange = document.selection.createRange();
                }

                // other browsers forgot the editor's scroll position
                else {
                    window.kphobiaScrollTop = edCanvas.scrollTop;
                }

                $('#kphobia_' + handle + '_dialog').dialog('open');
            }

            // insert self closing tag (no modal dialog)
            else if (kbSelfClose[index] == 'y') {
                edInsertContent(edCanvas, tagOpen + kbTags[index] + tagClose);
            }

            // insert opening tag (not self closing, no modal dialog)
            else {
                var tag = tagOpen + kbTags[index] + tagClose;
                var i = edButtons.length;
                edButtons[i] = new edButton(kbIds[index], handle, tag, tagOpen + '/' + kbTags[index] + tagClose);
                edInsertTag(edCanvas,i);
            }
        });

        $('#kphobia_' + handle + '_close').click(function() {
            $('#kphobia_' + handle + '_dialog').dialog('close');
        });
    });
});

