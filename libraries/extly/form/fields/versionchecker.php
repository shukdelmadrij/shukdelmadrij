<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Form Field class.
 * Provides donater markup to be used in form layouts.
 *
 * @package     Extly.Library
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldVersionChecker extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'VersionChecker';

	/**
	 * Method to get the field input markup for a donater.
	 * The donater does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		return ' ';
	}

	/**
	 * Method to get the field label markup for a donater.
	 * Use the label text or name from the XML element as the donater or
	 * Use a hr="true" to automatically generate plain hr markup
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
		$xml = $this->element->getAttribute('xml');

		if ($xml)
		{
			$xml = JApplicationHelper::parseXMLInstallFile(JPATH_SITE . '/' . $xml);

			if ($xml && isset($xml['version']))
			{
				$version = $xml['version'];
			}
		}

		$base_url = JUri::root();

		$html = '<div class="panel" style="height: auto; clear: both; margin-bottom: 8px !important;">
		<div style="height: auto; padding: 2px 5px;">

			<a title="Extly.com - Joomla Extensions" target="_blank" href="http://www.extly.com">
				<img width="60" height="80" border="0" alt="" style="float: left; margin-right: 10px;" src="' . $base_url . 'plugins/community/jswatermark/assets/extly-logo.png">
			</a>

			<h4 style="margin: 0px;">
				<a title="Extly.com - Joomla Extensions" target="_blank" href="http://www.extly.com">Extly.com - Joomla Extensions</a>
			</h4>
			<p>Your current installed version is: ' . $version . '</p>

			<p style="text-align: center;">
				<a target="_blank" href="http://www.extly.com/jomsocial-extensions/watermarks-for-jomsocial.html#changelog"
					style="background-color: #006DCC; background-image: linear-gradient(to bottom, #0088CC, #0044CC); background-repeat: repeat-x; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); color: #FFFFFF; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); border-image: none; border-radius: 4px 4px 4px 4px; border-style: solid; border-width: 1px; box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05); cursor: pointer; display: inline-block; font-size: 14px; line-height: 20px; margin-bottom: 0; padding: 4px 12px; text-align: center; vertical-align: middle; font-weight: normal;">Check the latest version!</a>
			</p>

			<div style="clear: both;"></div>
		</div>
	</div>';

		return $html;
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   11.1
	 */
	protected function getTitle()
	{
		return $this->getLabel();
	}
}
