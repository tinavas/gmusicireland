/**
 * Main JavaScript file
 *
 * @package         NoNumber Extension Manager
 * @version         4.6.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	if (typeof( window['nnManager'] ) == "undefined") {
		nnManager = {
			refreshData: function(external) {
				nnManager.hide('loaded, data');
				nnManager.show('progress');

				$('div#nnem').find('tr').removeClass();
				$('div#nnem').find('td.ext_new').removeClass('disabled');

				var url = 'index.php?option=com_nonumbermanager&task=update';
				nnScripts.loadajax(url, 'nnManager.setData( data, ' + external + ' )', 'nnManager.setData( 0 )', NNEM_TOKEN + '=1', '', 'xml');
			},

			setData: function(data, external) {
				var xml = nnScripts.getObjectFromXML(data);

				if (!xml) {
					return;
				}

				for (var i = 0; i < NNEM_IDS.length; i++) {
					var extension = NNEM_IDS[i];
					var dat = 0;
					if (typeof(xml[extension]) !== 'undefined') {
						dat = xml[extension];
					}

					tr = $('tr#row_' + extension);

					// Versions
					if (tr) {
						nnManager.show('pro_not_installed', tr);
						if (dat && typeof(dat['version']) !== 'undefined' && dat['version']) {
							tr.find('span.current_version').text(dat['version']);
							if (dat['pro'] == 1) {
								nnManager.hide('pro_not_installed', tr)
								nnManager.show('pro_installed', tr);
							} else if (dat['old'] == 1) {
								nnManager.show('old_installed', tr);
							} else {
								nnManager.show('free_installed', tr);
							}
							if (dat['missing']) {
								tr.find('.missing span').attr('data-content', dat['missing']);
								nnManager.show('has_missing', tr);
							}
							nnManager.show('installed', tr);
						} else {
							tr.find('span.current_version').text('');
							nnManager.show('not_installed', tr);
						}
					}
				}
				if (external) {
					nnManager.hide('progress', $('.ext_types'));
					nnManager.show('loaded, no_external', $('.ext_types'));
					nnManager.refreshExternalData();
				} else {
					nnManager.hide('progress');
					nnManager.show('loaded, no_external');
				}
			},

			refreshExternalData: function() {
				var url = 'download.nonumber.nl/extensions.php?j=3';
				if (NNEM_KEY) {
					url += '&k=' + NNEM_KEY;
				}
				nnScripts.loadajax(url, 'nnManager.setExternalData(data)', 'nnManager.setExternalData(0)', '', NNEM_TIMEOUT, 'xml');
			},

			setExternalData: function(data) {
				var xml = nnScripts.getObjectFromXML(data);
				if (!xml) {
					alert(NNEM_FAIL);

					nnManager.hide('progress');
					nnManager.show('loaded, no_external');
					return;
				}

				div = $('div#nnem');
				toolbar = $('div#toolbar');

				// reset stuff
				toolbar.removeClass('has_install').removeClass('has_update');

				for (var i = 0; i < NNEM_IDS.length; i++) {
					var extension = NNEM_IDS[i];
					var dat = 0;

					if (typeof(xml[extension]) !== 'undefined') {
						dat = xml[extension];
					}

					tr = div.find('tr#row_' + extension);

					// reset stuff
					tr.find('td.ext_new').removeClass('disabled');
					tr.find('.changelog, .changelog > span').removeClass('disabled');

					if (!dat) {
						nnManager.show('uptodate', tr);
					} else {
						// Changelog
						if (typeof(dat['changelog']) !== 'undefined' && dat['changelog'] != '') {
							changelog = dat['changelog'].replace('font-size:1.2em;', '');
							tr.find('.changelog a').attr('data-content', changelog);
							nnManager.show('changelog', tr);
						}

						// Install buttons
						if (typeof(dat['version']) !== 'undefined' && dat['version'] != '') {
							v_new = String(dat['version']).trim();

							if (!v_new || v_new == '0') {
								// no new version fond: show refresh button
								nnManager.show('no_external', tr);
							} else {
								v_old = String(tr.find('.current_version').first().text()).trim();
								is_old = ( tr.id == 'row_nonumberextensionmanager' ) ? 0 : tr.hasClass('old_installed');

								pro_installed = tr.hasClass('pro_installed');

								pro_access = (dat['pro'] == 1);
								if (pro_access) {
									nnManager.show('pro_access', tr);
									$('#url_' + extension).val(dat['downloadurl_pro']);
								} else {
									nnManager.show('pro_no_access', tr);
									$('#url_' + extension).val(dat['downloadurl']);
								}

								pro_available = (dat['has_pro'] == 1);
								if (pro_available) {
									nnManager.show('pro_available', tr);
								} else {
									nnManager.show('pro_not_available', tr);
								}

								tr.find('.new_version').text(v_new);
								nnManager.show('changelog', tr);

								if (!v_old || v_old == '0') {
									toolbar.addClass('has_install');
									nnManager.show('selectable', tr);
									nnManager.show('install', tr);
								} else if (is_old && pro_available && !pro_access) {
									nnManager.show('old', tr);
								} else if (tr.hasClass('has_missing')) {
									toolbar.addClass('has_install');
									nnManager.show('selectable', tr);
									nnManager.show('install', tr);
								} else if (pro_available && pro_installed && !pro_access) {
									nnManager.show('pro_no_access', tr);
								} else {
									compare = nnScripts.compareVersions(v_old, v_new);
									if (compare == '<' || (!pro_installed && pro_access)) {
										toolbar.addClass('has_update');
										nnManager.show('selectable', tr);
										nnManager.show('update', tr);
									} else if (compare == '>') {
										nnManager.show('downgrade', tr);
										tr.find('td.ext_new').addClass('disabled');
									} else {
										tr.find('.changelog, .changelog > span').addClass('disabled');
										nnManager.show('uptodate', tr);
										nnManager.show('reinstall', tr);
									}
								}
							}
						}
					}
				}

				nnManager.hide('progress');
				nnManager.show('loaded');

				nnManager.updateCheckboxes();

				// unlock height of table rows
				//div.find('table td.checkbox').css('height', '');

				// unlock width of table columns
				//div.find('table th').css('min-width', '');
			},

			updateCheckboxes: function() {
				// hide select boxes
				nnManager.hide('select');

				// reset hidden checkboxes
				div.find('table tr.not_installed').each(function(i, tr) {
					if (tr.hasClass('xselectable')) {
						tr.addClass('selectable').removeClass('xselectable');
					}
				});

				// make hidden rows unselectable
				div.find('table.hide_not_installed tr.not_installed').each(function(i, tr) {
					if (tr.hasClass('selectable')) {
						tr.addClass('xselectable').removeClass('selectable');
					}
				});

				// show select boxes of selectable rows
				nnManager.show('selectable .select');
			},

			install: function(task, id) {
				var url = $('#url_' + id).val();
				nnManager.openModal(task, [id], [url]);
			},

			installMultiple: function(task) {
				var ids = [];
				var urls = [];

				switch (task) {
					case 'updateall':
						type = 'update';
						msg = NNEM_NOUPDATE;
						break;
					default:
						type = 'install';
						msg = NNEM_NONESELECTED;
						break;
				}

				$('div#nnem tr.selectable').each(function() {
					tr = $(this);
					var el = tr.find('td.ext_checkbox input');
					id = el.val();
					if (id) {
						var pass = 0;
						switch (task) {
							case 'updateall':
								pass = tr.hasClass('update');
								break;
							default:
								pass = el.is(':checked');
								break;
						}

						if (pass) {
							url = $('#url_' + id).val();
							ids.push(id);
							urls.push(url);
						}
					}
				});

				if (!ids.length) {
					alert(msg);
				} else {
					nnManager.openModal(type, ids, urls);
				}
			},
			openModal: function(task, ids, urls) {
				a = [];
				for (var i = 0; i < ids.length; i++) {
					a.push('ids[]=' + escape(ids[i]))
				}

				width = 480;
				height = 58 + (a.length * 37);
				min = 140;
				max = window.getSize().y - 60;
				if (height > max) {
					height = max;
					width += 16;
				}
				if (height < min) {
					height = min;
				}

				a = a.join('&');

				b = [];
				for (var j = 0; j < urls.length; j++) {
					url = urls[j].replace('http://', '');
					b.push('urls[]=' + escape(url));
				}
				b = b.join('&');

				url = 'index.php?option=com_nonumbermanager&view=process&tmpl=component&task=' + task + '&' + a + '&' + b;
				SqueezeBox.open(url, {handler: 'iframe', size: {x: width, y: height}, classWindow: 'nnem_modal'});
			},

			show: function(classes, parent) {
				this.toggle(classes, parent, 1);
			},

			hide: function(classes, parent) {
				this.toggle(classes, parent, 0);
			},

			toggle: function(classes, parent, show) {
				classes = classes.split(',');
				$(classes).each(function(i, el) {
					c = classes[i].trim();
					if (!parent) {
						parent = $('div#nnem');
					} else {
						if (c != 'progress' && c != 'loaded') {
							if (show) {
								parent.addClass(c);
							} else {
								parent.removeClass(c);
							}
						}
					}
					if (show) {
						parent.find('.' + c).removeClass('hide');
					} else {
						parent.find('.' + c).addClass('hide');
					}
				});
			}
		}
	}
})(jQuery);

function nnem_function(task, id) {
	if (!task) {
		return;
	}
	switch (task) {
		case 'refresh':
			nnManager.refreshData(1);
			break;
		case 'updateall':
		case 'installselected':
			nnManager.installMultiple(task);
			break;
		case 'install':
		case 'update':
		case 'reinstall':
		case 'downgrade':
		case 'uninstall':
			nnManager.install(task, id);
			break;
	}
}
