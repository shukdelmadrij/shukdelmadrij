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

/**
 * SelectControlHelper
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class SelectControlHelper
{
	const REQ_ICON_YES = '<i class="xticon xticon-check text-success"></i>';

	const REQ_ICON_NO = '<i class="xticon xticon-clock-o text-warning"></i>';

	/**
	 * channels.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $idTag     Param
	 *
	 * @return  string  HTML
	 */
	public static function channels($selected = null, $name = 'channel', $attribs = array(), $idTag = null)
	{
		$channelsModel = F0FModel::getTmpInstance('Channels', 'AutoTweetModel');
		$channelsModel->set('published', 1);
		$channelsModel->set('scope', 'N');
		$items = $channelsModel->getItemList(true);

		$options = array();

		if ((!array_key_exists('multiple', $attribs)) || (!$attribs['multiple']))
		{
			$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');
		}

		if (count($items))
		{
			foreach ($items as $item)
			{
				$options[] = JHTML::_('select.option', $item->id, $item->name);
			}
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $idTag);
	}

	/**
	 * channelsMultiRadio.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $idTag     Param
	 *
	 * @return  string  HTML
	 */
	public static function channelsMultiRadio($selected = null, $name = 'channel', $attribs = array(), $idTag = null)
	{
		$channelsModel = F0FModel::getTmpInstance('Channels', 'AutoTweetModel');
		$channelsModel->set('published', 1);
		$channelsModel->set('scope', 'N');
		$items = $channelsModel->getItemList(true);

		$options = array();

		if (count($items))
		{
			foreach ($items as $item)
			{
				$options[] = JHTML::_('select.option', $item->id, $item->name);
			}
		}

		return EHtmlSelect::checkboxList($options, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	/**
	 * plugins.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   array   &$config   Config
	 *
	 * @return  string  HTML
	 */
	public static function plugins($selected = null, $name = 'plugin', $attribs = array(), &$config = null)
	{
		$pluginsModel = F0FModel::getTmpInstance('Plugins', 'AutoTweetModel');
		$pluginsModel->set('extension_plugins_only', true);
		$pluginsModel->set('published_only', true);
		$items = $pluginsModel->getItemList(true);

		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		if (count($items))
		{
			foreach ($items as $item)
			{
				$nameValue = $pluginsModel->getSimpleName($item->element);
				$options[] = JHTML::_('select.option', $item->element, $nameValue);
			}
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * ruletypes.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function ruletypes($selected = null, $name = 'ruletype', $attribs = array())
	{
		$channeltypes = F0FModel::getTmpInstance('Ruletypes', 'AutotweetModel');
		$items = $channeltypes->getItemList(true);

		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		if (count($items))
		{
			foreach ($items as $item)
			{
				$options[] = JHTML::_('select.option', $item->id, $item->name);
			}
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * channeltypes.
	 *
	 * @param   string  $selected              The key that is selected
	 * @param   string  $name                  The name for the field
	 * @param   array   $attribs               Additional HTML attributes for the <select> tag
	 * @param   bool    $onlyFrontEndChannels  Param
	 *
	 * @return  string  HTML
	 */
	public static function channeltypes($selected = null, $name = 'channeltype', $attribs = array(), $onlyFrontEndChannels = false)
	{
		$channeltypes = F0FModel::getTmpInstance('Channeltypes', 'AutotweetModel');
		$items = $channeltypes->getItemList(true);

		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		if ($onlyFrontEndChannels)
		{
			$channels = F0FModel::getTmpInstance('Channels', 'AutotweetModel');
			$channels->setState('frontendchannel', 1);
			$frontChannels = $channels->getItemList(true);

			$ids = $channels->getChannelTypes($frontChannels);
		}

		if (count($items))
		{
			foreach ($items as $item)
			{
				if ((!$onlyFrontEndChannels) || (in_array($item->id, $ids)))
				{
					$options[] = JHTML::_('select.option', $item->id, $item->name);
				}
			}
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * getRuletypeName.
	 *
	 * @param   string  $ruletype  Param
	 *
	 * @return  string
	 */
	public static function getRuletypeName($ruletype)
	{
		static $ruletypes = null;

		if (is_null($ruletypes))
		{
			$ruletypesModel = F0FModel::getTmpInstance('Ruletypes', 'AutotweetModel');
			$items = $ruletypesModel->getItemList(true);

			if (count($items))
			{
				foreach ($items as $item)
				{
					$ruletypes[$item->id] = $item->name;
				}
			}
			else
			{
				return '?';
			}
		}

		if (array_key_exists($ruletype, $ruletypes))
		{
			return $ruletypes[$ruletype];
		}
		else
		{
			return '?';
		}
	}

	/**
	 * getChanneltypeName.
	 *
	 * @param   string  $channeltype  Param
	 *
	 * @return  string
	 */
	public static function getChanneltypeName($channeltype)
	{
		static $channeltypes = null;

		if (is_null($channeltypes))
		{
			$channeltypesModel = F0FModel::getTmpInstance('Channeltypes', 'AutotweetModel');
			$items = $channeltypesModel->getItemList(true);

			if (count($items))
			{
				foreach ($items as $item)
				{
					$channeltypes[$item->id] = $channeltypesModel->getIcon($item->id) . ' - ' . $item->name;
				}
			}
			else
			{
				return '?';
			}
		}

		if (array_key_exists($channeltype, $channeltypes))
		{
			return $channeltypes[$channeltype];
		}
		else
		{
			return '?';
		}
	}

	/**
	 * getChannelName.
	 *
	 * @param   string  $channel   Param
	 * @param   bool    $isModule  Param
	 *
	 * @return  string
	 */
	public static function getChannelName($channel, $isModule = false)
	{
		static $channels = null;

		if (is_null($channels))
		{
			F0FModel::getTmpInstance('Channeltypes', 'AutotweetModel');
			$itemsModel = F0FModel::getTmpInstance('Channels', 'AutotweetModel');
			$itemsModel->set('scope', 'N');
			$itemsModel->set('published', true);

			$items = $itemsModel->getItemList(true);

			if (count($items))
			{
				foreach ($items as $item)
				{
					$icon = AutotweetModelChanneltypes::getIcon($item->channeltype_id);

					if ($isModule)
					{
						$channels[$item->id] = $icon;
					}
					else
					{
						$channels[$item->id] = $icon . ' - ' . $item->name;
					}
				}
			}
			else
			{
				return '?';
			}
		}

		if (array_key_exists($channel, $channels))
		{
			return $channels[$channel];
		}
		else
		{
			return '?';
		}
	}

	/**
	 * showstatictext.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function showstatictext($selected = null, $name = 'show_static_text', $attribs = array() )
	{
		$options = array();

		$selected = ($selected ? $selected : 'selected');

		// Get media modes
		$modes = self::getShowStaticTextEnum();

		// Generate html
		foreach ($modes as $mode)
		{
			$options[] = JHtml::_('select.option', $mode, JText::_(self::getTextForEnum($mode)), 'value', 'text');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * showurl.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function showurl($selected = null, $name = 'show_url', $attribs = array() )
	{
		$options = array();

		$selected = ($selected ? $selected : 'selected');

		// Get media modes
		$modes = self::getShowurlEnum();

		// Generate html
		foreach ($modes as $mode)
		{
			$options[] = JHtml::_('select.option', $mode, JText::_(self::getTextForEnum($mode)), 'value', 'text');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * mediamodes.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function autopublish($selected = null, $name = 'autopublish', $attribs = array() )
	{
		$options = array();

		$selected = ($selected ? $selected : 'on');

		// Get media modes
		$modes = self::getAutopublishEnum();

		// Generate html
		foreach ($modes as $mode)
		{
			$options[] = JHtml::_('select.option', $mode, JText::_(self::getTextForEnum($mode)), 'value', 'text');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * mediamodes.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function mediamodes($selected = null, $name = 'type', $attribs = array() )
	{
		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		// Get media modes
		$modes = self::getMediaModeEnum();

		// Generate html
		foreach ($modes as $mode)
		{
			$options[] = JHtml::_('select.option', $mode, JText::_(self::getTextForEnum($mode)), 'value', 'text');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * pubstates.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $idTag     Param
	 * @param   bool    $isFilter  Param
	 *
	 * @return  string  HTML
	 */
	public static function pubstates($selected = null, $name = 'type', $attribs = array(), $idTag = null, $isFilter = false)
	{
		$platform = F0FPlatform::getInstance();
		$input = new F0FInput;
		$editstate = $platform->authorise('core.edit.state', $input->getCmd('option', 'com_foobar'));

		if (($editstate) || ($isFilter))
		{
			if ((!$isFilter) && ($selected === null))
			{
				$selected = 'approve';
			}

			$options = array();
			$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

			// Get media modes
			$modes = self::getPubstateEnum();

			// Generate html
			foreach ($modes as $mode)
			{
				$options[] = JHtml::_('select.option', $mode, JText::_(self::getTextForEnum($mode)), 'value', 'text');
			}

			return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
		}
		else
		{
			if (!$selected)
			{
				$selected = 'approve';
			}

			$tag = JText::_(self::getTextForEnum($selected));

			$control = EHtml::readonlyText($tag, $name . '-readonly');
			$control .= '<input type="hidden" value="' . $selected . '" name="' . $name . '" id="' . $idTag . '">';

			return $control;
		}
	}

	/**
	 * pubstatesControl.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   string  $label     Param
	 * @param   string  $desc      Param
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $idTag     Param
	 *
	 * @return  string  HTML
	 */
	public static function pubstatesControl($selected = null, $name = 'type', $label = null, $desc = null, $attribs = array(), $idTag = null)
	{
		if (!$idTag)
		{
			$idTag = EHtml::generateIdTag();
		}

		$control = self::pubstates($selected, $name, $attribs, $idTag);

		return EHtml::genericControl(
				JText::_($label),
				JText::_($desc),
				$name,
				$control
		);
	}

	/**
	 * targettypes.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function targettypes($selected = null, $name = 'targettype_id', $attribs = array() )
	{
		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		// Get media modes
		$modes = self::getTargettypesEnum();

		// Generate html
		foreach ($modes as $mode)
		{
			$options[] = JHtml::_('select.option', $mode, JText::_(self::getTextForEnum($mode)), 'value', 'text');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * getTargettypesName.
	 *
	 * @param   string  $value  Param
	 *
	 * @return  string
	 */
	public static function getTargettypesName($value)
	{
		return JText::_(self::getTextForEnum($value));
	}

	/**
	 * tasktypes.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function tasktypes($selected = null, $name = 'type', $attribs = array() )
	{
		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		$options[] = JHTML::_('select.option', '1', JText::_('COM_AUTOTWEET_TASKS_TYPE_1'));
		$options[] = JHTML::_('select.option', '2', JText::_('COM_AUTOTWEET_TASKS_TYPE_2'));
		$options[] = JHTML::_('select.option', '3', JText::_('COM_AUTOTWEET_TASKS_TYPE_3'));
		$options[] = JHTML::_('select.option', '4', JText::_('COM_AUTOTWEET_TASKS_TYPE_4'));
		$options[] = JHTML::_('select.option', '5', JText::_('COM_AUTOTWEET_TASKS_TYPE_5'));
		$options[] = JHTML::_('select.option', '6', JText::_('COM_AUTOTWEET_TASKS_TYPE_6'));
		$options[] = JHTML::_('select.option', '7', JText::_('COM_AUTOTWEET_TASKS_TYPE_7'));

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Method to getPubstateEnum.
	 *
	 * @return	array.
	 */
	public static function getPubstateEnum()
	{
		return array('success', 'error', 'approve', 'cronjob', 'cancelled');
	}

	/**
	 * Method to getTargettypesEnum.
	 *
	 * @return	array.
	 */
	public static function getTargettypesEnum()
	{
		return array(
				'fbgenders',
				'fbage_max',
				'fbage_min',
				'fbcountries',
				'fbregions',
				'fbcities',
				'fbrelationship_statuses',
				'fbinterested_in',
				'fblocales',
				'fbeducation_statuses',
				'fbwork_networks',
				'fbcollege_networks',
				'fbcollege_majors',
				'fbcollege_years'
		);
	}

	/**
	 * Method to getMediaModeEnum.
	 *
	 * @return	array.
	 */
	public static function getMediaModeEnum()
	{
		return array('message', 'attachment', 'both');
	}

	/**
	 * Method to getAutopublishEnum.
	 *
	 * @return	array.
	 */
	public static function getAutopublishEnum()
	{
		return array('selected', 'on', 'off', 'cancel');
	}

	/**
	 * Method to getShowUrlEnum.
	 *
	 * @return	array.
	 */
	public static function getShowUrlEnum()
	{
		return array('selected', 'off', 'beginning_of_message', 'end_of_message');
	}

	/**
	 * Method to getShowStaticTextEnum.
	 *
	 * @return	array.
	 */
	public static function getShowStaticTextEnum()
	{
		return array('off', 'beginning_of_message', 'end_of_message');
	}

	/**
	 * Method to getTextForEnum.
	 *
	 * @param   string  $enum_string  Param
	 * @param   bool    $with_icon    Param
	 * @param   bool    $isModule     Param
	 *
	 * @return	string.
	 */
	public static function getTextForEnum($enum_string, $with_icon = false, $isModule = false)
	{
		switch ($enum_string)
		{
			case 'selected':
				$result = '-' . JText::_('JSELECT') . '-';
				break;

			case 'default':
				$result = JText::_('JDEFAULT');
				break;
			case 'on':
				$result = JText::_('JON');
				break;
			case 'off':
				$result = JText::_('JOFF');
				break;
			case 'cancel':
				$result = JText::_('JCANCEL');
				break;

			case 'message':
				$result = JText::_('COM_AUTOTWEET_OPTION_MEDIAMODE_MESSAGE');
				break;
			case 'attachment':
				$result = JText::_('COM_AUTOTWEET_OPTION_MEDIAMODE_ATTACHMENT');
				break;
			case 'both':
				$result = JText::_('COM_AUTOTWEET_OPTION_MEDIAMODE_BOTH');
				break;

			case 'beginning_of_message':
				$result = JText::_('COM_AUTOTWEET_OPTION_POSITION_BEGINNINGOFMESSAGE');
				break;
			case 'end_of_message':
				$result = JText::_('COM_AUTOTWEET_OPTION_POSITION_ENDOFMESSAGE');
				break;

			case 'success':
				$result = ($with_icon ? '<i class="xticon xticon-check text-success"></i>' : '') . ($isModule ? '' : ' - ' . JText::_('COM_AUTOTWEET_STATE_PUBSTATE_SUCCESS'));
				break;
			case 'error':
				$result = ($with_icon ? ' <i class="xticon xticon-times-circle text-error"></i>' : '') . ($isModule ? '' : ' - ' . JText::_('COM_AUTOTWEET_STATE_PUBSTATE_ERROR'));
				break;
			case 'approve':
				$result = ($with_icon ? ' <i class="xticon xticon-square-o"></i>' : '') . ($isModule ? '' : ' - ' . JText::_('COM_AUTOTWEET_STATE_PUBSTATE_APPROVE'));
				break;
			case 'cronjob':
				$result = ($with_icon ? ' <i class="xticon xticon-clock-o"></i>' : '') . ($isModule ? '' : ' - ' . JText::_('COM_AUTOTWEET_STATE_PUBSTATE_CRONJOB'));
				break;
			case 'cancelled':
				$result = ($with_icon ? ' <i class="xticon xticon-thumbs-down muted"></i>' : '') . ($isModule ? '' : ' - ' . JText::_('COM_AUTOTWEET_STATE_PUBSTATE_CANCELLED'));
				break;

			case 'fbgenders':
			case 'fbage_max':
			case 'fbage_min':
			case 'fbcountries':
			case 'fbregions':
			case 'fbcities':
			case 'fbrelationship_statuses':
			case 'fbinterested_in':
			case 'fblocales':
			case 'fbeducation_statuses':
			case 'fbwork_networks':
			case 'fbcollege_networks':
			case 'fbcollege_majors':
			case 'fbcollege_years':
				$result = JText::_('COM_AUTOTWEET_VIEW_TARGET_' . $enum_string);
				break;

			case 'feedcontent':
				$result = 'Joomla Content';
				break;
			case 'feedk2':
				$result = 'K2';
				break;
			case 'feedzoo':
				$result = 'Zoo - Coming Soon';
				break;

			default:
				$result = 'AUTOTWEET_MISSING_LANGUAGE_STRING';
				break;
		}

		return $result;
	}

	/**
	 * fbchannels.
	 *
	 * @param   string  $selected      The key that is selected
	 * @param   string  $name          The name for the field
	 * @param   array   $attribs       Additional HTML attributes for the <select> tag*
	 * @param   string  $app_id        Params
	 * @param   string  $secret        Params
	 * @param   string  $access_token  Params
	 *
	 * @return  string  HTML
	 */
	public static function fbchannels(
		$selected = null,
		$name = 'xtform[fbchannel_id]',
		$attribs = array(),
		$app_id = null,
		$secret = null,
		$access_token = null)
	{
		$options = array();
		$attribs = array();

		if ($access_token)
		{
			require_once dirname(__FILE__) . '/../helpers/channels/fbapp.php';

			try
			{
				$fbAppHelper = new FbAppHelper($app_id, $secret, $access_token);

				if ($fbAppHelper->login())
				{
					$channels = $fbAppHelper->getChannels();
					$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
						->getIcon(AutotweetModelChanneltypes::TYPE_FBCHANNEL);

					foreach ($channels as $channel)
					{
						$nm = $channel['name'];

						if ((empty($nm)) || ($nm == 'null'))
						{
							$nm = $channel['id'];
						}

						$opt = JHTML::_('select.option', $channel['id'], $channel['type'] . ': ' . $nm);

						if (array_key_exists('access_token', $channel))
						{
							$opt->access_token = array(
										'access_token' => $channel['access_token'],
										'social_icon' => $icon,
										'social_url' => $channel['url'],
										'data_type' => $channel['type']
									);
						}

						$options[] = $opt;
					}

					$attribs['id'] = $name;
					$attribs['list.attr'] = null;
					$attribs['list.translate'] = false;
					$attribs['option.key'] = 'value';
					$attribs['option.text'] = 'text';
					$attribs['list.select'] = $selected;
					$attribs['option.attr'] = 'access_token';

					return EHtmlSelect::genericlist($options, $name, $attribs);
				}
				else
				{
					$error_message = 'Facebook Login Failed!';
					$options[] = JHTML::_('select.option', '', $error_message);
				}
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
				$options[] = JHTML::_('select.option', '', $error_message);
			}
		}
		else
		{
			$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name, array('option.attr' => 'access_token'));
	}

	/**
	 * fbalbums.
	 *
	 * @param   string  $selected      The key that is selected
	 * @param   string  $name          The name for the field
	 * @param   array   $attribs       Additional HTML attributes for the <select> tag*
	 * @param   string  $app_id        Params
	 * @param   string  $secret        Params
	 * @param   string  $access_token  Params
	 * @param   string  $channelid     Params
	 *
	 * @return  string  HTML
	 */
	public static function fbalbums(
		$selected = null,
		$name = 'xtform[fbalbum_id]',
		$attribs = array(),
		$app_id = null,
		$secret = null,
		$access_token = null,
		$channelid = null)
	{
		$options = array();
		$attribs = array();

		if ($access_token)
		{
			require_once dirname(__FILE__) . '/../helpers/channels/fbapp.php';

			try
			{
				$fbAppHelper = new FbAppHelper($app_id, $secret, $access_token);

				if ($fbAppHelper->login())
				{
					$albums = $fbAppHelper->getAlbums($channelid);

					foreach ($albums as $album)
					{
						$nm = $album['name'];

						if ((empty($nm)) || ($nm == 'null'))
						{
							$nm = $album['id'];
						}

						$opt = JHTML::_('select.option', $album['id'], $nm);
						$options[] = $opt;
					}

					$attribs['id'] = $name;
					$attribs['list.attr'] = null;
					$attribs['list.translate'] = false;
					$attribs['option.key'] = 'value';
					$attribs['option.text'] = 'text';
					$attribs['list.select'] = $selected;

					return EHtmlSelect::genericlist($options, $name, $attribs);
				}
				else
				{
					$error_message = 'Facebook Login Failed!';
					$options[] = JHTML::_('select.option', '', $error_message);
				}
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
				$options[] = JHTML::_('select.option', '', $error_message);
			}
		}
		else
		{
			$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name, array('option.attr' => 'access_token'));
	}

	/**
	 * ligroups.
	 *
	 * @param   string  $selected           The key that is selected
	 * @param   string  $name               The name for the field
	 * @param   array   $attribs            Additional HTML attributes for the <select> tag*
	 * @param   string  $api_key            Params
	 * @param   string  $secret_key         Params
	 * @param   string  $oauth_user_token   Params
	 * @param   string  $oauth_user_secret  Params
	 *
	 * @return  string  HTML
	 */
	public static function ligroups(
		$selected = null,
		$name = 'xtform[group_id]',
		$attribs = array(),
		$api_key = null,
		$secret_key = null,
		$oauth_user_token = null,
		$oauth_user_secret = null)
	{
		$options = array();
		$attribs = array();

		if (!empty($oauth_user_secret))
		{
			require_once dirname(__FILE__) . '/../helpers/channels/liapp.php';

			try
			{
				$liAppHelper = new LiAppHelper($api_key, $secret_key, $oauth_user_token, $oauth_user_secret);

				if ($liAppHelper->login())
				{
					$channels = $liAppHelper->getMyGroups();

					if ((count($channels) > 0) && ($channels[0]))
					{
						$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
							->getIcon(AutotweetModelChanneltypes::TYPE_LINKCHANNEL);

						foreach ($channels as $channel)
						{
							$nm = $channel->name;

							if ((empty($nm)) || ($nm == 'null'))
							{
								$nm = $channel->id;
							}

							$attr = 'social_url="' . $channel->url . '" social_icon="' . $icon . '"';
							$attrs = array(
											'attr' => $attr,
											'option.attr' => 'social_url',

											'option.key' => 'value',
											'option.text' => 'text',
											'disable' => false
							);

							$opt = JHTML::_('select.option', $channel->id, $nm, $attrs);

							$options[] = $opt;
						}
					}

					$attribs['id'] = $name;
					$attribs['list.attr'] = null;
					$attribs['list.translate'] = false;
					$attribs['option.key'] = 'value';
					$attribs['option.text'] = 'text';
					$attribs['option.attr'] = 'social_url';
					$attribs['list.select'] = $selected;

					return EHtmlSelect::genericlist($options, $name, $attribs);
				}
				else
				{
					$error_message = 'LinkedIn Login Failed (Groups)!';
					$options[] = JHTML::_('select.option', '', $error_message);
				}
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
				$options[] = JHTML::_('select.option', '', $error_message);
			}
		}
		else
		{
			$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name, array('option.attr' => 'access_token'));
	}

	/**
	 * licompanies.
	 *
	 * @param   string  $selected           The key that is selected
	 * @param   string  $name               The name for the field
	 * @param   array   $attribs            Additional HTML attributes for the <select> tag*
	 * @param   string  $api_key            Params
	 * @param   string  $secret_key         Params
	 * @param   string  $oauth_user_token   Params
	 * @param   string  $oauth_user_secret  Params
	 *
	 * @return  string  HTML
	 */
	public static function licompanies(
		$selected = null,
		$name = 'xtform[company_id]',
		$attribs = array(),
		$api_key = null,
		$secret_key = null,
		$oauth_user_token = null,
		$oauth_user_secret = null)
	{
		$options = array();
		$attribs = array();

		if (!empty($oauth_user_secret))
		{
			require_once dirname(__FILE__) . '/../helpers/channels/liapp.php';

			try
			{
				$liAppHelper = new LiAppHelper($api_key, $secret_key, $oauth_user_token, $oauth_user_secret);

				if ($liAppHelper->login())
				{
					$channels = $liAppHelper->getMyCompanies();

					if ((count($channels) > 0) && ($channels[0]))
					{
						$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
							->getIcon(AutotweetModelChanneltypes::TYPE_LINKCHANNEL);

						foreach ($channels as $channel)
						{
							$nm = $channel->name;

							if ((empty($nm)) || ($nm == 'null'))
							{
								$nm = $channel->id;
							}

							$attr = 'social_url="' . $channel->url . '" social_icon="' . $icon . '"';
							$attrs = array(
											'attr' => $attr,
											'option.attr' => 'social_url',

											'option.key' => 'value',
											'option.text' => 'text',
											'disable' => false
							);

							$opt = JHTML::_('select.option', $channel->id, $nm, $attrs);
							$options[] = $opt;
						}
					}

					$attribs['id'] = $name;
					$attribs['list.attr'] = null;
					$attribs['list.translate'] = false;
					$attribs['option.key'] = 'value';
					$attribs['option.text'] = 'text';
					$attribs['option.attr'] = 'social_url';
					$attribs['list.select'] = $selected;

					return EHtmlSelect::genericlist($options, $name, $attribs);
				}
				else
				{
					$error_message = 'LinkedIn Login Failed (Companies)!';
					$options[] = JHTML::_('select.option', '', $error_message);
				}
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
				$options[] = JHTML::_('select.option', '', $error_message);
			}
		}
		else
		{
			$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name, array('option.attr' => 'access_token'));
	}

	/**
	 * category.
	 *
	 * @param   string  $name        The key that is selected
	 * @param   string  $extension   The name for the field
	 * @param   string  $selected    Additional HTML attributes for the <select> tag*
	 * @param   string  $javascript  Params
	 * @param   string  $order       Params
	 * @param   string  $size        Params
	 * @param   string  $sel_cat     Params
	 * @param   string  $readonly    Params
	 *
	 * @return  string  HTML
	 */
	public static function category($name, $extension, $selected = null, $javascript = null, $order = null, $size = 1, $sel_cat = 1, $readonly = false)
	{
		$categories = JHtml::_('category.options', $extension);

		if ($sel_cat)
		{
			array_unshift($categories, JHTML::_('select.option',  '0', JText::_('JOption_Select_Category')));
		}

		$category = JHTML::_(
				'select.genericlist',
				$categories,
				$name,
				($readonly ? 'readonly="readonly" ' : '') . 'class="inputbox" size="' . $size . '" ' . $javascript,
				'value', 'text',
				$selected
		);

		return $category;
	}

	/**
	 * languages.
	 *
	 * @param   string  $selected     The key that is selected
	 * @param   string  $name         The name for the field
	 * @param   array   $attribs      Additional HTML attributes for the <select> tag*
	 * @param   string  $show_select  Params
	 *
	 * @return  string  HTML
	 */
	public static function languages($selected = null, $name = 'language', $attribs = array(), $show_select = false )
	{
		jimport('joomla.language.helper');
		$languages = JLanguageHelper::getLanguages('lang_code');
		$options = array();

		if ($show_select)
		{
			$options[] = JHTML::_('select.option', '', '---');
		}

		$options[] = JHTML::_('select.option', '*', JText::_('JALL_LANGUAGE'));

		if (!empty($languages))
		{
			foreach ($languages as $key => $lang)
			{
				$options[] = JHTML::_('select.option', $key, $lang->title);
			}
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * fbcities.
	 *
	 * @param   string  $name  The name for the field
	 *
	 * @return  string  HTML
	 */
	public static function fbselect($name = 'fbcities')
	{
		$options = array();
		$attribs = array();

		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		$selected = null;

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name, array('option.attr' => 'access_token'));
	}

	/**
	 * targets.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function targets($selected = null, $name = 'target_id', $attribs = array())
	{
		$targets = F0FModel::getTmpInstance('Targets', 'AutotweetModel');
		$targets->set('published', 1);
		$items = $targets->getItemList(true);

		$options = array();
		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		if (count($items))
		{
			foreach ($items as $item)
			{
				$options[] = JHTML::_('select.option', $item->id, $item->name);
			}
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * contenttypes.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function contenttypes($selected = null, $name = 'contenttypes', $attribs = array() )
	{
		$options = array();

		$selected = ($selected ? $selected : 'on');

		// Get media modes
		$modes = self::getContenttypesEnum();

		$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');

		// Generate html
		foreach ($modes as $mode)
		{
			$options[] = JHtml::_('select.option', $mode, JText::_(self::getTextForEnum($mode)), 'value', 'text');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Method to getFeedtypeEnum.
	 *
	 * @return	array.
	 */
	public static function getContenttypesEnum()
	{
		if (AUTOTWEETNG_JOOCIAL)
		{
			return array('feedcontent', 'feedk2', 'feedzoo');
		}
		else
		{
			return array('feedcontent');
		}
	}

	/**
	 * getContenttypesName.
	 *
	 * @param   string  $value  Param
	 *
	 * @return  string
	 */
	public static function getContenttypesName($value)
	{
		return JText::_(self::getTextForEnum($value));
	}

	/**
	 * saveauthors.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function saveauthors($selected = null, $name = 'saveauthors', $attribs = array() )
	{
		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('JOPTION_USE_DEFAULT'));

		$options[] = JHTML::_('select.option', 1, JText::_('Use default alias'));
		$options[] = JHTML::_('select.option', 2, JText::_('Use custom alias'));
		$options[] = JHTML::_('select.option', 3, JText::_('Use feed author alias, or title'));
		$options[] = JHTML::_('select.option', 4, JText::_('Use feed author alias, or custom'));

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}

	/**
	 * authorarticles.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function authorarticles($selected = null, $name = 'authorarticles', $attribs = array() )
	{
		$options = array();
		$options[] = array('name' => 'JNO', 'value' => '');
		$options[] = array('name' => 'Top', 'value' => 'top');
		$options[] = array('name' => 'Bottom', 'value' => 'bottom');

		return EHtmlSelect::btnGroupList($selected, $name, $attribs, $options, null);
	}

	/**
	 * feedCategories.
	 *
	 * @param   string  $contenttype_id  The contenttype
	 * @param   string  $selected        The key that is selected
	 * @param   string  $name            The name for the field
	 * @param   array   $attribs   	     Additional HTML attributes for the <select> tag*
	 * @param   string  $idTag           The id for the field
	 *
	 * @return  string  HTML
	 */
	public static function feedCategories($contenttype_id, $selected = null, $name = 'catid', $attribs = array(), $idTag = null)
	{
		$categories = array();

		// FeedContent
		$items = JHtml::_('category.options', 'com_content');

		$c = array();

		foreach ($items as $item)
		{
			$c[] = array(
				'id' => $item->value,
				'title' => $item->text,
			);
		}

		$categories['feedcontent'] = $c;

		// FeedK2
		$hasK2 = file_exists(JPATH_ROOT . '/components/com_k2/helpers/route.php');

		if ((AUTOTWEETNG_JOOCIAL) && ($hasK2))
		{
			$catsModel = F0FModel::getTmpInstance('FeedK2Categories', 'AutoTweetModel');
			$items = $catsModel->getItemList(true);

			$c = array();

			foreach ($items as $item)
			{
				$c[] = array(
								'id' => $item->id,
								'title' => $item->title,
				);
			}

			$categories['feedk2'] = $c;
		}

		$document = JFactory::getDocument();
		$document->addScriptDeclaration('var feedCategories = ' . json_encode($categories) . ';');

		$options = array();
		$options[] = JHTML::_('select.option', $selected, '-' . JText::_('JSELECT') . '-');

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $idTag);
	}

	/**
	 * vkgroups
	 *
	 * @param   string  $selected      The key that is selected
	 * @param   string  $name          The name for the field
	 * @param   array   $attribs       Additional HTML attributes for the <select> tag*
	 * @param   string  $access_token  Params
	 * @param   int     $channel_id    Params
	 *
	 * @return  string  HTML
	 */
	public static function vkgroups(
		$selected = null,
		$name = 'xtform[vkgroup_id]',
		$attribs = array(),
		$access_token = null,
		$channel_id = null)
	{
		$options = array();
		$attribs = array();

		if ((!empty($access_token)) && (!empty($channel_id)))
		{
			try
			{
				$ch = F0FTable::getAnInstance('Channel', 'AutoTweetTable');
				$result = $ch->load($channel_id);

				if (!$result)
				{
					break;
				}

				$params = $ch->params;
				$registry = new JRegistry;
				$registry->loadString($params);
				$registry->set('access_token', $access_token);
				$ch->bind(array('params' => (string) $registry));

				$vkChannelHelper = new VkChannelHelper($ch);
				$result = $vkChannelHelper->getGroups();

				if ($result['status'])
				{
					$groups = $result['items'];

					$icon = F0FModel::getTmpInstance('Channeltypes', 'AutoTweetModel')
						->getIcon(AutotweetModelChanneltypes::TYPE_VKCHANNEL);

					foreach ($groups as $group)
					{
						$nm = $group['name'];

						if ((empty($nm)) || ($nm == 'null'))
						{
							$nm = $group['gid'];
						}

						$attr = 'social_url="' . $group['url'] . '" social_icon="' . $icon . '"';
						$attrs = array(
										'attr' => $attr,
										'option.attr' => 'social_url',

										'option.key' => 'value',
										'option.text' => 'text',
										'disable' => false
						);

						$opt = JHTML::_('select.option', $group['gid'], $nm, $attrs);
						$options[] = $opt;
					}
				}

				$attribs['id'] = $name;
				$attribs['list.attr'] = null;
				$attribs['list.translate'] = false;
				$attribs['option.key'] = 'value';
				$attribs['option.text'] = 'text';
				$attribs['option.attr'] = 'social_url';
				$attribs['list.select'] = $selected;

				return EHtmlSelect::genericlist($options, $name, $attribs);
			}
			catch (Exception $e)
			{
				$error_message = $e->__toString();
				$options[] = JHTML::_('select.option', '', $error_message);
			}
		}
		else
		{
			$options[] = JHTML::_('select.option', null, '-' . JText::_('JSELECT') . '-');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name, array('option.attr' => 'access_token'));
	}

	/**
	 * workingDays.
	 *
	 * @param   string  $name      The name for the field
	 * @param   string  $selected  The key that is selected
	 * @param   string  $label     Param
	 * @param   array   $desc      Param
	 * @param   string  $idtag     Param
	 *
	 * @return  string  HTML
	 */
	public static function workingDaysControl($name, $selected, $label, $desc, $idtag = null)
	{
			$data = array();

			$data[] = JHTML::_('select.option', 0, 'SUNDAY');
			$data[] = JHTML::_('select.option', 1, 'MONDAY');
			$data[] = JHTML::_('select.option', 2, 'TUESDAY');
			$data[] = JHTML::_('select.option', 3, 'WEDNESDAY');
			$data[] = JHTML::_('select.option', 4, 'THURSDAY');
			$data[] = JHTML::_('select.option', 5, 'FRIDAY');
			$data[] = JHTML::_('select.option', 6, 'SATURDAY');

			echo EHtmlSelect::checkboxListControl(
				$data,
				$name,
				null,
				$selected,
				$label,
				$desc,
				$idtag
			);
	}

	/**
	 * evergreenTypeControl.
	 *
	 * @param   string  $name      The name for the field
	 * @param   string  $selected  The key that is selected
	 * @param   string  $label     Param
	 * @param   array   $desc      Param
	 * @param   string  $idtag     Param
	 *
	 * @return  string  HTML
	 */
	public static function evergreenTypeControl($name, $selected, $label, $desc, $idtag = null)
	{
		$options = array();
		$options[] = JHTML::_('select.option', '1', JText::_('COM_AUTOTWEET_EVERGREEN_TYPE_RANDOM'));

		/*
		$options[] = JHTML::_('select.option', '2', JText::_('COM_AUTOTWEET_EVERGREEN_TYPE_RANDOM_HITS'));
		$options[] = JHTML::_('select.option', '3', JText::_('COM_AUTOTWEET_EVERGREEN_TYPE_RANDOM_DATER'));
		$options[] = JHTML::_('select.option', '4', JText::_('COM_AUTOTWEET_EVERGREEN_TYPE_RANDOM_DATEO'));
		*/

		$options[] = JHTML::_('select.option', '5', JText::_('COM_AUTOTWEET_EVERGREEN_TYPE_SEQUENCE'));

		return EHtmlSelect::customGenericListControl($options, $name, array(), $selected, $label, $desc, $idtag);
	}

	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value     Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i         The index
	 * @param   bool     $withLink  Param
	 *
	 * @return  string
	 */
	public static function processedWithIcons($value, $i, $withLink = null)
	{
		if (is_object($value))
		{
			$value = $value->published;
		}

		$img = $value ? self::REQ_ICON_YES : self::REQ_ICON_NO;

		if ($withLink === null)
		{
			$platform = F0FPlatform::getInstance();
			$input = new F0FInput;
			$withLink = $platform->authorise('core.edit.state', $input->getCmd('option', 'com_foobar'));
		}

		if (!$withLink)
		{
			return $img;
		}

		$task = $value ? 'unpublish' : 'publish';
		$alt = $value ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED');
		$action = $value ? JText::_('JLIB_HTML_UNPUBLISH_ITEM') : JText::_('JLIB_HTML_PUBLISH_ITEM');
		$href = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $action . '">' . $img . '</a>';

		return $href;
	}

	/**
	 * scope.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 * @param   string  $idTag     The id for the field
	 *
	 * @return  string  HTML
	 */
	public static function scope($selected = null, $name = 'scope', $attribs = array(), $idTag = false)
	{
		if (!$selected)
		{
			$selected = 'S';
		}

		$options = array();
		$options[] = array(
						'name' => 'COM_AUTOTWEET_SCOPE_TYPE_SITE',
						'value' => 'S'
		);
		$options[] = array(
						'name' => 'COM_AUTOTWEET_SCOPE_TYPE_USER',
						'value' => 'U'
		);

		return EHtmlSelect::btngrouplist($selected, $name, $attribs, $options, $idTag);
	}

	/**
	 * getTargettypesName.
	 *
	 * @param   string  $value  Param
	 *
	 * @return  string
	 */
	public static function getScopeName($value)
	{
		if ($value == 'S')
		{
			return '<i class="xticon xticon-globe"></i> - ' . JText::_('COM_AUTOTWEET_SCOPE_TYPE_SITE');
		}
		elseif ($value == 'U')
		{
			return '<i class="xticon xticon-user"></i> - ' . JText::_('COM_AUTOTWEET_SCOPE_TYPE_USER');
		}

		return '?';
	}

	/**
	 * scopeControl.
	 *
	 * @param   string  $selectedScope            Param
	 * @param   string  $selectedFrontendChannel  Param
	 *
	 * @return  string  HTML
	 */
	public static function scopeControl($selectedScope = null, $selectedFrontendChannel = null)
	{
		$control = '<span class="label label-info">' . self::getScopeName($selectedScope) . '</span>';

		if ($selectedScope == 'S')
		{
			$control .= ' ' .
				EHtml::label('COM_AUTOTWEET_VIEW_CHANNEL_FRONTEND_TITLE', 'COM_AUTOTWEET_VIEW_CHANNEL_FRONTEND_DESC', 'xtform[frontendchannel]') .
				EHtmlSelect::yesNo($selectedFrontendChannel, 'xtform[frontendchannel]');
		}

		return EHtml::genericControl('COM_AUTOTWEET_VIEW_CHANNEL_SCOPE_TITLE', 'COM_AUTOTWEET_VIEW_CHANNEL_SCOPE_DESC', null, $control, 'scope-control');
	}

	/**
	 * sharedWith.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $name      The name for the field
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag*
	 *
	 * @return  string  HTML
	 */
	public static function sharedWith($selected = 'EVERYONE', $name = 'sharedwith', $attribs = array() )
	{
		$options = array();

		if (AUTOTWEETNG_JOOCIAL)
		{
			$shares = array('EVERYONE', 'ALL_FRIENDS', 'FRIENDS_OF_FRIENDS', 'SELF');
		}
		else
		{
			$shares = array('EVERYONE');
		}

		// Generate html
		foreach ($shares as $share)
		{
			$options[] = JHtml::_('select.option', $share, JText::_('COM_AUTOTWEET_VIEW_CHANNEL_SHARED_' . $share), 'value', 'text');
		}

		return EHtmlSelect::customGenericList($options, $name, $attribs, $selected, $name);
	}
}
