/*
 * Author: tylerchao
 * Date: 2014/08/08
 **/

/*
 * Global variables, from the sass files.
 */
var left_side_width = 220; //Sidebar width in pixels

$(function() {
    "use strict";

    //Enable sidebar toggle
    $("[data-toggle='offcanvas']").click(function(e) {
        e.preventDefault();

        //If window is small enough, enable sidebar push menu
        if ($(window).width() <= 992) {
            $('.row-offcanvas').toggleClass('active');
            $('.left-side').removeClass("collapse-left");
            $(".right-side").removeClass("strech");
            $('.row-offcanvas').toggleClass("relative");
        } else {
            //Else, enable content streching
            $('.left-side').toggleClass("collapse-left");
            $(".right-side").toggleClass("strech");
        }
    });

    //Add hover support for touch devices
    $('.btn').bind('touchstart', function() {
        $(this).addClass('hover');
    }).bind('touchend', function() {
        $(this).removeClass('hover');
    });

    //Activate tooltips
    $("[data-toggle='tooltip']").tooltip();

    /*
     * Add collapse and remove events to boxes
     */
    $("[data-widget='collapse']").click(function() {
        //Find the box parent
        var box = $(this).parents(".box").first();
        //Find the body and the footer
        var bf = box.find(".box-body, .box-footer");
        if (!box.hasClass("collapsed-box")) {
            box.addClass("collapsed-box");
            //Convert minus into plus
            $(this).children(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
            bf.slideUp();
        } else {
            box.removeClass("collapsed-box");
            //Convert plus into minus
            $(this).children(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
            bf.slideDown();
        }
    });

    /*
     * ADD SLIMSCROLL TO THE TOP NAV DROPDOWNS
     * ---------------------------------------
     */
    $(".navbar .menu").slimscroll({
        height: "200px",
        alwaysVisible: false,
        size: "3px"
    }).css("width", "100%");

    /*
     * INITIALIZE BUTTON TOGGLE
     * ------------------------
     */
    $('.btn-group[data-toggle="btn-toggle"]').each(function() {
        var group = $(this);
        $(this).find(".btn").click(function(e) {
            group.find(".btn.active").removeClass("active");
            $(this).addClass("active");
            e.preventDefault();
        });

    });

    $("[data-widget='remove']").click(function() {
        //Find the box parent
        var box = $(this).parents(".box").first();
        box.slideUp();
    });

    /* Sidebar tree view */
    $(".sidebar .treeview").tree();

    /*
     * Make sure that the sidebar is streched full height
     * ---------------------------------------------
     * We are gonna assign a min-height value every time the
     * wrapper gets resized and upon page load. We will use
     * Ben Alman's method for detecting the resize event.
     *
     **/
    function _fix() {
        //Get window height and the wrapper height
        var height = $(window).height() - ($("body > .header").outerHeight() || 0) - ($("body > .footer").outerHeight() || 0);
        var content = $(".wrapper").height();
        $(".wrapper").css("min-height", height);
        //Set sidebar height to the wrapper or the height of the window if who's higher
        $(".left-side, html, body").css("min-height", Math.max(content, height));
    }
    //Fire upon load
    _fix();
    //Fire when wrapper is resized
    $(window).resize(function() {
        _fix();
        fix_sidebar();
    });

    //Fix the fixed layout sidebar scroll bug
    fix_sidebar();


    //form validator
    $('form:not([novalidate])').Validator({
        icons: {
            valid:      'icon icon-checkmark-a',
            invalid:    'icon icon-alert',
            validating: 'icon icon-loading-c'
        }
    });

    //click thumbnail to show the big image
    $('body').on('click', '.show-pics a', function(e) {
        e.preventDefault();
        var imgUrl = $(this).attr('href');
        if(!imgUrl){
            return false;
        }else{
            $('#show_img_madal').modal('show');
            $('#big_img').attr('src',imgUrl);
        };
    });

});
function fix_sidebar() {
    //Make sure the body tag has the .fixed class
    if (!$("body").hasClass("fixed")) {
        return;
    }

    //Add slimscroll
    $(".sidebar").slimscroll({
        height: ($(window).height() - $(".header").height()) + "px",
        color: "rgba(0,0,0,0.2)"
    });
}
/*END DEMO*/


/*
 * BOX REFRESH BUTTON
 * ------------------
 * This is a custom plugin to use with the compenet BOX. It allows you to add
 * a refresh button to the box. It converts the box's state to a loading state.
 *
 * USAGE:
 *  $("#box-widget").boxRefresh( options );
 * */
(function($) {
    "use strict";

    $.fn.boxRefresh = function(options) {

        // Render options
        var settings = $.extend({
            //Refressh button selector
            trigger: ".refresh-btn",
            //File source to be loaded (e.g: ajax/src.php)
            source: "",
            //Callbacks
            onLoadStart: function(box) {
            }, //Right after the button has been clicked
            onLoadDone: function(box) {
            } //When the source has been loaded

        }, options);

        //The overlay
        var overlay = $('<div class="overlay"></div><div class="loading-img"></div>');

        return this.each(function() {
            //if a source is specified
            if (settings.source === "") {
                if (console) {
                    console.log("Please specify a source first - boxRefresh()");
                }
                return;
            }
            //the box
            var box = $(this);
            //the button
            var rBtn = box.find(settings.trigger).first();

            //On trigger click
            rBtn.click(function(e) {
                e.preventDefault();
                //Add loading overlay
                start(box);

                //Perform ajax call
                box.find(".box-body").load(settings.source, function() {
                    done(box);
                });


            });

        });

        function start(box) {
            //Add overlay and loading img
            box.append(overlay);

            settings.onLoadStart.call(box);
        }

        function done(box) {
            //Remove overlay and loading img
            box.find(overlay).remove();

            settings.onLoadDone.call(box);
        }

    };

})(jQuery);

/*
 * SIDEBAR MENU
 * ------------
 * This is a custom plugin for the sidebar menu. It provides a tree view.
 *
 * Usage:
 * $(".sidebar).tree();
 *
 * Note: This plugin does not accept any options. Instead, it only requires a class
 *       added to the element that contains a sub-menu.
 *
 * When used with the sidebar, for example, it would look something like this:
 * <ul class='sidebar-menu'>
 *      <li class="treeview active">
 *          <a href="#>Menu</a>
 *          <ul class='treeview-menu'>
 *              <li class='active'><a href=#>Level 1</a></li>
 *          </ul>
 *      </li>
 * </ul>
 *
 * Add .active class to <li> elements if you want the menu to be open automatically
 * on page load. See above for an example.
 */
(function($) {
    "use strict";

    $.fn.tree = function() {

        return this.each(function() {
            var btn = $(this).children("a").first();
            var menu = $(this).children(".treeview-menu").first();
            var isActive = $(this).hasClass('active');

            //initialize already active menus
            if (isActive) {
                menu.show();
                btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
            }
            //Slide open or close the menu on link click
            btn.click(function(e) {
                e.preventDefault();
                if (isActive) {
                    //Slide up to close menu
                    menu.slideUp();
                    isActive = false;
                    btn.children(".fa-angle-down").first().removeClass("fa-angle-down").addClass("fa-angle-left");
                    btn.parent("li").removeClass("active");
                } else {
                    //Slide down to open menu
                    menu.slideDown();
                    isActive = true;
                    btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
                    btn.parent("li").addClass("active");
                }
            });

            /* Add margins to submenu elements to give it a tree look */
            menu.find("li > a").each(function() {
                var pad = parseInt($(this).css("margin-left")) + 10;

                $(this).css({"margin-left": pad + "px"});
            });

        });

    };


}(jQuery));


/* CENTER ELEMENTS */
(function($) {
    "use strict";
    jQuery.fn.center = function(options) {
        options = $.extend(true, {}, options);

        if (options.parent) {
            parent = this.parent().css('position', 'relative');
        } else {
            parent = window;
        }
        this.css({
            // "position": "absolute",
            "top": options.top === 'top' ? 0 : options.top ? options.top : ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
            "left": options.left === 'left' ? 0 : options.left ? options.left : ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
        });
        return this;
    }
}(jQuery));

(function ($) {
    /* Twitter Bootstrap Message Helper
    ** Usage: Just select an element with `alert` class and then pass this object for options.
    ** Example: $("#messagebox").message("Hello world!", {type: "error"});
    ** Author: Afshin Mehrabani <afshin.meh@gmail.com>
    ** Date: Monday, 08 October 2012
    */
    $.fn.message = function(text, type, delay, callback) {
        //remove all previous bootstrap alert box classes
        var $this = this;
        var top;

        if(this.data('timer')) {
            clearTimeout(this.data('timer'));
            this.timer = null;
        }
        this[0].className = this[0].className.replace(/alert-(success|error|warning|info|danger)/g , '');
        this.html(text).addClass('alert-' + (type || 'error'));
        if($('header.header').size()) {
            top = $('header.header').height();
        }
        else {
            top = 'top';
        }
        this.fadeIn('fast').center({top:top});

        this.data('timer', setTimeout(function() {
            $this.fadeOut('fast');
        }, delay || 3000));
    };
})(jQuery);

/*!
 * jquery.resize.js 0.0.1 - https://github.com/yckart/jquery.resize.js
 * Resize-event for DOM-Nodes
 *
 * @see http://workingdraft.de/113/
 * @see http://www.backalleycoder.com/2013/03/18/cross-browser-event-based-element-resize-detection/
 *
 * Copyright (c) 2013 Yannick Albert (http://yckart.com)
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
 * 2013/04/01
 */

(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function($) {

    function addFlowListener(element, type, fn) {
        var flow = type == 'over';
        element.addEventListener('OverflowEvent' in window ? 'overflowchanged' : type + 'flow', function(e) {
            if (e.type == (type + 'flow') || ((e.orient == 0 && e.horizontalOverflow == flow) || (e.orient == 1 && e.verticalOverflow == flow) || (e.orient == 2 && e.horizontalOverflow == flow && e.verticalOverflow == flow))) {
                e.flow = type;
                return fn.call(this, e);
            }
        }, false);
    };

    function fireEvent(element, type, data, options) {
        var options = options || {},
        event = document.createEvent('Event');
        event.initEvent(type, 'bubbles' in options ? options.bubbles : true, 'cancelable' in options ? options.cancelable : true);
        for (var z in data) event[z] = data[z];
        element.dispatchEvent(event);
    };

    $.event.special.resize = {
        setup: function() {
            var element = this;
            var resize = 'onresize' in element;
            if (!resize && !element._resizeSensor) {
                var sensor = element._resizeSensor = document.createElement('div');
                sensor.className = 'resize-sensor';
                sensor.innerHTML = '<div class="resize-overflow"><div></div></div><div class="resize-underflow"><div></div></div>';

                var x = 0,
                    y = 0,
                    first = sensor.firstElementChild.firstChild,
                    last = sensor.lastElementChild.firstChild,
                    matchFlow = function(event) {
                        var change = false,
                            width = element.offsetWidth;
                        if (x != width) {
                            first.style.width = width - 1 + 'px';
                            last.style.width = width + 1 + 'px';
                            change = true;
                            x = width;
                        }
                        var height = element.offsetHeight;
                        if (y != height) {
                            first.style.height = height - 1 + 'px';
                            last.style.height = height + 1 + 'px';
                            change = true;
                            y = height;
                        }
                        if (change && event.currentTarget != element) fireEvent(element, 'resize');
                    };

                if (getComputedStyle(element).position == 'static') {
                    element.style.position = 'relative';
                    element._resizeSensor._resetPosition = true;
                }
                addFlowListener(sensor, 'over', matchFlow);
                addFlowListener(sensor, 'under', matchFlow);
                addFlowListener(sensor.firstElementChild, 'over', matchFlow);
                addFlowListener(sensor.lastElementChild, 'under', matchFlow);
                element.appendChild(sensor);
                matchFlow({});
            }
            var events = element._flowEvents || (element._flowEvents = []);
            if (events.indexOf(handler) == -1) events.push(handler);
            if (!resize) element.addEventListener('resize', handler, false);
            element.onresize = function(e) {
                events.forEach(function(fn) {
                    fn.call(element, e);
                });
            };
        },

        teardown: function() {
            var element = this;
            var index = element._flowEvents.indexOf(handler);
            if (index > -1) element._flowEvents.splice(index, 1);
            if (!element._flowEvents.length) {
                var sensor = element._resizeSensor;
                if (sensor) {
                    element.removeChild(sensor);
                    if (sensor._resetPosition) element.style.position = 'static';
                    delete element._resizeSensor;
                }
                if ('onresize' in element) element.onresize = null;
                delete element._flowEvents;
            }
            element.removeEventListener('resize', handler);
        }
    };

    $.fn.extend({
        resize: function(fn) {
            return fn ? this.bind("resize", fn) : this.trigger("resize");
        },

        unresize: function(fn) {
            return this.unbind("resize", fn);
        }
    });


    function handler(event) {
        var orgEvent = event || window.event,
            args = [].slice.call(arguments, 1);

        event = $.event.fix(orgEvent);
        event.type = "resize";

        // Add event to the front of the arguments
        args.unshift(event);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

}));

/*!
 * SlimScroll https://github.com/rochal/jQuery-slimScroll
 * =======================================================
 *
 * Copyright (c) 2011 Piotr Rochala (http://rocha.la) Dual licensed under the MIT
 */
(function(f) {
    jQuery.fn.extend({slimScroll: function(h) {
            var a = f.extend({width: "auto", height: "250px", size: "7px", color: "#000", position: "right", distance: "1px", start: "top", opacity: 0.4, alwaysVisible: !1, disableFadeOut: !1, railVisible: !1, railColor: "#333", railOpacity: 0.2, railDraggable: !0, railClass: "slimScrollRail", barClass: "slimScrollBar", wrapperClass: "slimScrollDiv", allowPageScroll: !1, wheelStep: 20, touchScrollStep: 200, borderRadius: "0px", railBorderRadius: "0px"}, h);
            this.each(function() {
                function r(d) {
                    if (s) {
                        d = d ||
                                window.event;
                        var c = 0;
                        d.wheelDelta && (c = -d.wheelDelta / 120);
                        d.detail && (c = d.detail / 3);
                        f(d.target || d.srcTarget || d.srcElement).closest("." + a.wrapperClass).is(b.parent()) && m(c, !0);
                        d.preventDefault && !k && d.preventDefault();
                        k || (d.returnValue = !1)
                    }
                }
                function m(d, f, h) {
                    k = !1;
                    var e = d, g = b.outerHeight() - c.outerHeight();
                    f && (e = parseInt(c.css("top")) + d * parseInt(a.wheelStep) / 100 * c.outerHeight(), e = Math.min(Math.max(e, 0), g), e = 0 < d ? Math.ceil(e) : Math.floor(e), c.css({top: e + "px"}));
                    l = parseInt(c.css("top")) / (b.outerHeight() - c.outerHeight());
                    e = l * (b[0].scrollHeight - b.outerHeight());
                    h && (e = d, d = e / b[0].scrollHeight * b.outerHeight(), d = Math.min(Math.max(d, 0), g), c.css({top: d + "px"}));
                    b.scrollTop(e);
                    b.trigger("slimscrolling", ~~e);
                    v();
                    p()
                }
                function C() {
                    window.addEventListener ? (this.addEventListener("DOMMouseScroll", r, !1), this.addEventListener("mousewheel", r, !1), this.addEventListener("MozMousePixelScroll", r, !1)) : document.attachEvent("onmousewheel", r)
                }
                function w() {
                    u = Math.max(b.outerHeight() / b[0].scrollHeight * b.outerHeight(), D);
                    c.css({height: u + "px"});
                    var a = u == b.outerHeight() ? "none" : "block";
                    c.css({display: a})
                }
                function v() {
                    w();
                    clearTimeout(A);
                    l == ~~l ? (k = a.allowPageScroll, B != l && b.trigger("slimscroll", 0 == ~~l ? "top" : "bottom")) : k = !1;
                    B = l;
                    u >= b.outerHeight() ? k = !0 : (c.stop(!0, !0).fadeIn("fast"), a.railVisible && g.stop(!0, !0).fadeIn("fast"))
                }
                function p() {
                    a.alwaysVisible || (A = setTimeout(function() {
                        a.disableFadeOut && s || (x || y) || (c.fadeOut("slow"), g.fadeOut("slow"))
                    }, 1E3))
                }
                var s, x, y, A, z, u, l, B, D = 30, k = !1, b = f(this);
                if (b.parent().hasClass(a.wrapperClass)) {
                    var n = b.scrollTop(),
                            c = b.parent().find("." + a.barClass), g = b.parent().find("." + a.railClass);
                    w();
                    if (f.isPlainObject(h)) {
                        if ("height"in h && "auto" == h.height) {
                            b.parent().css("height", "auto");
                            b.css("height", "auto");
                            var q = b.parent().parent().height();
                            b.parent().css("height", q);
                            b.css("height", q)
                        }
                        if ("scrollTo"in h)
                            n = parseInt(a.scrollTo);
                        else if ("scrollBy"in h)
                            n += parseInt(a.scrollBy);
                        else if ("destroy"in h) {
                            c.remove();
                            g.remove();
                            b.unwrap();
                            return
                        }
                        m(n, !1, !0)
                    }
                } else {
                    a.height = "auto" == a.height ? b.parent().height() : a.height;
                    n = f("<div></div>").addClass(a.wrapperClass).css({position: "relative",
                        overflow: "hidden", width: a.width, height: a.height});
                    b.css({overflow: "hidden", width: a.width, height: a.height});
                    var g = f("<div></div>").addClass(a.railClass).css({width: a.size, height: "100%", position: "absolute", top: 0, display: a.alwaysVisible && a.railVisible ? "block" : "none", "border-radius": a.railBorderRadius, background: a.railColor, opacity: a.railOpacity, zIndex: 90}), c = f("<div></div>").addClass(a.barClass).css({background: a.color, width: a.size, position: "absolute", top: 0, opacity: a.opacity, display: a.alwaysVisible ?
                                "block" : "none", "border-radius": a.borderRadius, BorderRadius: a.borderRadius, MozBorderRadius: a.borderRadius, WebkitBorderRadius: a.borderRadius, zIndex: 99}), q = "right" == a.position ? {right: a.distance} : {left: a.distance};
                    g.css(q);
                    c.css(q);
                    b.wrap(n);
                    b.parent().append(c);
                    b.parent().append(g);
                    a.railDraggable && c.bind("mousedown", function(a) {
                        var b = f(document);
                        y = !0;
                        t = parseFloat(c.css("top"));
                        pageY = a.pageY;
                        b.bind("mousemove.slimscroll", function(a) {
                            currTop = t + a.pageY - pageY;
                            c.css("top", currTop);
                            m(0, c.position().top, !1)
                        });
                        b.bind("mouseup.slimscroll", function(a) {
                            y = !1;
                            p();
                            b.unbind(".slimscroll")
                        });
                        return!1
                    }).bind("selectstart.slimscroll", function(a) {
                        a.stopPropagation();
                        a.preventDefault();
                        return!1
                    });
                    g.hover(function() {
                        v()
                    }, function() {
                        p()
                    });
                    c.hover(function() {
                        x = !0
                    }, function() {
                        x = !1
                    });
                    b.hover(function() {
                        s = !0;
                        v();
                        p()
                    }, function() {
                        s = !1;
                        p()
                    });
                    b.bind("touchstart", function(a, b) {
                        a.originalEvent.touches.length && (z = a.originalEvent.touches[0].pageY)
                    });
                    b.bind("touchmove", function(b) {
                        k || b.originalEvent.preventDefault();
                        b.originalEvent.touches.length &&
                                (m((z - b.originalEvent.touches[0].pageY) / a.touchScrollStep, !0), z = b.originalEvent.touches[0].pageY)
                    });
                    w();
                    "bottom" === a.start ? (c.css({top: b.outerHeight() - c.outerHeight()}), m(0, !0)) : "top" !== a.start && (m(f(a.start).position().top, null, !0), a.alwaysVisible || c.hide());
                    C()
                }
            });
            return this
        }});
    jQuery.fn.extend({slimscroll: jQuery.fn.slimScroll})
})(jQuery);

/* jQuery form remember state plugin
 Name: rememberState
 Version: 1.4.1
 Description: When called on a form element, localStorage is used to
 remember the values that have been input up to the point of either
 saving or unloading. (closing window, navigating away, etc.) If
 localStorage isn't available, nothing is bound or stored.
 The plugin looks for an element with a class of remember_state to show
 a note indicating there is stored data that can be repopulated by clicking
 on the anchor within the remember_state container. If the element doesn't
 exist, it is created and prepended to the form.
 Usage: $("form").rememberState("my_object_name");
 Notes: To trigger the deletion of a form's localStorage object from
 outside the plugin, trigger the reset_state event on the form element
 by using $("form").trigger("reset_state");
 To manually call restore state, call $("form").rememberState("restoreState");
*/
(function($) {
  if (!window.localStorage || !window.JSON) {
    return $.fn.rememberState = function() { return this; };
  }

  var remember_state = {
    name: "rememberState",
    clearOnSubmit: true,
    ignore: null,
    use_ids: false,
    mark: null,
    restoreState: function(e) {
      var data = JSON.parse(localStorage.getItem(this.mark)),
          $f = this.$el,
          $e;
      for (var i in data) {
        $e = $f.find("[name=\"" + data[i].name + "\"]");
        if ($e.is(":radio")) {
          $e.filter("[value=\"" + data[i].value + "\"]").prop("checked", true);
        }
        else if ($e.is(":checkbox") && data[i].value) {
          $e.prop("checked", true);
        }
        else if ($e.is("select")) {
          $e.find("[value=\"" + data[i].value + "\"]").prop("selected", true);
        }
        else {
          $e.val(data[i].value);
        }
        $e.change();
      }
      e && e.preventDefault && e.preventDefault();
    },
    saveState: function(e) {
      var instance = e.data.instance;
      var values = instance.$el.serializeArray();
      // jQuery doesn't currently support datetime-local inputs despite a
      // comment by dmethvin stating the contrary:
      // http://bugs.jquery.com/ticket/5667
      // Manually storing input type until jQuery is patched
      instance.$el.find("input[type='datetime-local']").each(function() {
        var $i = $(this);
        values.push({ name: $i.attr("name"), value: $i.val() });
      });
      values = instance.removeIgnored(values);
      values.length && internals.setObject(instance.mark, values);
    },
    save: function() {
      var instance = this;
      if (!this.saveState) {
        instance = this.data(remember_state.name);
      }
      instance.saveState({ data: { instance: instance } });
    },
    removeIgnored: function(values) {
      var ignore = this.ignore;
      if (!ignore) { return values; }
      $.each(values, function(i, input) {
        if ($.inArray(input.name, ignore) !== -1) {
          values[i] = false;
        }
      });
      values = $.grep(values, function(val) { return val; });
      return values;
    },
    setName: function() {
      this.mark = this.mark || this.$el.attr("id");
      if (!this.mark) { console.warn('No form ID or other mark.'); }
      this.mark = 'rememberstate_' + this.mark;
    },
    bindResetEvents: function() {
      var instance = this;
      if (this.clearOnSubmit) {
        this.$el.bind("submit." + this.name, function() {
          $(this).trigger("reset_state");
          $(window).unbind("unload." + instance.name);
        });
      }

      this.$el.bind("reset_state." + this.name, function() {
        localStorage.removeItem(instance.mark);
      });
      this.$el.find(":reset").bind("click." + this.name, function() {
        $(this).closest("form").trigger("reset_state");
      });
    },
    destroy: function(destroy_local_storage) {
      var namespace = "." + this.name;
      this.$el.unbind(namespace).find(":reset").unbind(namespace);
      $(window).unbind(namespace);
      destroy_local_storage && localStorage.removeItem(this.mark);
    },
    init: function() {
      this.setName();

      if (!this.mark) { return; }

      this.bindResetEvents();

      $(window).bind("unload." + this.name, { instance: this }, this.saveState);
    }
  };

  var internals = {
    setObject: function(key, value) { localStorage[key] = JSON.stringify(value); },
    getObject: function(key) { return JSON.parse(localStorage[key]); },
    createPlugin: function(plugin) {
      $.fn[plugin.name] = function(opts) {
        var $els = this,
            method = $.isPlainObject(opts) || !opts ? "" : opts,
            args = arguments;
        if (method && plugin[method]) {
          $els.each(function(i) {
            plugin[method].apply($els.eq(i).data(plugin.name), Array.prototype.slice.call(args, 1));
          });
        }
        else if (!method) {
          $els.each(function(i) {
            var plugin_instance = $.extend(true, {
              $el: $els.eq(i)
            }, plugin, opts);
            $els.eq(i).data(plugin.name, plugin_instance);
            plugin_instance.init();
          });
        }
        else {
          $.error('Method ' +  method + ' does not exist on jQuery.' + plugin.name);
        }
        return $els;
      };
    }
  };

  internals.createPlugin(remember_state);
})(jQuery);
