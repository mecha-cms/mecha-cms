/**
 * AJAX Button
 * -----------
 *
 *    <button class="ajax" data-url="/path/to/action" data-loading-text="Loading&hellip;" data-error-text="Error." data-source="#my-form" data-destination="#my-div">Load!</button>
 *    <form id="my-form"></form>
 *    <div id="my-div"></div>
 *
 */

(function($, base) {

    var $btn = $('.ajax');

    if (!$btn.length) return;

    $btn.on("click", function(e) {

        var _this = this,
            $this = $(_this),
            $source = $($this.data('source')),
            _error = $this.attr('data-error-text') ? $this.data('errorText') : "",
            _action = $this.attr('data-url') ? $this.data('url') : $source.attr('action'),
            _loading = $this.attr('data-loading-text') ? $this.data('loadingText') : "",
            $destination = $this.attr('data-destination') ? $($this.data('destination')) : $this.next();

        $destination.html(_loading);

        base.fire('on_ajax_begin', [e, _this]);

        $.ajax({
            url: _action,
            type: 'POST',
            data: $source.serializeArray(),
            success: function(data, status, xhr) {
                $destination.html(data);
                base.fire('on_ajax_success', {
                    'data': data,
                    'status': status,
                    'xhr': xhr,
                    'event': e,
                    'target': _this
                });
                base.fire('on_ajax_end', {
                    'event': e,
                    'target': _this
                });
            },
            error: function(xhr, status, error) {
                $destination.html(_error);
                base.fire('on_ajax_error', {
                    'xhr': xhr,
                    'status': status,
                    'error': error,
                    'event': e,
                    'target': _this
                });
                base.fire('on_ajax_end', {
                    'event': e,
                    'target': _this
                });
            }
        });

        return false;

    });

})(Zepto, DASHBOARD);