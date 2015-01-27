/**
 * Article/Page Editor
 * -------------------
 */

(function($, base) {

    var $zone = $(document.body),
        $editor = $('.form-compose').first(),
        $preview = $('.editor-preview'),
        $title = $('[name="title"]', $editor),
        $slug = $('[name="slug"]', $editor),
        $content = $('.MTE[name="content"]', $editor),
        $tab = $('.tab-area a'),
        $check = $('input[type="checkbox"]', $editor),
        $css = $('.MTE[name="css"]', $editor),
        $js = $('.MTE[name="js"]', $editor),
        $css_check = $('[name="css_live_check"]', $editor),
        $js_check = $('[name="js_live_check"]', $editor);

    $zone.removeClass('no-js').addClass('js');

    var $css_preview = $('<div id="live-preview-css"></div>').appendTo($zone),
        $js_preview = $('<div id="live-preview-js"></div>').appendTo($zone);

    var languages = $content.data('mteLanguages');

    $tab.on("click", function() {
        if (this.hash.replace('#', "") == 'tab-content-4') { // preview tab only
            $preview.html($preview.data('progressText'));
            $.ajax({
                url: $editor.data('previewUrl'),
                type: 'POST',
                data: $editor.serializeArray(),
                success: function(data, textStatus, jqXHR) {
                    $preview.html(data);
                    base.fire('on_preview_complete', [data, textStatus, jqXHR]);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $preview.html($preview.data('errorText'));
                    base.fire('on_preview_failure', [jqXHR, textStatus, errorThrown]);
                }
            });
        }
        return false;
    });

    if ($content.length && typeof MTE != "undefined") {
        base.composer = new MTE($content[0], {
            tabSize: base.tab_size,
            shortcut: true,
            toolbarClass: 'editor-toolbar cf',
            buttons: languages.buttons,
            prompt: languages.prompt,
            placeholder: languages.placeholder,
            keydown: function(e, editor) {
                base.fire('on_editor_keydown', [e, editor]);
            },
            click: function(e, editor, type) {
                base.fire('on_editor_click', [e, editor, type]);
            },
            ready: function(editor) {
                base.fire('on_editor_ready', [editor]);
            }
        });
        base.composer.button('table', {
            'title': languages.others.table,
            'position': 8,
            'click': function(e, editor) {
                var s = editor.grip.selection(),
                    p = base.is_html_parser_enabled,
                    table = languages.others['table_text_' + (p ? 'raw' : 'html')];
                table = table.replace(/\t/g, base.tab_size);
                editor.grip.insert(table, function() {
                    editor.grip.select(s.start + (p ? 0 : 25 + (base.tab_size.length * 6)), s.start + table.indexOf(p ? ' |' : '</th>'), function() {
                        editor.grip.updateHistory();
                    });
                });
            }
        });
        base.composer.button('question-circle', {
            'title': languages.others.help,
            'click': function() {
                window.open('http://mecha-cms.com/article/markdown-syntax');
            }
        });
        new MTE($css[0], {
            tabSize: base.tab_size,
            toolbar: false
        });
        new MTE($js[0], {
            tabSize: base.tab_size,
            toolbar: false
        });
    }

    if ($('.btn-destruct').length === 0) {
        $.slugger($title, $slug, '-');
    }

    var timer = null;

    $css.on("keyup", function() {
        window.clearTimeout(timer);
        timer = window.setTimeout(function() {
            if ($css_check.is(':checked')) $css_preview.html($css.val());
        }, 15);
    });

    $js.on("keyup", function() {
        window.clearTimeout(timer);
        timer = window.setTimeout(function() {
            if ($js_check.is(':checked')) $js_preview.html($js.val());
        }, 15);
    });

    $css_check.on("change", function() {
        if (this.checked) {
            $css.trigger("keyup");
        } else {
            $css_preview.html("");
        }
    });

    $js_check.on("change", function() {
        if (this.checked) {
            $js.trigger("keyup");
        } else {
            $js_preview.html("");
        }
    });

})(Zepto, DASHBOARD);