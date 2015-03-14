/**
 * @package Extly.Components
 * @subpackage com_autotweet - AutoTweet posts content to social channels
 *             (Twitter, Facebook, LinkedIn, etc).
 *
 * @author Prieco S.A.
 * @copyright Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link http://www.extly.com http://support.extly.com
 */

/* jslint plusplus: true, browser: true, sloppy: true */
/* global jQuery, Request, Joomla, alert, Backbone */

define('itemeditor.helper', [],
		function() {
	"use strict";

	var templateRender = null;

	var ItemEditorHelper = {

		POSTTHIS_DEFAULT: 1,
		POSTTHIS_NO: 2,
		POSTTHIS_YES: 3,

		setHtmlAdvancedAttrs: function(message) {
			var form = 'form[name=adminForm]', $form, element;

			element = window.parent.document.getElementById('autotweet_advanced_attrs');

			if (element)
			{
				message = JSON.stringify(message);
				element.value = message;

				return true;
			}

			message = _.escape(JSON.stringify(message));

			element = '<input type="hidden" id="autotweet_advanced_attrs" name="autotweet_advanced_attrs" value="'
					+ message
					+ '">';

			$form = window.parent.jQuery(form);

			// Form found
			if ($form.size() == 1) {
				$form.append(element);
			} else {

				// Zoo form?
				$form = window.parent.jQuery('form[name=submissionForm]');

				if ($form.size() == 1) {
					$form.append(element);
				}
			}

			return true;
		},

		getHtmlAdvancedAttrs: function() {
			var input, message;

			input = window.parent.document.getElementById('autotweet_advanced_attrs');
			if ((input) && (!_.isEmpty(input.value)))
			{
				message = input.value;
				message = JSON.parse(_.unescape(message));

				return message;
			}

			return null;
		},

		getHtmlEditorText: function() {
			var editor, text;

			if ((window.parent.tinymce) && (window.parent.tinymce.activeEditor)) {
				text = window.parent.tinymce.activeEditor.getContent();

				// No text, but in Zoo there's a change to load it via editors
				if (_.isEmpty(text)) {
					text = window.parent.tinymce.editors[0].getContent();
				}

				return text;
			} else if (editor = window.parent.document.getElementById('jform_articletext')) {

				return editor.value;
			}
		},

		setHtmlPanel: function(panel, title) {
			var element;

			// com_content - J3 - admin
			element = window.parent.jQuery('#item-form #myTabContent');
			if (element.size())
			{
				ItemEditorHelper.addHtmlTab(title);
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_content - J3 - front
			element = window.parent.jQuery('.item-page #adminForm .tab-content');
			if (element.size())
			{
				ItemEditorHelper.addHtmlTab(title);
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_content - J25 - admin
			element = window.parent.jQuery('#item-form .pane-sliders');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_content - J25 - front
			element = window.parent.jQuery('.item-page #adminForm');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_autotweet requests qTypeTabs
			element = window.parent.jQuery('.request-edit #qContent');
			if (element.size())
			{
				ItemEditorHelper.addHtmlTab(title);
				ItemEditorHelper.addHtmlPanel(element, panel);
				window.parent.jQuery('#filterconditions-tab a').tab('show');
				return true;
			}

			// com_k2
			element = window.parent.jQuery('#adminFormK2Sidebar');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_zoo - admin
			element = window.parent.jQuery('#parameter-accordion');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_zoo - front
			element = window.parent.jQuery('#item-submission');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_easyblog - admin - J3
			element = window.parent.jQuery('#eblog-wrapper #options');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_easyblog - admin - J25
			element = window.parent.jQuery('#eblog-wrapper #content-pane');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_easyblog - front
			element = window.parent.jQuery('#widget-writepost .ui-modbody');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// com_flexicontent - J3 - admin
			element = window.parent.jQuery('#fcform_tabset_0');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				element.height('100%');
				return true;
			}

			// com_jreviews
			element = window.parent.jQuery('.jr-tabs');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// General Joomla 3 - Component
			element = window.parent.jQuery('#content');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			// General Joomla 2.5 - Component
			element = window.parent.jQuery('#element-box div.m');
			if (element.size())
			{
				ItemEditorHelper.addHtmlPanel(element, panel);
				return true;
			}

			return false;
		},

		addHtmlPanel: function(sidebar, panel) {
			var elem;

			elem = window.parent.document.getElementById('autotweet-advanced-attrs');

			if (elem) {
				elem.parentNode.removeChild(elem);
			}

			sidebar.append(panel);
		},

		loadPanel: function(advancedAttrs) {
			var tpl = [], panel;

			if (_.isEmpty(advancedAttrs)) {
				advancedAttrs = window.parent.autotweetAdvancedAttrs;
			}

			if (_.isEmpty(advancedAttrs)) {
				return false;
			}

			if (templateRender) {
				panel = templateRender(advancedAttrs);
				ItemEditorHelper.setHtmlPanel(panel, advancedAttrs.editorTitle);
			}

			tpl.push(window.parent.autotweetPanelTemplate);
			require(tpl,
				function(template) {
					templateRender = _.template(template),

					panel = templateRender(advancedAttrs);
					ItemEditorHelper.setHtmlPanel(panel, advancedAttrs.editorTitle);
				}
			);
		},

		addHtmlTab: function(title) {
			var tabs;

			// Joomla 3 - Admin
			tabs = window.parent.jQuery('#myTabTabs');
			if (tabs.size()) {
				ItemEditorHelper._addHtmlTab(title, tabs);
				return true;
			}

			// Joomla 3 - Front
			tabs = window.parent.jQuery('#adminForm ul.nav-tabs');
			if (tabs.size()) {
				ItemEditorHelper._addHtmlTab(title, tabs);
				return true;
			}

			// com_autotweet requests qTypeTabs
			tabs = window.parent.jQuery('.request-edit #qTypeTabs ul.nav-tabs');
			if (tabs.size()) {
				ItemEditorHelper._addHtmlTab(title, tabs);
				return true;
			}

			return false;
		},

		_addHtmlTab: function(title, tabs) {
			var img = '<img src="' + window.parent.autotweetUrlRoot + 'media/com_autotweet/images/autotweet-icon.png">',
				tab;

			if (!window.parent.jQuery('#myAutoTweetTab').size()) {
			    tab = window.parent.jQuery('<li id="myAutoTweetTab" class=""><a id="myAutoTweetTabToogle" href="#autotweet-advanced-attrs" data-toggle="tab">' + img + ' ' + title + '</a></li>');
				tabs.append(tab);

				window.parent.jQuery('#myAutoTweetTabToogle').click(function (e) {
					e.preventDefault();

					if (_.isFunction($(this).tab)) {
						$(this).tab('show');
					}
				});
			}
		},

		retrieveImages: function() {
			var text, $dummyNode, imgs, img, imgsrc, imgalt;

			text = ItemEditorHelper.getHtmlEditorText();

			$dummyNode = jQuery('<div></div>'),
			$dummyNode.html(text);
			imgs = $dummyNode.find('img');

			// Joomla
			imgsrc = window.parent.jQuery('#jform_images_image_intro').val();
			if (!_.isEmpty(imgsrc)) {
				imgalt = window.parent.jQuery('#jform_images_image_intro_alt').val() || window.parent.jQuery('#jform_images_image_intro_caption').val();
				img = {
					src: imgsrc,
					alt: imgalt
				};
				imgs.push(img);
			}

			imgsrc = window.parent.jQuery('#jform_images_image_fulltext').val();
			if (!_.isEmpty(imgsrc)) {
				imgalt = window.parent.jQuery('#jform_images_image_fulltext_alt').val() || window.parent.jQuery('#jform_images_image_fulltext_caption').val();
				img = {
					src: imgsrc,
					alt: imgalt
				};
				imgs.push(img);
			}

			// K2
			imgsrc = window.parent.jQuery('.k2AdminImage').attr('src');
			if (!_.isEmpty(imgsrc)) {
				imgsrc = imgsrc.slice(0, imgsrc.indexOf('_S.jpg'));

				imgalt = window.parent.jQuery('input[name=image_caption]').val();

				// XS
				img = {
					src: imgsrc + '_XS.jpg',
					alt: imgalt + ' XS'
				};
				imgs.push(img);

				// S
				img = {
					src: imgsrc + '_S.jpg',
					alt: imgalt + ' S'
				};
				imgs.push(img);

				// Generic
				img = {
					src: imgsrc + '_Generic.jpg',
					alt: imgalt + ' Generic'
				};
				imgs.push(img);

				// M
				img = {
					src: imgsrc + '_M.jpg',
					alt: imgalt + ' M'
				};
				imgs.push(img);

				// L
				img = {
					src: imgsrc + '_L.jpg',
					alt: imgalt
				};
				imgs.push(img);

				// XL
				img = {
					src: imgsrc + '_XL.jpg',
					alt: imgalt + ' XL'
				};
				imgs.push(img);
			}

			// Zoo
			imgsrc = window.parent.jQuery('.image-preview img');
			if (imgsrc.size()) {
				_.each(imgsrc, function(source) {
					img = {
							src: source.src
					};

					if (source.src.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/)) {
						imgs.push(img);
					}
				});
			}

			// EasyBlog
			imgsrc = window.parent.jQuery('.imageData').val();
			if (!_.isEmpty(imgsrc)) {
				imgsrc = JSON.parse(_.unescape(imgsrc));
				img = {
					src: imgsrc.url,
					alt: imgsrc.title
				};
				imgs.push(img);
			}

			// E-Shop
			imgsrc = window.parent.jQuery('input[name=product_image]+img').attr('src');
			if (!_.isEmpty(imgsrc)) {
				imgs.push({
					src: imgsrc
				});
			}

			imgsrc = window.parent.jQuery('#product_images_area img');
			if (imgsrc.size()) {
				_.each(imgsrc, function(source) {
					img = {
							src: source.src
					};
					imgs.push(img);
				});
			}

			return imgs;
		},

		setYesNo: function(v, $field) {
			var data_value;

			if (v != $field.val()) {
				data_value = $field.parent().find('a[data-value=' + v + ']');

				// If there's a button
				if (data_value.size())	{
					data_value.click();
				} else {
					$field.val(v);
					$field.trigger('liszt:updated');
				}
			}
		}
	};

	ItemEditorHelper.loadPanel();

	if (window.parent.autotweetAdvancedAttrs) {

		// Loading values from the plugin itself
		ItemEditorHelper.setHtmlAdvancedAttrs(window.parent.autotweetAdvancedAttrs);
	}

	return ItemEditorHelper;

});
