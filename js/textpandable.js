/* 
 * Textpandable v0.9
 * Expanding Textareas Plugin for jQuery
 *
 * Copyright (c) 2009 Thom Stricklin
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
 
(function($) {
	$.extend({
		textpandable : new function() {
			this.defaults = {
				minRows : 1,
				maxRows : -1,
				padding : 1,
				width : 20,
				lineHeight: "1.4em",
				speed: 0,
				initSpeed: -1
			}
			
			this.init = function(opts) {
				return this.each(function() {
					if (!opts) opts = {};
					this.config = $.extend({}, $.textpandable.defaults, opts);
					this.config.textarea = this;
					if (!opts.lineHeight && $(this).css("lineHeight").match(/[\d.]+[a-zA-Z]+/)) {
						this.config.lineHeight = $(this).css("lineHeight");
					}
					if (!opts.width) {
						if ($(this).css("width").match(/[\d.]+ex/)) {
							this.config.width = parseFloat($(this).css("width"))*1.0;
						} else if ($(this).css("width").match(/[\d.]+em/)) {
							this.config.width = parseFloat($(this).css("width"))*1.5;
						} else if ($(this).attr("cols")) {
							this.config.width = $(this).attr("cols");
						} else if ($(this).css("width").match(/[\d.]+px/)) {
							this.config.width = parseInt($(this).css("width"))/14.0;
						}
						this.config.width = parseInt(this.config.width);
					}
					if (this.config.initSpeed = -1) {
						this.config.initSpeed = this.config.speed;
					}
					$(this).css("lineHeight",this.config.lineHeight).
						css("marginTop","0px");
					this.ranInit = false;
					var self = this;
					$(this).bind("keyup",function() { updateRows(self); return true; });
					updateRows(this);
				});
			}
			
			function updateRows(ob) {
				var el = $(ob);
				var rows = el.val().split("\n");
				var height=0;
				for (var r in rows) {
					height += Math.ceil((rows[r].length+1)/ob.config.width);
				}
				height += ob.config.padding;
				if (height<ob.config.minRows) {
					height = ob.config.minRows;
				}
				if (ob.config.maxRows>-1 && height >= ob.config.maxRows) {
					el.css('overflow','auto');
					height = ob.config.maxRows;
				} else {
					el.css('overflow','hidden');
				}
				var multiplier = ob.config.lineHeight.replace(/[^\d.]+/,'');
				var units = ob.config.lineHeight.replace(/[\d.]+/,'');
				height=Math.round(height*parseFloat(multiplier))+units;
			
				var cHeight = el.get(0).style.height;
				var cNumber = cHeight.replace(/[^\d.]+/,'');
				var cUnits = cHeight.replace(/[\d.]+/,'');
				cHeight = Math.round(parseFloat(cNumber))+cUnits;
				if (cHeight=='' || cHeight!=height) {
					if (ob.ranInit) {
						if (ob.config.speed>0) {
							el.animate({height: height},ob.config.speed);
						} else {
							el.css('height',height);
						}
					} else {
						if (ob.config.initSpeed>0) {
							el.animate({height: height},ob.config.initSpeed);
						} else {
							el.css('height',height);
						}
					}
				}
				ob.ranInit = true;
			}
		}
	});
	
	$.fn.extend({ textpandable : $.textpandable.init });
			
})(jQuery);
