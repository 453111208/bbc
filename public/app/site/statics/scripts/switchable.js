/*
 * Switchable jQuery Plugin
 * @author: Jinpu Hu <jinpu.hu@qunar.com>
 * @modified: Tyler Chao
 */
(function($) {

    var DOT = '.';
    var EVENT_BEFORE_SWITCH = 'beforeSwitch.switchable';
    // var EVENT_SWITCH = 'switch.switchable';
    var EVENT_AFTER_SWITCH = 'afterSwitch.switchable';
    var CLASSPREFIX = 'switchable-';

    // Namespace Switchable
    $.extend({
        Switchable: Switchable
    });

    SP = Switchable.prototype;
    Switchable.Plugins = [];

    // Class Switchable
    function Switchable(container, config) {
        this.$container = $(container);
        this.config = $.extend({}, $.fn.switchable.defaults, config || {});
        this._init();
    };


    // Extend Switchable prototype
    $.extend(SP, {
        _init: function() {
            var self = this,
            config = this.config;

            this.activeIndex = config.activeIndex;

            this.$evtBDObject = $('<div />');

            this._parseStructure();

            if (config.hasTriggers) this._bindTriggers();
            if (config.hasFlips) this._bindFlips();

            $.each(Switchable.Plugins, function() {
                this._init(self);
            });
        },

        _parseStructure: function() {
            var self = this,
                $container = this.$container,
                config = this.config;

            switch (config.type) {
                case 0:
                    this.$triggers = $container.find(DOT + config.navCls).children();
                    this.$panels = $container.find(DOT + config.contentCls).children();
                    break;

                case 1:
                    this.$triggers = $container.find(DOT + config.triggerCls);
                    this.$panels = $container.find(DOT + config.panelCls);
                    break;
            }
            this.viewLength = this.$panels.length / config.step;
        },

        _bindTriggers: function() {
            var self = this, config = this.config,
            $triggers = this.$triggers, events = config.events;
            if(!$.isArray(events)) {
                events = events.split(',');
            }

            $triggers.each(function(index, trigger) {
                if ($.inArray('click', events) !== -1) {
                    $(trigger).click(function(evt) {
                        evt.preventDefault();
                        if (self.activeIndex === index) return self;
                        if (self.switchTimer) clearTimeout(self.switchTimer);
                        self.switchTimer = setTimeout(function() {
                            self.switchTo(index);
                        }, config.delay * 1000);

                        evt.stopPropagation();
                    });
                }

                if ($.inArray('hover', events) !== -1) {
                    $(trigger).hover(function(evt) {
                        if (self.activeIndex === index) return self;
                        if (self.switchTimer) clearTimeout(self.switchTimer);
                        self.switchTimer = setTimeout(function() {
                            self.switchTo(index);
                        }, config.delay * 1000);
                    }, function(evt) {
                        if (self.switchTimer) clearTimeout(self.switchTimer);
                        evt.stopPropagation();
                    });
                }
            });
        },

        _bindFlips: function() {
            var self = this,
                config = this.config,
                $backward = this.$container.find(config.backward),
                $forward = this.$container.find(config.forward);

                $backward.click(function(e) {
                    e.preventDefault();
                    self.backward();
                });
                $forward.click(function(e) {
                    e.preventDefault();
                    self.forward();
                });
        },

        beforeSwitch: function(fn) {
            fn = fn || this.config.onBeforeSwitch;
            if ($.isFunction(fn)) this.$container.on(EVENT_BEFORE_SWITCH, fn);
        },

        afterSwitch: function(fn) {
            fn = fn || this.config.onAfterSwitch;
            if ($.isFunction(fn)) this.$container.on(EVENT_AFTER_SWITCH, fn);
        },

        switchTo: function(index) {
            var self = this, config = this.config,
            triggers = $.makeArray(this.$triggers), panels = $.makeArray(this.$panels),
            activeIndex = this.activeIndex,
            step = config.step,
            fromIndex = activeIndex * step,
            toIndex = index * step;

            this.beforeSwitch();
            var evt = $.Event(EVENT_BEFORE_SWITCH);
            this.$container.trigger(evt, [index]);

            if (config.hasTriggers) {
                this._switchTrigger(activeIndex > -1 ? triggers[activeIndex] : null, triggers[index]);
            }

            this._switchView(
                panels.slice(fromIndex, fromIndex + step),
                panels.slice(toIndex, toIndex + step),
                index);

            // update activeIndex
            this.activeIndex = index;

            this.afterSwitch();
            evt = $.Event(EVENT_AFTER_SWITCH);
            this.$container.trigger(evt, [index]);  //# 是否还未完全实现
        },

        /**
         * 切换到上一视图
         */
        backward: function() {
            var activeIndex = this.activeIndex;
            this.switchTo(activeIndex > 0 ? activeIndex - 1 : this.viewLength - 1/*, BACKWARD*/);
        },

        /**
         * 切换到下一视图
         */
        forward: function() {
            var activeIndex = this.activeIndex;
            this.switchTo(activeIndex < this.viewLength - 1 ? activeIndex + 1 : 0/*, FORWARD*/);
        },

        _switchTrigger: function(fromTrigger, toTrigger) {

            var activeCls = this.config.activeCls;

            if (fromTrigger) $(fromTrigger).removeClass(activeCls);
            $(toTrigger).addClass(activeCls);
        },

        _switchView: function(fromPanels, toPanels, index) {
            // 最简单的切换效果：直接隐藏/显示
            $.each(fromPanels, function() {
                $(this).hide();
            });
            $.each(toPanels, function() {
                $(this).show();
            });
        }
    }); // EOF Switchable prototype extend

    $.fn.switchable = function(config) {
        var $self = this;

        // 维系Switchable对象将来可以访问
        var switchables = $self.data('switchables');
        $self.data('switchables', switchables ? switchables : []);

        return $self.each(function() {
            $self.data('switchables').push(new Switchable($(this), config));
        });
    };

    $.fn.switchable.defaults = {

        /**
         * @cfg Number type
         * 默认为0。

         * type为0，则通过navCls和contentCls来获取triggers和panels；

         * type为1，则通过triggerCls和panelCls来获取triggers和panels；
         */
        type: 0,

        /**
         * @cfg String navCls
         * 默认为switchable-nav，通过此类获取触发条件的容器，比如1 2 3 4 5的列表，这个class应该设置到ul或者ol上面，而不是每个触发条件li上面。
         */
        navCls: CLASSPREFIX + 'nav',

        /**
         * @cfg String contentCls
         * 默认为switchable-content，通过此类获取显示内容的容器，但不是具体的内容面板。
         */
        contentCls: CLASSPREFIX + 'content',

        /**
         * @cfg String triggerCls
         * 默认为switchable-trigger，通过此类获取具体的触发条件，此情况下，一般触发条件不在同一个容器。
         */
        triggerCls: CLASSPREFIX + 'trigger',

        /**
         * @cfg String panelCls
         * 默认为switchable-panel，通过此类获取具体的显示内容面板，此情况下，一般内容面板不在同一个容器。
         */
        panelCls: CLASSPREFIX + 'panel',

        /**
         * @cfg Boolean hasTriggers
         * 默认为true，是否有可见的触发条件。
         */
        hasTriggers: true,

        /**
         * @cfg Number activeIndex
         * 默认为0，初始时被激活的索引。
         */
        activeIndex: 0,

        /**
         * @cfg String activeCls
         * 默认为active，被激活时的css样式名。
         */
        activeCls: 'active',

        /**
         * @cfg Array events
         * 触发条件事件响应数组，目前支持click和hover。
         */
        events: ['click', 'hover'],

        // 是否有翻页按钮
        hasFlips: false,
        // 翻页按钮的dom节点
        backward: '.prev',
        forward: '.next',

        /**
         * @cfg Number step
         * 默认为1，一次切换的内容面板数。
         */
        step: 1,

        /**
         * @cfg Number delay
         * 默认为0.1秒，延迟执行切换的时间间隔。
         */
        delay: 0.1,

        /**
         * @cfg Array viewSize
         * 一般自动设置，除非自己需要控制，显示内容面板的[宽, 高]，如果[680]、[320, 150]。
         */
        viewSize: []
    };

    /*
     * Switchable autoplay Plugin
     */

    $.extend($.fn.switchable.defaults, {

        /**
         * @cfg Boolean autoplay
         * 默认为false，不自动播放。
         */
        autoplay: false,

        /**
         * @cfg Number interval
         * 默认为3，自动播放间隔时间。
         */
        interval: 3,

        /**
         * @cfg Boolean pauseOnHover
         * 默认为true，鼠标悬停在容器上是否暂停自动播放
         */
        pauseOnHover: true
    });

    Switchable.Plugins.push({
        name: 'autoplay',

        _init: function(host) {
            var config = host.config;
            if (!config.autoplay) return;

            // 鼠标悬停，停止自动播放
            if (config.pauseOnHover) {

                host.$container.hover(function(evt) {
                    host.paused = true;
                }, function(evt) {
                    // because target can be child of evt set container
                    if (evt.currentTarget !== evt.target && host.$container.has(evt.target).length === 0) return;

                    // setTimeout interval 是为了确保自动播放的间隔不会小于间隔时间
                    setTimeout(function() {
                        host.paused = false;
                    }, config.interval * 1000);
                });
            }

            // 设置自动播放
            host.autoplayTimer = setInterval(function() {
                if (host.paused) return;
                host.switchTo(host.activeIndex < host.viewLength - 1 ? host.activeIndex + 1 : 0);
            }, config.interval * 1000, true);
        }
    });

    /*
     * Switchable effect Plugin
     * @author: Jinpu Hu <jinpu.hu@qunar.com>
     */

    var Effects;

    var DISPLAY = 'display';
    var BLOCK = 'block';

    var OPACITY = 'opacity';

    var ZINDEX = 'z-index';
    var POSITION = 'position';
    var RELATIVE = 'relative';
    var ABSOLUTE = 'absolute';

    var SCROLLX = 'scrollx';
    var SCROLLY = 'scrolly';

    var NONE = 'none';
    var FADE = 'fade';
    var LINEAR = 'linear';
    var SWING = 'swing';

    $.extend($.fn.switchable.defaults, {
        /**
         * @cfg String effect
         * 默认为none，即只是显示隐藏，目前支持的特效为scrollx、scrolly、fade或者自己直接传入特效函数。
         */
        effect: NONE,

        /**
         * @cfg Number duration
         * 默认为.5，动画的时长。
         */
        duration: 0.5,

        /**
         * @cfg String easing
         * 默认为liner，即线性的。
         */
        easing: LINEAR,

        /**
         * @cfg Boolean circle
         * 默认为false，如果设置为true，最后一帧到第一帧切换的时候会更加平滑。
         */
        circle: false
    });

    /**
     * 定义效果集
     */
    Switchable.Effects = {

        // 最朴素的显示/隐藏效果
        none: function(fromPanels, toPanels, callback) {
            $.each(fromPanels, function() {
                $(this).hide();
            });
            $.each(toPanels, function() {
                $(this).show();
            });
            callback();
        },

        // 淡隐淡现效果
        fade: function(fromPanels, toPanels, callback) {
            if(fromPanels.length !== 1) {
                return; //fade effect only supports step == 1.
            }
            var self = this, config = self.config,
                fromPanel = fromPanels[0], toPanel = toPanels[0];
            if (self.$anim) self.$anim.clearQueue();

            if(self.viewLength == 1) {
                return;
            }
            // 首先显示下一张
            $(toPanel).css(OPACITY, 1);

            // 动画切换
            self.$anim = $(fromPanel).animate({
                opacity: 0,
                duration: config.duration,
                easing: config.easing
            }, function() {
                self.$anim = null; // free

                // 切换 z-index
                $(toPanel).css(ZINDEX, 9);
                $(fromPanel).css(ZINDEX, 1);
                callback();
            });
        },

        // 水平/垂直滚动效果
        scroll: function(fromPanels, toPanels, callback, index) {
            var self = this, config = self.config,
                isX = config.effect === SCROLLX,
                diff = self.viewSize[isX ? 0 : 1] * index,
                attributes = {};

            var $first, _diff;
            if (config.circle && index == 0 && self.activeIndex == self.viewLength-1) {
                $first = $(toPanels);
                if (!self.$container.data('switchables-circle_appended')) {
                    $first.parent().append( $first.clone() );
                    self.$container.data('switchables-circle_appended', true);
                    if (isX)
                        $first.parent().css('width', self.viewSize[0] * (self.viewLength +1) + 'px');
                }
                _diff = diff;
                diff = self.viewSize[isX ? 0 : 1] * self.viewLength;
            }

            attributes[isX ? 'left' : 'top'] = -diff;
            $.extend(attributes, {
                duration: config.duration,
                easing: config.easing
            });

            if (self.$anim) self.$anim.clearQueue();

            self.$anim = self.$panels.parent().animate(attributes, function() {
                if ($first) {
                //  $first.parent().children(':gt('+ (self.viewLength-1) +')').remove();  //# IE8 下会有显示问题，若先设置 top 再删除多余元素
                    $first.parent().css( isX ? 'left' : 'top', _diff);
                    $first = null;
                }
                self.$anim = null; // free
                callback();
            });
        }

    };
    Effects = Switchable.Effects;
    Effects[SCROLLX] = Effects[SCROLLY] = Effects.scroll;

    Switchable.Plugins.push({
        name: 'effect',

        /**
         * 根据 effect, 调整初始状态
         */
        _init: function(host) {
            var config = host.config,
                effect = config.effect,
                $panels = host.$panels,
                step = config.step,
                activeIndex = host.activeIndex,
                fromIndex = activeIndex * step,
                toIndex = fromIndex + step - 1,
                panelLength = $panels.length;

            // 1. 获取高宽
            host.viewSize = [
                config.viewSize[0] || $panels.outerWidth() * step,
                config.viewSize[1] || $panels.outerHeight() * step
            ];
            // 注：所有 panel 的尺寸应该相同
            //    最好指定第一个 panel 的 width 和 height，因为 Safari 下，图片未加载时，读取的 offsetHeight 等值会不对

            // 2. 初始化 panels 样式
            if (effect !== NONE) { // effect = scrollx, scrolly, fade
                // 这些特效需要将 panels 都显示出来
                $panels.css(DISPLAY, BLOCK);

                switch (effect) {

                    // 如果是滚动效果
                    case SCROLLX:
                    case SCROLLY:
                        // 设置定位信息，为滚动效果做铺垫
                        $panels.parent().css('position', ABSOLUTE);
                        $panels.parent().parent().css('position', RELATIVE); // 注：content 的父级不一定是 container

                        // 水平排列
                        if (effect === SCROLLX) {
                            $panels.css('float', 'left');

                            // 设置最大宽度，以保证有空间让 panels 水平排布
                            $panels.parent().css('width', host.viewSize[0] * host.viewLength + 'px');
                        }
                        break;

                    // 如果是透明效果，则初始化透明
                    case FADE:
                        $panels.each(function(index) {
                            $(this).css({
                                opacity: (index >= fromIndex && index <= toIndex) ? 1 : 0,
                                position: ABSOLUTE,
                                zIndex: (index >= fromIndex && index <= toIndex) ? 9 : 1
                            });
                        });
                        break;
                }
            }
            else {
                $panels.eq(activeIndex).siblings($panels.selector() + ':visible').css(DISPLAY, NONE);
            }

            // 3. 在 CSS 里，需要给 container 设定高宽和 overflow: hidden
            //    nav 的 cls 由 CSS 指定
        }
    });

    /**
     * 覆盖切换方法
     */
    $.extend(SP, {
        /**
         * 切换视图
         */
        _switchView: function(fromPanels, toPanels, index) {
            var self = this, config = self.config,
                effect = config.effect,
                fn = $.isFunction(effect) ? effect : Effects[effect];

            fn.call(self, fromPanels, toPanels, function() {}, index);
        }
    });

    /*
     * Switchable lazyload Plugin
     * @author: Jinpu Hu <jinpu.hu@qunar.com>
     * depend on jquery.lazyload.js
     */
    var DATA_TEXTAREA = 'action-lazyload', DATA_IMG = 'data-src';

    $.extend($.fn.switchable.defaults, {
        /**
         * @cfg Boolean lazyload
         * 默认为false，即不延迟加载。
         */
        lazyload: false,

        /**
         * @cfg String lazyDataType
         * 默认为data-src，支持图片延迟加载，及文本数据和脚本延迟加载。
         */
        lazyDataType: DATA_IMG // or DATA_TEXTAREA

    });


    Switchable.Plugins.push({
        name: 'lazyload',

        _init: function(host) {
            var config = host.config;
            if (!config.lazyload) return;

            var panels = $.makeArray(host.$panels);
            var type = config.lazyDataType;

            host.beforeSwitch(loadLazyData);
            host.$container.trigger(EVENT_BEFORE_SWITCH);

            /**
             * 加载延迟数据
             */
            function loadLazyData(evt, index) {
                var step = config.step,
                    begin = index * step,
                    end = begin + step;

                $.loadCustomLazyData(panels.slice(begin, end), type);

                if (isAllDone()) {
                    host.afterSwitch();
                    var evt = $.Event(EVENT_AFTER_SWITCH);
                    host.$container.trigger(evt).off(EVENT_BEFORE_SWITCH, loadLazyData);
                }
            }

            /**
             * 是否都已加载完成
             */
            function isAllDone() {
                var $imgs, isDone = true;

                if (type === DATA_IMG) {

                    $imgs = panels[0].nodeName == 'IMG' ? host.$panels : host.$panels.find('img');

                    $imgs.each(function() {
                        if (this.getAttribute(type)) {
                            isDone = false;
                            return false;
                        }

                    });
                }
                // TODO textarea

                return isDone;
            }
        }
    });

    function _loadImgSrc(img, type) {
        var data_src = img.getAttribute(type);

        if (data_src && img.src != data_src) {
            img.src = data_src;
            img.removeAttribute(type);
        }
    };

    $.extend({
        loadCustomLazyData: function(containers, type, execScript) {

            var $imgs, $area;

            $.each(containers, function() {
                switch (type) {
                    case DATA_TEXTAREA:
                        // 通过textarea延迟加载内容(图片，网页html，脚本)
                        if (this.nodeName === 'TEXTAREA' && $(this).hasClass(DATA_TEXTAREA)) {
                            $area = $(this);
                        } else {
                            $area = $(this).find('textarea.' + DATA_TEXTAREA);
                        }
                        var content = $('<div/>').insertAfter($area);
                        if($area && $area.val()) {
                            evalScripts($area.val(), content, execScript);
                            $area.remove();
                        }
                        break;
                    case DATA_IMG:
                    default:
                        if (this.nodeName === 'IMG' && $(this).attr(DATA_IMG)) { // 本身就是图片
                            $imgs = $(this);
                        } else {
                            $imgs = $(this).find('img[' + DATA_IMG + ']');
                        }
                        $imgs && $imgs.each(function() {
                            _loadImgSrc(this, type);
                        });
                }
            });
        }
    });

    Switchable.autoRender = function(autoAttr,container) {
        var self = this;
        autoAttr = autoAttr || '[data-toggle=switchable]';

        var switchableElements = $(container || document.body).find(autoAttr);
        // var lazyloadElements = $('img[data-src], textarea.action-lazyload');
        $.loadCustomLazyData($(container || document.body), 'action-lazyload');
        switchableElements.each(function() {
            try {
                var config = $(this).data('switchableConfig');
                var options = dataOptions(this, 'switchable');
                if(typeof config === 'string') config = JSON.parse(config);
                $(this).removeData('switchableConfig');
                $.extend(true, config, options || {});
                new self($(this), config);
            } catch(e) {}
        });
    };

})(jQuery);

jQuery(function($){
    $.Switchable.autoRender();
});
