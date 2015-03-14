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
<div class="row-fluid import-progress hide">
	<div class="span3">
	</div>

	<div class="span6">
		<div class="alert alert-info alert-block">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <h4 class="alert-heading"><?php echo JText::_('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_PROGRESS'); ?></h4>

            <p>&nbsp;</p>

		    <div class="progress progress-striped active">
    			<div class="bar" style="width: 0%;"></div>
    		</div>

    		<p>
    			<span class="label label-info"><?php echo JText::_('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_FEED_NAME'); ?></span>
    			<input type="text" readonly="readonly" class="feed span4" value="">
    			<span class="label label-info"><?php echo JText::_('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_ARTICLE_LABEL'); ?></span>
    			<input type="text" readonly="readonly" class="total span2" value="">
    		</p>

    		<p class="text-center success-message hide" style="text-align: center;">
    			<span class="label label-success success-message"><?php echo JText::_('COM_AUTOTWEET_VIEW_FEEDS_IMPORT_SUCCESS'); ?></span><br/><br/>
    		</p>

         </div>
	</div>

	<div class="span3">
	</div>

</div>
