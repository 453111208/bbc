/**
 * AJAX File Upload
 * Modified by Tyler Chao
 */

(function($) {
    $.fn.AjaxFileUpload = function(options) {

        var defaults = {
            url:        'upload.php',
            /*size:       null,
            type:       null,
            limit:      0,*/
            onChange:   function(filename, container) {},
            onSubmit:   function(filename, container) {},
            onComplete: function(response, container) {}
        },
        settings = $.extend({}, defaults, options),
        randomId = (function() {
            var id = 0;
            return function() {
                return '_AjaxFileUpload' + id++;
            };
        })();

        return this.each(function() {
            var $this = $(this);
            $this.on('change', 'input[type=file]', function(e) {
                var size = $(this).data('size') || settings.size,
                    url = $(this).data('remote') || settings.url,
                    // type = $(this).attr('accept') || settings.type,
                    limit = $(this).data('max') || settings.limit;
                onChange(e, $this, url, size, limit);
            });
        });

        function onChange(e, container, url, size, limit) {
            var $element = $(e.target);
            if (limit) {
                var length = container.find('.img-thumbnail:not(.action-upload)').length,
                    filelen;
                if($element[0].multiple && $element[0].files) {
                    filelen = $element[0].files.length;
                }
                else {
                    filelen = 1;
                }
                if(length + filelen > limit) {
                    alert('超出限制，最多上传' + limit + '张。');
                    return false;
                }
            }
            var id       = $element.attr('id'),
                $clone   = $element.removeAttr('id').clone().attr('id', id),//.AjaxFileUpload(options),
                filename = $element.val().replace(/.*(\/|\\)/, ''),
                iframe   = createIframe(),
                form     = createForm(iframe, url, size, limit);

            // We append a clone since the original input will be destroyed
            $clone.insertBefore($element);

            settings.onChange.call($clone[0], filename, container);

            iframe.on('load', {element: $clone, form: form, container: container}, onComplete);

            form.append($element).on('submit', {element: $clone, iframe: iframe, filename: filename, container: container}, onSubmit).submit();
        }

        function onSubmit(e) {
            // e.stopPropagation();
            var data = settings.onSubmit.call(e.data.element, e.data.filename, e.data.container);

            // If false cancel the submission
            if (data === false) {
                // Remove the temporary form and iframe
                $(e.target).remove();
                e.data.iframe.remove();
                return false;
            } else {
                // Else, append additional inputs
                for (var variable in data) {
                    $('<input type="hidden" name="' + variable + '" value="' + data[variable] + '">')
                        .appendTo(e.target);
                }
            }
        }

        function onComplete (e) {
            var $iframe  = $(e.target),
                doc      = ($iframe[0].contentWindow || $iframe[0].contentDocument).document,
                response = $.trim($(doc.body).text());

            if (response) {
                response = JSON.parse(response);
            } else {
                response = {};
            }

            settings.onComplete.call(e.data.element, response, e.data.container);

            // Remove the temporary form and iframe
            e.data.form.remove();
            $iframe.remove();
        }

        function createIframe() {
            var id = randomId();

            return $('<iframe src="javascript:false;" width="0" height="0" frameborder="0" name="' + id + '" id="' + id + '" style="display: none;" />').appendTo('body');
        }

        function createForm(iframe, url, size, limit) {
            return $('<form method="post" action="' + url + '" enctype="multipart/form-data" encoding="multipart/form-data" target="' + iframe[0].name + '" />')
                .hide()
                .append('<input type="hidden" name="MAX_FILE_SIZE" value="' + (size || '') + '">')
                .append('<input type="hidden" name="FILE_LIMIT" value="' + (limit || '') + '">')
                // .append('<input type="hidden" name="ACCEPT_FILE_TYPE" value="' + (type || '') + '">')
                .appendTo('body');
        }
    };
})(jQuery);

$(function () {
    var uploader = $('.images-uploader');
    uploader.AjaxFileUpload({
        onComplete: function(rs, container) {
            if (rs.error) {
                return alert(rs.message);
            }
            var data = $.makeArray(rs.data);
            var name = container.find('input[type=file]').attr('name');
            $.each(data, function () {
                container.find('.action-upload').before('<div class="handle img-thumbnail"><i class="icon-close-b action-remove"></i><a href="' + this.url + '" target="_blank"><img src="' + this.url + '"></a><input type="hidden" name="' + name + '" value="' + this.url + '"></div>');
            });
            container.on('click', '.action-remove', function (e) {
                $(this).parent().remove();
            });
            container.on('mouseover', '.img-thumbnail', function (e) {
                $(this).find('.action-remove').show();
            });
            container.on('mouseout', '.img-thumbnail', function (e) {
                $(this).find('.action-remove').hide();
            });
        }
    });
    $(".img-thumbnail").hover(function(e){
        var src=$(this).find("input[type=hidden]").val();
        if(src){
            $(this).find('.action-remove').show();
        }
    },function(e){
         var src=$(this).find("input[type=hidden]").val();
        if(src){
            $(this).find('.action-remove').hide();
        }
    });
    $(".action-remove").click(function(e){
        $(this).parent().remove();
    });
})
