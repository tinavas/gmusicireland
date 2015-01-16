/**
 * Main JavaScript file
 *
 * @package         Sliders
 * @version         4.0.5
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	$(document).ready(function() {
		if (typeof( window['nn_sliders_use_hash'] ) != "undefined") {
			nnSliders.init();
		}
	});

	nnSliders = {
		init: function() {
			var $this = this;

			this.hash_id = decodeURIComponent(window.location.hash.replace('#', ''));
			var nn_sliders_speed_fade = $('.nn_sliders .fade').length ? $('.nn_sliders .fade').first().css('transition-duration') : '0.15s';
			var nn_sliders_speed_collapse = $('.nn_sliders .collapse').length ? $('.nn_sliders .collapse').first().css('transition-duration') : '0.35s';

			// Set the transition durations to 1ms to make initial setting of active tabs as fast as possible
			this.insertCSSRule('.nn_sliders .fade', 'transition-duration: 1ms');
			this.insertCSSRule('.nn_sliders .collapse', 'transition-duration: 1ms');

			var timeout = $('.nn_tabs').length ? 250 : 0;
			setTimeout((function() {
				$this.initActiveClasses();


				$this.showByURL();

				$this.showByHash();
			}), timeout);

			setTimeout((function() {
				$this.initClickMode();


				if (nn_sliders_use_hash) {
					$this.initHashHandling();
				}

				$this.initHashLinkList();

				$this.initIframeReloading();

				// Set the transition durations back to original
				$this.insertCSSRule('.nn_sliders .fade', 'transition-duration: ' + nn_sliders_speed_fade);
				$this.insertCSSRule('.nn_sliders .collapse', 'transition-duration: ' + nn_sliders_speed_collapse);
			}), 1000);

		},

		show  : function(id, scroll, openparents) {
			if (openparents) {
				this.openParents(id, scroll);
				return;
			}

			var $this = this;
			var $el = this.getElement(id);

			if (!$el.length) {
				return;
			}

			if (!$el.hasClass('in')) {

				$el.collapse({
					toggle: true,
					parent: $el.parent().parent()
				});
				$el.collapse('show');
			} else {
			}

			$el.focus();
		},


		getElement: function(id) {
			return this.getSliderElement(id);
		},

		getTabElement: function(id) {
			return $('a.nn_tabs-toggle[data-id="' + id + '"]');
		},

		getSliderElement: function(id) {
			return $('#' + id + '.nn_sliders-body');
		},

		insertCSSRule: function(selector, rules) {
			if (typeof document.styleSheets != 'object') {
				return;
			}

			if (!document.styleSheets.length) {
				document.getElementsByTagName('head')[0].appendChild(document.createElement('style'));
			}

			var sheet = document.styleSheets[document.styleSheets.length - 1];

			// Internet Explorer, Firefox
			if ('cssText' in sheet) {
				sheet.cssText = selector + '{' + rules + '}';
				return;
			}
			// Opera, Google Chrome and Safari
			if ('insertRule' in sheet) {
				try {
					var index = sheet.cssRules ? sheet.cssRules.length : 0;
				} catch (e) {
					return;
				}

				sheet.insertRule(selector + '{' + rules + '}', index);
				return;
			}

			// Non-standard (should not be necessary)
			if ('addRule' in sheet) {
				sheet.addRule(selector, rules, index);
				return;
			}
		},


		showByURL: function() {
			var id = this.getUrlVar();

			if (id == '') {
				return;
			}

			this.showByID(id);
		},

		showByHash: function() {
			if (!this.hash_id) {
				return;
			}

			var id = this.hash_id;
			if (id == '' || id.indexOf("&") != -1 || id.indexOf("=") != -1) {
				return;
			}

			// hash is a text anchor
			if ($('a[name="nn_sliders-scrollto_' + id + '"]').length == 0) {
				this.showByHashAnchor(id);

				return;
			}

			// hash is a slider
			if (!nn_sliders_use_hash) {
				return;
			}

			this.showByID(id);
		},

		showByHashAnchor: function(id) {
			var $anchor = $('a[name="' + id + '"]');

			if ($anchor.length == 0) {
				return;
			}

			// Check if anchor has a parent slider
			if ($anchor.closest('.nn_sliders').length == 0) {
				return;
			}

			var $slider = $anchor.closest('.nn_sliders-body').first();

			// Check if slider has tabs. If so, let Tabs handle it.
			if ($slider.find('.nn_tabs').length > 0) {
				return;
			}

			this.openParents($slider.attr('id'), 0);

			setTimeout(function() {
				$('html,body').animate({scrollTop: $anchor.offset().top});
			}, 250);
		},

		showByID: function(id) {
			var $el = $('a[name="nn_sliders-scrollto_' + id + '"]');

			if ($el.length == 0) {
				return;
			}

			this.openParents(id, nn_sliders_urlscroll);
		},

		openParents: function(id, scroll) {
			var $el = this.getElement(id);

			if (!$el.length) {
				return;
			}

			var parents = new Array;

			var parent = this.getElementArray($el);
			while (parent) {
				parents[parents.length] = parent;
				parent = this.getParent(parent.el);
			}

			if (!parents.length) {
				return false;
			}

			this.stepThroughParents(parents, null, scroll);
		},

		stepThroughParents: function(parents, parent, scroll) {
			$this = this;

			if (!parents.length && parent) {

				parent.el.focus();
				return;
			}

			parent = parents.pop();

			if (parent.el.hasClass('in') || parent.el.parent().hasClass('active')) {
				$this.stepThroughParents(parents, parent, scroll);
				return;
			}

			switch (parent.type) {
				case 'tab':
					if (typeof( window['nnTabs'] ) == "undefined") {
						$this.stepThroughParents(parents, parent, scroll);
						break;
					}

					parent.el.one('shown shown.bs.tab', function(e) {
						$this.stepThroughParents(parents, parent, scroll);
					});

					nnTabs.show(parent.id);
					break;

				case 'slider':
					if (typeof( window['nnSliders'] ) == "undefined") {
						$this.stepThroughParents(parents, parent, scroll);
						break;
					}

					parent.el.one('shown shown.bs.collapse', function(e) {
						$this.stepThroughParents(parents, parent, scroll);
					});

					nnSliders.show(parent.id);
					break;
			}
		},

		getParent: function($el) {
			if (!$el) {
				return false;
			}

			var $parent = $el.parent().closest('.nn_tabs-pane, .nn_sliders-body');

			if (!$parent.length) {
				return false;
			}

			var parent = this.getElementArray($parent);

			return parent;
		},

		getElementArray: function($el) {
			var id = $el.attr('data-toggle') ? $el.attr('data-id') : $el.attr('id');
			var type = ($el.hasClass('nn_tabs-pane') || $el.hasClass('nn_tabs-toggle')) ? 'tab' : 'slider'

			return {
				'type': type,
				'id'  : id,
				'el'  : type == 'tab' ? this.getTabElement(id) : this.getSliderElement(id)
			};
		},

		initActiveClasses: function() {
			$('.nn_sliders-body').on('show show.bs.collapse', function(e) {
				$(this).parent().addClass('active');
				e.stopPropagation();
			});
			$('.nn_sliders-body').on('hidden hidden.bs.collapse', function(e) {
				$(this).parent().removeClass('active');
				e.stopPropagation();
			});
		},

		initHashLinkList: function() {
			var $this = this;

			$('a[href^="#"]').each(function($i, el) {
				$this.initHashLink(el);
			});
		},

		initHashLink: function(el) {
			var $this = this;
			var $link = $(el);

			// link is a tab or slider, so ignore
			if ($link.attr('data-toggle')) {
				return;
			}

			var id = $link.attr('href').replace('#', '');
			var $anchor = $('a[name="' + id + '"]');

			// No accompanying link found
			if ($anchor.length == 0) {
				return;
			}

			// Check if anchor has a parent slider
			if ($anchor.closest('.nn_sliders').length == 0) {
				return;
			}

			var $slider = $anchor.closest('.nn_sliders-body').first();

			// Check if tab has sliders. If so, let Sliders handle it.
			if ($slider.find('.nn_tabs').length > 0) {
				return;
			}

			var slider_id = $slider.attr('id');

			// Check if link is inside the same slider
			if ($link.closest('.nn_sliders').length > 0) {
				if ($link.closest('.nn_sliders-body').first().attr('id') == slider_id) {
					return;
				}
			}

			$link.click(function(e) {
				// Open parent slider and parents
				$this.openParents(slider_id, $anchor);
				e.stopPropagation();
			});
		},

		initHashHandling: function(el) {
			$('.nn_sliders-body').on('shown shown.bs.collapse', function(e) {
				// prevent scrolling on setting hash, so temp empty the id of the element
				var id = this.id
				this.id = '';
				window.location.hash = id;
				this.id = id;
				e.stopPropagation();
			});
		},

		initClickMode: function(el) {
			var $this = this;
			$('body').on('click.collapse.data-api', 'a.nn_sliders-toggle', function(e) {
				e.preventDefault();

				var id = $(this).attr('data-id');
				var $el = $this.getElement(id);

				if (!$el.hasClass('in')) {
					nnSliders.show(id, $(this).hasClass('nn_sliders-item-scroll'));
				} else {
					$el.collapse('hide');
				}

				e.stopPropagation();
			});
		},


		initIframeReloading: function() {
			var $this = this;

			$('.nn_sliders-body.in iframe').each(function() {
				$(this).attr('reloaded', true);
			});

			$('.nn_sliders-body').on('show show.bs.collapse', function(e) {
				// Re-inintialize Google Maps on tabs show
				if (typeof initialize == 'function') {
					initialize();
				}

				var $el = $(this);

				$el.find('iframe').each(function() {
					if (this.src && !$(this).attr('reloaded')) {
						this.src += '';
						$(this).attr('reloaded', true);
					}
				});
			});

			$(window).resize(function() {
				if (typeof initialize == 'function') {
					initialize();
				}

				$('.nn_sliders-body iframe').each(function() {
					$(this).attr('reloaded', false);
				});

				$('.nn_sliders-body.in iframe').each(function() {
					if (this.src) {
						this.src += '';
						$(this).attr('reloaded', true);
					}
				});
			});
		},

		getUrlVar: function() {
			var search = 'slider';
			var query = window.location.search.substring(1);

			if (query.indexOf(search + '=') == -1) {
				return '';
			}

			var vars = query.split('&');
			for (var i = 0; i < vars.length; i++) {
				var keyval = vars[i].split('=');

				if (keyval[0] != search) {
					continue;
				}

				return keyval[1];
			}

			return '';
		}
	};
})(jQuery);

/* For custom use */
function openAllSliders() {
	jQuery('.nn_sliders-body:not(.in)').collapse('show');
}

function closeAllSliders() {
	jQuery('.nn_sliders-body.in').collapse('hide');
}
