(function(gmu, $, undefined) {
 
    gmu.define('Message', {
        options: {
            container: '#message',
            prefix: 'message',
            target: document.body,
            type: 'error',
            hideDelay: 3 //秒数
            /*show: $nil,
            hide: $nil*/
        }, 

        _create: function() {
            var $el = this.getEl();
            if(!$el || !$el.length) {
                this.$el = $('<div id="message" class="message message-' + this._options.type + ' fade"></div>');
            }
            this.$el.appendTo(this._options.target || document.body);

            return this;
        },

        getEl: function() {
            this.$el = $(this._options.container);
            return this.$el;
        },

        show: function(msg, type, options) {
            if(!msg) return;
            if($.isPlainObject(type)) {
                options = type;
                type = null;
            }
            $.extend(true, this._options, options || {});
            
            clearTimeout(this.$el[0].timer);

            this.$el[0].className = [this._options.prefix, type ? this._options.prefix + '-' + type : null, 'fade', 'in'].join(' ');
            this.$el.html(msg)
            // .fix()
            .position({
                my: 'center',
                at: 'center',
                of: window
            });

            this.trigger('show');

            if(this._options.hideDelay) {
                this.$el[0].timer = setTimeout($.proxy(function() {
                    this.hide(options);
                }, this), (parseInt(this._options.hideDelay, 10) || 3) * 1000);
            }

            return this;
        },

        hide: function(options) {
            $.extend(true, this._options, options || {});

            this.$el.removeClass('in').addClass('out');
            this.trigger('hide');
            
            return this;
        }, 

        success: function(msg, options) {
            return this.show(msg, 'success', options);
        },

        error: function(msg, options) {
            return this.show(msg, 'error', options);
        }
    });   
})(gmu, gmu.$);
