/**
 * Main JavaScript file
 *
 * @package         Tabs
 * @version         4.0.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	$(document).ready(function() {
		if (typeof( window['nn_tabs_use_hash'] ) != "undefined") {
			nnTabs.init();
		}
	});

	nnTabs = {
		init: function() {
			var $this = this;

			var nn_tabs_speed_fade = $('.nn_tabs .fade').length ? $('.nn_tabs .fade').first().css('transition-duration') : '0.15s';
			var nn_tabs_speed_collapse = $('.nn_tabs .collapse').length ? $('.nn_tabs .collapse').first().css('transition-duration') : '0.35s';

			// Set the transition durations to 1ms to make initial setting of active tabs as fast as possible
			this.insertCSSRule('.nn_tabs .fade', 'transition-duration: 1ms');
			this.insertCSSRule('.nn_tabs .collapse', 'transition-duration: 1ms');


			this.showByURL();

			this.showByHash();

			setTimeout((function() {
				$this.initActiveClasses();


				if (nn_tabs_use_hash) {
					$this.initHashHandling();
				}

				$this.initHashLinkList();

				$this.initIframeReloading();

				// Set the transition durations back to original
				$this.insertCSSRule('.nn_tabs .fade', 'transition-duration: ' + nn_tabs_speed_fade);
				$this.insertCSSRule('.nn_tabs .collapse', 'transition-duration: ' + nn_tabs_speed_collapse);
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


			$el.tab('show');

			$el.focus();
		},


		getElement: function(id) {
			return this.getTabElement(id);
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
			var id = decodeURIComponent(window.location.hash.replace('#', ''));

			if (id == '' || id.indexOf("&") != -1 || id.indexOf("=") != -1) {
				return;
			}

			// hash is a text anchor
			if ($('a.nn_tabs-toggle[data-id="' + id + '"]').length == 0) {
				this.showByHashAnchor(id);

				return;
			}

			// hash is a tab
			if (!nn_tabs_use_hash) {
				return;
			}

			this.showByID(id);
		},

		showByHashAnchor: function(id) {
			var $anchor = $('a[name="' + id + '"]');

			if ($anchor.length == 0) {
				return;
			}

			// Check if anchor has a parent tab
			if ($anchor.closest('.nn_tabs').length == 0) {
				return;
			}

			var $tab = $anchor.closest('.tab-pane').first();

			// Check if tab has sliders. If so, let Sliders handle it.
			if ($tab.find('.nn_sliders').length > 0) {
				return;
			}

			this.openParents($tab.attr('id'), 0);

			setTimeout(function() {
				$('html,body').animate({scrollTop: $anchor.offset().top});
			}, 250);
		},

		showByID: function(id) {
			var $el = $('a.nn_tabs-toggle[data-id="' + id + '"]');

			if ($el.length == 0) {
				return;
			}

			this.openParents(id, nn_tabs_urlscroll);
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
			$('li.nn_tabs-tab-sm').removeClass('active');
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

			// Check if anchor has a parent tab
			if ($anchor.closest('.nn_tabs').length == 0) {
				return;
			}

			var $tab = $anchor.closest('.tab-pane').first();

			// Check if tab has sliders. If so, let Sliders handle it.
			if ($tab.find('.nn_sliders').length > 0) {
				return;
			}

			var tab_id = $tab.attr('id');

			// Check if link is inside the same tab
			if ($link.closest('.nn_tabs').length > 0) {
				if ($link.closest('.tab-pane').first().attr('id') == tab_id) {
					return;
				}
			}

			$link.click(function(e) {
				// Open parent tab and parents
				$this.openParents(tab_id, $anchor);
				e.stopPropagation();
			});
		},

		initHashHandling: function(el) {
			$('a.nn_tabs-toggle').on('shown shown.bs.tab', function(e) {
				// prevent scrolling on setting hash, so temp empty the id of the element
				var id = $(this).attr('data-id');
				var $el = $('#' + id);
				$el.attr('id', '');
				window.location.hash = id;
				$el.attr('id', id);
				e.stopPropagation();
			});
		},

		initClickMode: function(el) {
			$('body').on('click.tab.data-api', 'a.nn_tabs-toggle', function(e) {
				e.preventDefault();
				nnTabs.show($(this).attr('data-id'), $(this).hasClass('nn_tabs-doscroll'));
				e.stopPropagation();
			});
		},


		initIframeReloading: function() {
			var $this = this;

			$('.tab-pane.active iframe').each(function() {
				$(this).attr('reloaded', true);
			});

			$('a.nn_tabs-toggle').on('show show.bs.tab', function(e) {
				// Re-inintialize Google Maps on tabs show
				if (typeof initialize == 'function') {
					initialize();
				}

				var $el = $('#' + $(this).attr('data-id'));

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

				$('.tab-pane iframe').each(function() {
					$(this).attr('reloaded', false);
				});

				$('.tab-pane.active iframe').each(function() {
					if (this.src) {
						this.src += '';
						$(this).attr('reloaded', true);
					}
				});
			});
		},

		getUrlVar: function() {
			var search = 'tab';
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
})
(jQuery);
