(function($) {

$.widget("ui.LodgixTextExpander", {

	options: {
		heightPx: 114,
		more: '+',
		less: '-'
	},

	_create: function() {
        this._element = this.element.clone();
		var text = this.element;
        this._text = text;
		text.css('font-size', text.css('font-size'));
		text.css('line-height', 1);
		var wrapper = $('<div class="lodgixTextExpanderWrapper" style="position:relative"></div>');
		this.element.before(wrapper);
        this.element = wrapper;
		wrapper.append(text);
		var lineHeight = parseFloat(text.css('line-height'));
		if (text[0].scrollHeight - this.options.heightPx > lineHeight) {
			this._height = Math.ceil(Math.floor(this.options.heightPx / lineHeight) * lineHeight);
			wrapper.append($('<div class="lodgixTextExpanderShadow" style="position:absolute;bottom:0;right:0"></div>'));
			this._control = $('<div class="lodgixTextExpander" style="position:absolute;bottom:0;right:0"></div>');
			wrapper.append(this._control);
		}
	},

	destroy: function() {
		$.Widget.prototype.destroy.call(this);
        this._text = null;
        this._control = null;
        this.element.before(this._element);
        this.element.detach();
        this.element = this._element;
        this._element = null;
	},

	_init: function() {
		if (this._control) {
			this._collapse();
		}
	},

	_collapse: function() {
		var w = this;
		w._text.css('height', w._height + 'px');
		w._text.css('overflow', 'hidden');
		w._control.html(w.options.more);
		w._control.click(function() {
			w._expand();
		});
	},

	_expand: function() {
		var w = this;
		w._text.css('height', 'auto');
		w._text.css('overflow', 'hidden');
		w._control.html(w.options.less);
		w._control.click(function() {
			w._collapse();
		});
	}

});

})(jQueryLodgix);
