var imageUpload = function(options) {

    options = $.extend({
        url: '',
        target: document.body,
        fileName: 'upload_files',
        inputName: 'images[]',
        size: 500 * 1024,
        width: 50,
        height: 50,
        multiple: true,
        isModal: false,
        limit: 0,
        handle: '.action-upload',
        file_input: '.action-file-input',
        insertWhere: null,
        callback: null
    }, options || {});

    // var compos = '<div class="choose-image"><span class="image-box"></span><b class="action-upload" title="选择图片"><i class="icon-arrow-right-b"></i></b></div>';

    // var file_input_old = $(options.target).find('input[type=file]' + options.file_input);
    // var file_input = file_input_old.clone(true, true).attr('name', '');

    // compos = $(compos).insertAfter(file_input_old).prepend(file_input);
    // file_input_old.remove();

    $(options.target).on('click', options.handle, function(e) {
        var handle = $(this);
        var container = handle.parent();
        var file_input = container.find(options.file_input);
        file_input.click();
    })
    .on('change', options.file_input, function(e) {
        var file_input = $(this);
        var container = file_input.parent();
        var handle = container.find(options.handle);

        var fileName = this.getAttribute('data-filename') || options.fileName;
        var size = this.getAttribute('data-size');
        size = Number(size) || options.size;


        var insertWhere = container.find(this.getAttribute('data-insertwhere') || options.insertWhere || '.image-box');

        var multiple = this.multiple;
        var isModal = this.getAttribute('data-ismodal');
        var url = this.getAttribute('data-remote') || options.url;
        var limit = this.getAttribute('data-max') || options.limit;
        var inputName = this.getAttribute('name') || options.inputName;
        var callback = this.getAttribute('data-callback') || file_input.data('callback') || options.callback;

        var data = new FormData();
        var files = this.files;

        if (limit) {
            var length = container.find('.img-thumbnail:not(.action-upload)').length,
                filelen;
            if(multiple && files) {
                filelen = files.length;
            }
            else {
                filelen = 1;
            }
            if(length + filelen > limit) {
                alert('超出限制，最多上传' + limit + '张。');
                return false;
            }
        }

        if(!files || !Array.prototype.slice.call(files).every(function (file, i) {
            if(file.size > size) {
                $('#messagebox').message('抱歉，上传图片 "' + file.name + '" 须小于' + size / 1024 + 'kB!');
                file_input.val('');
                return false;
            }
            if(multiple) {
                data.append(fileName + '[]', file);
            }
            else {
                data.append(fileName, file);
            }
            return true;
        })) return false;

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (rs) {
                try {
                    rs = JSON.parse(rs);
                }
                catch (e) {}
                if(rs.success && rs.data) {

                    if(callback) {
                        return callback(rs);
                    }

                    var html = '';
                    if(multiple) {
                        if(isModal) {
                            $('#areaselect_modal').modal('hide');

                            var type = $('.nav-tabs .active').attr('data-type');
                            if($('.has-searched')){
                                var name = $('.has-searched').val();
                            }else{
                                var name = '';
                            }
                            if($('.gallery-condition .active').hasClass('time')){
                                var orderBy = $('.gallery-condition .active').attr('data-order') + ' ' + $('.gallery-condition .active').attr('data-sort');
                            }else{
                                var orderBy = $('.gallery-condition .active').attr('data-order');
                            }
                            getList(type, orderBy, name);
                        }else{
                            insertWhere = handle;
                            $.each(rs.data, function (k, data) {
                                if(data.url) {
                                    html += '<div class="handle img-thumbnail"><i class="icon-close-b" onclick="$(this).parent().remove();"></i><img src="' + data.url +'"><input type="hidden" name="' + inputName + '" value="' + data.url +'"></div>';
                                }
                            });
                            $(html).insertBefore(insertWhere);
                        }
                    }else{
                        var data = rs.data[fileName];
                        if(data.url) {
                            html = '<img src="' + data.url +'"><input type="hidden" name="' + inputName + '" value="' + data.url +'">';
                        }
                        $(insertWhere).html(html);
                    }
                }else if(rs.message) {
                    $('#messagebox').message(rs.message);
                }
            },
            error: function () {
                $('#messagebox').message('上传出错，请重试');
            }
        });
    });
}

jQuery(function(){
    imageUpload();

    var button;

    $('#gallery_modal').on('show.bs.modal', function (event) {
        var that = $(this);
        button = $(event.relatedTarget);
    })

    $('#gallery_modal').on('click','.action-save',function(){
        var imgList = $('#gallery_modal').find('.checked');
        var imgsrc = imgList.find('img').attr('src');
        var url = imgList.find('.caption').attr('data-name');
        var img = '<img src="' + imgsrc + '">';
        if(imgList.length>0){
            button.find('.img-put').empty().append(img);
            button.find('input').val(url);
            $('#gallery_modal').modal('hide');
        }else{
            $('#messagebox').message('请选择图片!');
        }
    });


    $('#gallery_modal').on('hide.bs.modal', function (e) {
        $(this).removeData('bs.modal');
    })

    $('#gallery_modal').on('click','.thumbnail',function(){
        $(this).parent().parent().find('.thumbnail').removeClass('checked');
        $(this).addClass('checked');
    });

    $('.note-image-dialog').on('click','.action-save',function(){
        var imgList = $('.note-image-dialog').find('.checked');
        var imgsrc = imgList.find('img').attr('src');
        
        if(imgList.length>0){
            $('.note-image-dialog').modal('hide');
        }else{
            $('#messagebox').message('请选择图片!');
        }
    });

    $('.note-image-dialog').on('click','.thumbnail',function(){
        $(this).parent().parent().find('.thumbnail').removeClass('checked');
        $(this).addClass('checked');
        var imgList = $('.note-image-dialog').find('.checked');
        var url = imgList.find('.caption').attr('data-name');
        $(this).parents('.modal-body').find('.note-image-url').val(url);
    });
    multipleUpload();

    function multipleUpload(){
        $('.multiple-add').click(function(){
            var name = $(this).attr('data-name');
            var dataUrl = $(this).attr('data-url');
            var multiple =  '<div class="multiple-item">'
                            + '<div class="multiple-del glyphicon glyphicon-remove-circle"></div>'
                            + '<a class="select-image" data-toggle="modal" href="'+ dataUrl +'" data-target="#gallery_modal">'
                            + '<input type="hidden" name="'+ name +'" value="">'
                            + '<div class="img-put">'
                            + '<img src="">'
                            + '<i class="glyphicon glyphicon-picture"></i>'
                            + '</div>'
                            + '</a>'
                            + '</div>';
            var uploadItem = $(this).parents('.multiple-upload').find('.multiple-item');
            var limit = $(this).attr('data-limit');
            if(limit){
                $(this).before(multiple);
                if(uploadItem.length >= (limit-1)){
                    $(this).hide();
                    //$('#messagebox').message('最多添加'+ limit +'张图片!');
                }
            }else{
                $(this).before(multiple);
            }
        })
    }

    $('.multiple-upload').on('click','.multiple-del',function(){
        $(this).parents('.multiple-upload').find('.multiple-add').show();
        $(this).parent().remove();
    })

    function getList(type,orderBy,name) {
        $.post('<{url action=topshop_ctl_shop_image@search imageModal=true}>', {'img_type': type, 'orderBy': orderBy, 'image_name': name}, function(data) {
          $('.gallery-modal-content').empty().append(data);
        });
      }
});
