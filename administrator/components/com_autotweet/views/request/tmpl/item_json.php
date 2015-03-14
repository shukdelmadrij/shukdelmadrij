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

if (AUTOTWEETNG_JOOCIAL)
{
	$params = AdvancedattrsHelper::getAdvancedAttrByReq($this->item->id);
	AutotweetBaseHelper::convertUTCLocalAgenda($params->agenda);
	$this->item->autotweet_advanced_attrs = $params;
}

echo EJSON_START . json_encode($this->item) . EJSON_END;
