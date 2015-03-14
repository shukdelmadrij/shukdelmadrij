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

$this->loadHelper('select');

$layout = $this->input->get('channeltype_id', AutotweetModelChanneltypes::TYPE_FBCHANNEL, 'cmd');
$channeltypeId = $layout;

$channeltypes = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel');
$channeltype = $channeltypes->setId($channeltypeId)->getItem();

$useownapi = $this->item->xtform->get('use_own_api', 2);
$authorizeCanvas = ($useownapi != 2);

$required = '';
$requiredTag = '';
$requiredToken = '';
$requiredId = '';
$requiredCanvasPage = '';

if ($useownapi)
{
	// Check required="required"';
	$requiredTag = '';

	$required = ' required';
	$requiredToken = ' validate-token';
	$requiredId = ' validate-numeric';
}

if (($useownapi) && ($authorizeCanvas))
{
	$requiredCanvasPage = 'required validate-facebookapp';
}

$requireAlbum = ($channeltypeId == AutotweetModelChanneltypes::TYPE_FBPHOTOCHANNEL);

?>
<!-- com_autotweet_OUTPUT_START -->
<span class="loaderspinner72"><?php echo JText::_('COM_AUTOTWEET_LOADING'); ?></span>

<legend>
	<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_ACCOUNTDATA_TITLE'); ?>
</legend>
<?php echo JText::_('COM_AUTOTWEET_CHANNEL_FACEBOOK_DESC'); ?>

<ul class="nav nav-tabs" id="fbChannelTabs">
	<li class=""><a data-toggle="tab" href="#fbapp">
		 <i class="xticon xticon-facebook"></i>
		 1. <?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_TAB_FBAPPDEFINITION'); ?>
	</a></li>
	<li class=""><a data-toggle="tab" href="#fbauth">
		 <i class="xticon xticon-lock"></i>
		 2. <?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_TAB_AUTHORIZATION'); ?></a>
	</li>
	<li class=""><a data-toggle="tab" href="#fbchannel">
		<i class="xticon xticon-bullhorn"></i>
		3. <?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_TAB_CHANNEL'); ?>
	</a></li>
</ul>

<div class="tab-content" id="fbChannelTabsContent" style="height: 370px">
<?php

	include_once 'step1.php';
	include_once 'step2.php';
	include_once 'step3.php';

?>
</div>
<script type="text/javascript">
var autotweet_canvas_app_url='<?php

echo $channeltype->get('auth_url');

?>';
</script>
<!-- com_autotweet_OUTPUT_START -->
