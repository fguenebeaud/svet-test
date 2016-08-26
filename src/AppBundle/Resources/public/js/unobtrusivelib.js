/*
 * Unobtrusivelib 1.1
 *
 * Copyright (c) 2008 Pierre Bertet (pierrebertet.net)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 */

(function($){
	$.unobtrusivelib = function (enabled_modules) {
		var modules = {
			autoClearInput: function () {
				var defaultClass = 'autoclear';
				$('input.autoclear:text, input.autoclear:password, textarea.autoclear').each(function(){
					var $this = jQuery(this);
					if ($this.is(":password")) {
						// edit
						var $original = $this, attributes = {};
						if($this.attr('style'))
							attributes['style'] = $this.attr('style');
						if($this.attr('class'))
							attributes['class'] = $this.attr('class');
						if($this.attr('id'))
							attributes['id'] = $this.attr('id');
						if($this.attr('value'))
							attributes['value'] = $this.attr('value');
						if($this.attr('size'))
							attributes['size'] = $this.attr('size');
						if($this.attr('maxlength'))
							attributes['maxlength'] = $this.attr('maxlength');
						
						$this = $('<input type="text" />').attr(attributes);
						// end edit
						
						$original.after($this).hide();
						
						$this.focus(function(){
							$this.hide();
							$original.show().focus();
						});

						if ( $this.val() == this.defaultValue ) {
							$this.addClass(defaultClass);
						}
						$original.focus(function () {
							if ( this.defaultValue == $original.val() ) {
								$original.removeClass(defaultClass).val("");
							}
						}).blur(function () {
							if ( $original.val() == "" ) {
								$original.hide();
								$this.show().addClass(defaultClass).val( this.defaultValue );
							}
						});
					} else {
						if ( $this.val() == this.defaultValue ) {
							$this.addClass(defaultClass);
						}
						$this.focus(function () {
							if ( this.defaultValue == $this.val() ) {
								$this.removeClass(defaultClass).val("");
							}
						}).blur(function () {
							if ( $this.val() == "" ) {
								$this.addClass(defaultClass).val( this.defaultValue );
							}
						});
					}
				});
			},
			
			autoFocusInput: function () {
				var focusElmts = $("input.autofocus");
				if (focusElmts.length != 0){
					focusElmts.get(0).focus();
				}
			}
		};
		if (!!enabled_modules) {
			$.each(enabled_modules,function(i,n){
				if(modules[n]){
					modules[n]();
				}
			});
		}
		else {
			$.each(modules,function(i,n){
				n();
			});
		}
	};
})(jQuery);