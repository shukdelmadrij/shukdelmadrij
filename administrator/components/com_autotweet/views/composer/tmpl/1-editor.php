<?php
/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<form id="adminForm" name="adminForm" action="index.php" method="post"
	class="form form-horizontal form-validate"
	ng-controller="EditorController as editorCtrl"
	ng-submit="editorCtrl.addRequest($event)">
	<input type="hidden" name="option" value="com_autotweet" /> <input
		type="hidden" name="view" value="composer" /> <input type="hidden"
		name="task" value="" /> <input type="hidden" name="returnurl"
		value="<?php

		echo base64_encode(JRoute::_('index.php?option=com_autotweet&view=cpanels'));

		?>" />
<?php
echo EHtml::renderRoutingTags();

// Publish_up

echo '<input type="hidden" name="plugin"
 		ng-init="editorCtrl.plugin = \'autotweetpost\'"
 		ng-value="editorCtrl.plugin" />';

echo '<input type="hidden" name="ref_id"
 		ng-init="editorCtrl.ref_id = \'' . AutotweetBaseHelper::getHash() . '\'"
 		ng-value="editorCtrl.ref_id"/>';

echo '<input type="hidden" name="id"
 		ng-init="editorCtrl.request_id = 0"
 		ng-value="editorCtrl.request_id" />';

echo '<input type="hidden" name="published" value="0" />';

?>
<fieldset>
		<div class="row-fluid">
			<div class="span12">

				<p class="text-center" ng-if="editorCtrl.waiting"><span class="loaderspinner72 loading72">
					<?php echo JText::_('COM_AUTOTWEET_LOADING'); ?>
				</span></p>

				<div class="control-group" ng-if="editorCtrl.showDialog">
					<div class="alert alert-success" ng-if="editorCtrl.messageResult">
						<button type="button" class="close"
							ng-click="editorCtrl.showDialog = false">&times;</button>
						<div ng-bind-html="editorCtrl.messageText"></div>
					</div>
					<div class="alert alert-error" ng-if="!editorCtrl.messageResult">
						<button type="button" class="close"
							ng-click="editorCtrl.showDialog = false">&times;</button>
						<div ng-bind-html="editorCtrl.messageText"></div>
					</div>
				</div>

				<div class="control-group">
					<textarea id="description" rows="2" class="span12"
						placeholder="<?php echo JText::_('COM_AUTOTWEET_COMPOSER_TYPE_MESSAGE_LABEL'); ?>" ng-model="editorCtrl.description"
						ng-change="editorCtrl.countRemaining()"></textarea>
					<br /> <span class="xtd-counter pull-right">
					<sub ng:class="{true:'text-info', false:'text-error'}[editorCtrl.remainingCount >= 0]">
					{{editorCtrl.remainingCount}} / 140</sub></span>
				</div>

				<div class="control-group">

					<div class="input-prepend span8">
						<span class="add-on">
							<i class="xticon xticon-link"></i>
						</span>

						<input type="text"
							placeholder="<?php echo JText::_('COM_AUTOTWEET_COMPOSER_TYPE_URL_LABEL'); ?>" ng-model="editorCtrl.url" class="span9">
<?php
	if (EXTLY_J3)
	{
?>
						<span class="add-on">
							<a ng-click="editorCtrl.selectMenu = (editorCtrl.selectMenu ? false : true)">
								<i class="xticon xticon-caret-square-o-right "></i>
							</a>
						</span>
<?php
	}
?>
					</div>

					<div class="pull-right post-attrs-group">
						<input type="hidden" value="2" id="xtformid5095" name="postAttrs" class="ng-pristine ng-untouched ng-valid">
						<div data-toggle="buttons-radio" class="xt-group">
						<a class="xt-button btn btn-small" data-value="i" data-ref="xtformid5095">
							<?php echo JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_IMGCHOOSER_ICON'); ?></a>
<?php
		if (AUTOTWEETNG_JOOCIAL)
		{
?>
						<a class="xt-button btn btn-small" data-value="b" data-ref="xtformid5095">
							<?php echo JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_BASIC_ICON'); ?></a>
						<a class="xt-button btn btn-small" data-value="c" data-ref="xtformid5095">
							<?php echo JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_CHANNELCHOOSER_ICON'); ?></a>
						<a class="xt-button btn btn-small" data-value="s" data-ref="xtformid5095">
							<?php echo JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_SCHEDULER_ICON'); ?></a>
						<a class="xt-button btn btn-small" data-value="r" data-ref="xtformid5095">
							<?php echo JText::_('COM_AUTOTWEET_VIEW_ITEMEDITOR_REPEAT_ICON'); ?></a>
<?php
		}
?>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
	if (EXTLY_J3)
	{
?>
		<div class="row-fluid">
			<div class="span12">
				<div class="control-group" ng-if="editorCtrl.selectMenu">
					<div class="input-prepend">
						<span class="add-on">
							<i class="xticon xticon-list"></i>
						</span>
<?php
				echo EHtmlSelect::menuitemlist(
					null,
					'selectedMenuItem',
					array(
						'ng-model' => "editorCtrl.selectedMenuItem",
						'ng-change' => "editorCtrl.url = editorCtrl.selectedMenuItem",
						'class' => 'span12',
						'size' => 1
					)
				);
?>
					</div>
				</div>
			</div>
		</div>
<?php
	}

/*
				<div class="control-group" ng-if="!editorCtrl.selectMenu">
					<label data-original-title="<?php echo JText::_('COM_AUTOTWEET_COMPOSER_PLEASE_ENTER'); ?>" rel="tooltip" class="control-label">
						<?php echo JText::_('COM_AUTOTWEET_COMPOSER_PLEASE_ENTER'); ?>
					</label>
				</div>

			<div class="span4" style="margin-left: 0;">
				<button type="submit" class="btn pull-right"><?php echo JText::_('JSUBMIT'); ?></button>
			</div>
*/

		echo '<div class="row-fluid xt-subform xt-subform-i alert alert-info span12" style="display: none;">';
		include_once '1-1-image.php';

		if (AUTOTWEETNG_JOOCIAL)
		{
			echo '</div><div class="row-fluid xt-subform xt-subform-b alert alert-info span12" style="display: none;">';
			include JPATH_ADMINISTRATOR . '/components/com_autotweet/views/itemeditor/tmpl/1-basic.php';

			echo '</div><div class="row-fluid xt-subform xt-subform-c alert alert-info span12" style="display: none;">';
			include JPATH_ADMINISTRATOR . '/components/com_autotweet/views/itemeditor/tmpl/6-channels.php';

			echo '</div><div class="row-fluid xt-subform xt-subform-s alert alert-info span12" style="display: none;">';
			include '1-2-scheduler.php';

			echo '</div><div class="row-fluid xt-subform xt-subform-r alert alert-info span12" style="display: none;">';
			include JPATH_ADMINISTRATOR . '/components/com_autotweet/views/itemeditor/tmpl/3-repeat.php';
			echo '</div>';
		}

		/*
		 * Created
		* Created_by
		* Modified
		* Modified_by
		*/

		/*
		 echo EHtml::textControl(JFactory::getUser()->id, 'xtform[author]', 'xtform[author]', 'xtform[author]');
		echo EHtml::textControl(null, 'xtform[title]', 'xtform[title]', 'xtform[title]');
		echo EHtml::textControl(null, 'xtform[article_text]', 'xtform[article_text]', 'xtform[article_text]');
		echo EHtml::textControl(null, 'xtform[hashtags]', 'xtform[hashtags]', 'xtform[hashtags]');
		echo EHtml::textControl(null, 'xtform[catid]', 'xtform[catid]', 'xtform[catid]');
		echo EHtml::textControl(null, 'xtform[language]', 'xtform[language]', 'xtform[language]');
		echo EHtml::textControl(null, 'xtform[access]', 'xtform[access]', 'xtform[access]');
		echo EHtml::textControl(null, 'xtform[target_id]', 'xtform[target_id]', 'xtform[target_id]');
		*/

		/*
		 * <input id="autotweet_advanced_attrs"
		* 	type="hidden"
		* 	value="{"postthis":"3","evergreen":"2","agenda":["2014-10-04 15:35:00","2014-10-01 21:21:00"],"unix_mhdmd":"","image":"","channels":["10"],"client_id":true,"option":"com_content","controller":null,"task":"save","view":null,"layout":"edit","ref_id":13401,"channels_text":"Test","editorTitle":"Joocial Post Management","postthisLabel":"Post this","evergreenLabel":"Evergreen Post","agendaLabel":"Posting Date","unix_mhdmdLabel":"Repeat Expression","imageLabel":"Post Image","channelLabel":"Channels","postthisDefaultLabel":"<i class=\"xticon xticon-circle-o\"></i> Default","postthisYesLabel":"<i class=\"xticon xticon-check\"></i> Yes","postthisNoLabel":"<i class=\"xticon xticon-times\"></i> No"}"
		* 	name="autotweet_advanced_attrs">
		*
		* echo '<input type="hidden" name="autotweet_advanced_attrs" value="" ng-model="editorCtrl.attrs"/>';
		*/

?>

	</fieldset>
</form>
