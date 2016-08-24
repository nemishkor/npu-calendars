/*! UIkit 2.24.3 | http://www.getuikit.com | (c) 2014 YOOtheme | MIT License */
(function(addon) {
    var component;

    if (window.UIkit) {
        component = addon(UIkit);
    }

    if (typeof define == "function" && define.amd) {
        define("uikit-tooltip", ["uikit"], function(){
            return component || addon(UIkit);
        });
    }

})(function(UI){

    "use strict";

    var $tooltip,   // tooltip container
        tooltipdelay, checkdelay;

    UI.component('tooltip', {

        defaults: {
            "offset": 5,
            "pos": "top",
            "animation": false,
            "delay": 0, // in miliseconds
            "cls": "",
            "activeClass": "uk-active",
            "src": function(ele) {
                var title = ele.attr('title');

                if (title !== undefined) {
                    ele.data('cached-title', title).removeAttr('title');
                }

                return ele.data("cached-title");
            }
        },

        tip: "",

        boot: function() {

            // init code
            UI.$html.on("mouseenter.tooltip.uikit focus.tooltip.uikit", "[data-uk-tooltip]", function(e) {
                var ele = UI.$(this);

                if (!ele.data("tooltip")) {
                    UI.tooltip(ele, UI.Utils.options(ele.attr("data-uk-tooltip")));
                    ele.trigger("mouseenter");
                }
            });
        },

        init: function() {

            var $this = this;

            if (!$tooltip) {
                $tooltip = UI.$('<div class="uk-tooltip"></div>').appendTo("body");
            }

            this.on({
                focus      : function(e) { $this.show(); },
                blur       : function(e) { $this.hide(); },
                mouseenter : function(e) { $this.show(); },
                mouseleave : function(e) { $this.hide(); }
            });
        },

        show: function() {

            this.tip = typeof(this.options.src) === "function" ? this.options.src(this.element) : this.options.src;

            if (tooltipdelay)     clearTimeout(tooltipdelay);
            if (checkdelay)       clearTimeout(checkdelay);

            if (typeof(this.tip) === 'string' ? !this.tip.length:true) return;

            $tooltip.stop().css({"top": -2000, "visibility": "hidden"}).removeClass(this.options.activeClass).show();
            $tooltip.html('<div class="uk-tooltip-inner">' + this.tip + '</div>');

            var $this      = this,
                pos        = UI.$.extend({}, this.element.offset(), {width: this.element[0].offsetWidth, height: this.element[0].offsetHeight}),
                width      = $tooltip[0].offsetWidth,
                height     = $tooltip[0].offsetHeight,
                offset     = typeof(this.options.offset) === "function" ? this.options.offset.call(this.element) : this.options.offset,
                position   = typeof(this.options.pos) === "function" ? this.options.pos.call(this.element) : this.options.pos,
                tmppos     = position.split("-"),
                tcss       = {
                    "display"    : "none",
                    "visibility" : "visible",
                    "top"        : (pos.top + pos.height + height),
                    "left"       : pos.left
                };


            // prevent strange position
            // when tooltip is in offcanvas etc.
            if (UI.$html.css('position')=='fixed' || UI.$body.css('position')=='fixed'){
                var bodyoffset = UI.$('body').offset(),
                    htmloffset = UI.$('html').offset(),
                    docoffset  = {'top': (htmloffset.top + bodyoffset.top), 'left': (htmloffset.left + bodyoffset.left)};

                pos.left -= docoffset.left;
                pos.top  -= docoffset.top;
            }


            if ((tmppos[0] == "left" || tmppos[0] == "right") && UI.langdirection == 'right') {
                tmppos[0] = tmppos[0] == "left" ? "right" : "left";
            }

            var variants =  {
                "bottom"  : {top: pos.top + pos.height + offset, left: pos.left + pos.width / 2 - width / 2},
                "top"     : {top: pos.top - height - offset, left: pos.left + pos.width / 2 - width / 2},
                "left"    : {top: pos.top + pos.height / 2 - height / 2, left: pos