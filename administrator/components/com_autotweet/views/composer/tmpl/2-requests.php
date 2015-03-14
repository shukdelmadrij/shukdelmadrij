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

JFactory::getDocument()->addStyleDeclaration('.table-hover tbody tr:hover > td {
	cursor: pointer;
	cursor: hand;
}

.table-hover tbody tr .xt-req-editor {
	display: none;
}

.table-hover tbody tr:hover .xt-req-editor {
	display: inline-block;
}');

?>
<form name="adminForm" method="post"
	ng-controller="RequestsController as requestsCtrl">
	<input type="hidden" name="option" value="com_autotweet" /> <input
		type="hidden" name="view" value="composer" /> <input type="hidden"
		name="task" value="" /> <input type="hidden" name="returnurl"
		value="<?php

		echo base64_encode(JRoute::_('index.php?option=com_autotweet&view=cpanels'));

		?>" />
<?php
echo EHtml::renderRoutingTags();
?>
	<fieldset>

		<p class="text-center" ng-if="requestsCtrl.waiting"><span class="loaderspinner72 loading72">
			<?php echo JText::_('COM_AUTOTWEET_LOADING'); ?>
		</span></p>

        <table ng-table="requestsCtrl.requestsTable" class="table table-hover ng-table-rowselected">
        	<thead>
	            <tr>
	                <th><?php echo JText::_('COM_AUTOTWEET_VIEW_MSGLOG_POSTDATE_TITLE'); ?></th>
	                <th><?php echo JText::_('COM_AUTOTWEET_REQUESTS_FIELD_MESSAGE'); ?></th>
	                <th><?php echo JText::_('COM_AUTOTWEET_VIEW_SOURCE_TITLE'); ?></th>
	                <?php

// <th><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); </th>

?>
	            </tr>
	        </thead>
        	<tbody>
				<tr ng-repeat="request in $data"
					ng-click="requestsCtrl.requestsTable.selectRow(request)"
					ng-class="{'info': request.$selected}">
					<td class="span3">
						{{request.publish_up | date:'d MMM H:mm':'UTC'}}</td>
					<td class="span8">
						{{request.description}} -

						<a title="<?php echo JText::_('COM_AUTOTWEET_COMPOSER_VIEW_URL'); ?>"
							href="{{request.url}}" target="_blank" ng-if="request.url != null"><i class="xticon xticon-globe"></i></a>

						<a title="<?php echo JText::_('COM_AUTOTWEET_COMPOSER_VIEW_IMAGE'); ?>"
							href="{{request.image_url}}" target="_blank" ng-if="request.image_url != null"><i class="xticon xticon-image"></i></a>

						<span ng-if="request.published == 1"> <i class="xticon xticon-check text-success"></i></span>

						<span ng-if="request.published == 0"> <i class="xticon xticon-clock-o text-warning"></i></span>

						<div class="btn-group pull-right xt-req-editor">
<?php
						if ($this->get('editown'))
						{
?>
							<a class="btn btn-mini"
								ng-click="requestsCtrl.requestsTable.editRow(request)"><i class="xticon xticon-pencil"></i></a>
<?php
						}

						if ($this->get('editstate'))
						{
?>
							<a ng-if="request.published == 0" class="btn btn-success btn-mini"
								ng-click="requestsCtrl.requestsTable.publishRow(request)"><i class="xticon xticon-check"></i></a>
							<a ng-if="request.published == 0" class="btn btn-inverse btn-mini"
								ng-click="requestsCtrl.requestsTable.cancelRow(request)"><i class="xticon xticon-times"></i></a>

							<a ng-if="request.published == 1" class="btn btn-inverse btn-mini"
								ng-click="requestsCtrl.requestsTable.backtoQueueRow(request)"><i class="xticon xticon-repeat"></i></a>
<?php
						}
?>
						</div>
					</td>
					<td class="span1">{{request.plugin}}</td>
<?php

// <td class="span1">{{request.id}}</td>

?>
					<!-- ID: {{request.id}} -->
				</tr>
			</tbody>
		</table>

	</fieldset>
</form>
