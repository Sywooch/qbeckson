(function ($) {
    "use strict";

    const SliderWithRange = function (element, options) {
        const self = this;
        self.slider = options.slider;
        self.$fieldFrom = options.fieldFrom;
        self.$fieldTo = options.fieldTo;

        if (!self.slider
            || !self.$fieldTo
            || !self.$fieldFrom) {
            console.log('обязательные параметры slider, fieldFrom, fieldTo');
            return;
        }
        self.init();
    };

    SliderWithRange.prototype = {
        constructor: SliderWithRange,
        init: function () {
            const self = this;
            self.setFields(self);
            self.slider.on('slideStop', function (event) {
                self.setFields(self);
            });
            self.$fieldFrom.keyup(function (event) {
                self.setSlider(self);
            });
            self.$fieldTo.keyup(function () {
                self.setSlider(self);
            });
        },

        setFields: function (self) {
            self.$fieldFrom.val(self.slider.getValue()[0]);
            self.$fieldTo.val(self.slider.getValue()[1]);
        },
        setSlider: function (self) {
            var from = Number(self.$fieldFrom.val());
            var to = Number(self.$fieldTo.val());
            if (isNaN(from) || isNaN(to)) {
                return;
            }
            self.slider.setValue([from, to]);
        }
    };

    $.fn.SliderWithRange = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var self = $(this), data = self.data('SliderWithRange'), options = typeof option === 'object' && option;
            if (!data) {
                data = new SliderWithRange(this, $.extend({}, $.fn.SliderWithRange.defaults, options, self.data()));
                self.data('SliderWithRange', data);
            }
            if (typeof option === 'string') {
                data[option].apply(data, args);
            }
        });
    };

    $.fn.SliderWithRange.defaults = {
        slider: null,
        fieldFrom: null,
        fieldTo: null
    };

    $.fn.SliderWithRange.Constructor = SliderWithRange;
}(window.jQuery));