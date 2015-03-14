<?php

/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - AutoTweetNG posts content to social channels (Twitter, Facebook, LinkedIn, etc).
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

defined('AUTOTWEET_API') || define('AUTOTWEET_API', true);

defined('CAUTOTWEETNG') || define('CAUTOTWEETNG', 'com_autotweet');
defined('CAUTOTWEETNG_VERSION') || define('CAUTOTWEETNG_VERSION', '7.5.0');

// Load F0F
if (!defined('F0F_INCLUDED'))
{
	include_once JPATH_LIBRARIES . '/f0f/include.php';
}

if (!defined('F0F_INCLUDED'))
{
	JError::raiseError('500', 'Your AutoTweetNG installation is broken; please re-install.
			Alternatively, extract the installation archive and copy the f0f directory inside your site\'s libraries directory.');
}

defined('JPATH_AUTOTWEET') || define('JPATH_AUTOTWEET', JPATH_ADMINISTRATOR . '/components/com_autotweet');
defined('JPATH_AUTOTWEET_HELPERS') || define('JPATH_AUTOTWEET_HELPERS', JPATH_AUTOTWEET . '/helpers');

JLoader::import('extly.extlyframework');

JLoader::register('AclPermsHelper', JPATH_AUTOTWEET_HELPERS . '/acl.php');
JLoader::register('AdvancedattrsHelper', JPATH_AUTOTWEET_HELPERS . '/advancedattrs.php');
JLoader::register('AutotweetBaseHelper', JPATH_AUTOTWEET_HELPERS . '/autotweetbasehelper.php');
JLoader::register('AutotweetBitlyService', JPATH_AUTOTWEET_HELPERS . '/urlshortservices/autotweetbitly.php');
JLoader::register('AutotweetDefaultView', JPATH_AUTOTWEET_HELPERS . '/defaultview.php');
JLoader::register('AutotweetGooglService', JPATH_AUTOTWEET_HELPERS . '/urlshortservices/autotweetgoogl.php');
JLoader::register('AutotweetIsgdService', JPATH_AUTOTWEET_HELPERS . '/urlshortservices/autotweetisgd.php');
JLoader::register('AutotweetLogger', JPATH_AUTOTWEET_HELPERS . '/logger.php');
JLoader::register('AutotweetPostHelper', JPATH_AUTOTWEET_HELPERS . '/autotweetposthelper.php');
JLoader::register('AutotweetRenderBack25', JPATH_AUTOTWEET_HELPERS . '/render.php');
JLoader::register('AutotweetRenderBack3', JPATH_AUTOTWEET_HELPERS . '/render.php');
JLoader::register('AutotweetRenderFront25', JPATH_AUTOTWEET_HELPERS . '/render.php');
JLoader::register('AutotweetRenderFront3', JPATH_AUTOTWEET_HELPERS . '/render.php');
JLoader::register('AutotweetShortservice', JPATH_AUTOTWEET_HELPERS . '/urlshortservices/autotweetshortservice.php');
JLoader::register('AutotweetTinyurlcomService', JPATH_AUTOTWEET_HELPERS . '/urlshortservices/autotweettinyurlcom.php');
JLoader::register('AutotweetURLShortserviceFactory', JPATH_AUTOTWEET_HELPERS . '/urlshortservices/autotweeturlshortservicefactory.php');
JLoader::register('AutotweetYourlsService', JPATH_AUTOTWEET_HELPERS . '/urlshortservices/autotweetyourls.php');
JLoader::register('ChannelFactory', JPATH_AUTOTWEET_HELPERS . '/channels/channelfactory.php');
JLoader::register('ChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/channel.php');
JLoader::register('CronjobHelper', JPATH_AUTOTWEET_HELPERS . '/cronjob.php');
JLoader::register('FacebookApp', JPATH_AUTOTWEET_HELPERS . '/facebookapp.php');
JLoader::register('FacebookBaseChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/facebookbase.php');
JLoader::register('FacebookChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/facebook.php');
JLoader::register('FacebookEventChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/facebookevent.php');
JLoader::register('FacebookLinkChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/facebooklink.php');
JLoader::register('FacebookPhotoChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/facebookphoto.php');
JLoader::register('FacebookVideoChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/facebookvideo.php');
JLoader::register('FbAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/fbapp.php');
JLoader::register('FeedLoaderHelper', JPATH_AUTOTWEET_HELPERS . '/feedloader.php');
JLoader::register('FeedImporterHelper', JPATH_AUTOTWEET_HELPERS . '/feedimporter.php');
JLoader::register('FeedTextHelper', JPATH_AUTOTWEET_HELPERS . '/feedtext.php');
JLoader::register('GplusAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/gplusapp.php');
JLoader::register('GplusChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/gplus.php');
JLoader::register('GridHelper', JPATH_AUTOTWEET_HELPERS . '/grid.php');
JLoader::register('ImageHelper', JPATH_AUTOTWEET_HELPERS . '/image.php');
JLoader::register('LiAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/liapp.php');
JLoader::register('LinkedinBaseChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/linkedinbase.php');
JLoader::register('LinkedinChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/linkedin.php');
JLoader::register('LinkedinGroupChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/linkedingroup.php');
JLoader::register('LinkedinCompanyChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/linkedincompany.php');
JLoader::register('MailChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/mail.php');
JLoader::register('PlgAutotweetBase', JPATH_AUTOTWEET_HELPERS . '/autotweetbase.php');
JLoader::register('PostHelper', JPATH_AUTOTWEET_HELPERS . '/post.php');
JLoader::register('RequestHelp', JPATH_AUTOTWEET_HELPERS . '/request.php');
JLoader::register('RouteHelp', JPATH_AUTOTWEET_HELPERS . '/route.php');
JLoader::register('RuleEngineHelper', JPATH_AUTOTWEET_HELPERS . '/ruleengine.php');
JLoader::register('SelectControlHelper', JPATH_AUTOTWEET_HELPERS . '/select.php');
JLoader::register('SharingHelper', JPATH_AUTOTWEET_HELPERS . '/sharing.php');
JLoader::register('ShorturlHelper', JPATH_AUTOTWEET_HELPERS . '/shorturl.php');
JLoader::register('TextUtil', JPATH_AUTOTWEET_HELPERS . '/text.php');
JLoader::register('TwAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/twapp.php');
JLoader::register('VkAppHelper', JPATH_AUTOTWEET_HELPERS . '/channels/vkapp.php');
JLoader::register('TwitterChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/twitter.php');
JLoader::register('VirtualManager', JPATH_AUTOTWEET_HELPERS . '/virtualmanager.php');
JLoader::register('VkChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/vk.php');
JLoader::register('VkGroupChannelHelper', JPATH_AUTOTWEET_HELPERS . '/channels/vkgroup.php');
JLoader::register('UpdateNgHelper', JPATH_AUTOTWEET_HELPERS . '/update.php');
JLoader::register('VersionHelper', JPATH_AUTOTWEET_HELPERS . '/version.php');

JLoader::load('VersionHelper');

if (!defined('AUTOTWEET_LOGGER'))
{
	define('AUTOTWEET_LOGGER', true);
	AutotweetLogger::getInstance();
}

/**
 *
 * HOW TO: http://www.extly.com/autotweetng-documentation-faq/605-how-to-use-autotweetng-api.html
 *
 * AutotweetAPI::insertRequest
 *
 * 		Description: Main method to insert new requests.
 *
 * AutotweetAPI::sampleJoocialIntegration
 *
 * 		Description: sample external call to save Joocial advanced attributes
 *
 * AutotweetAPI::sampleAutoTweetNGIntegration
 *
 * 		Description: sample external call to save AutoTweetNG content request
 *
 */

/**
 * AutotweetAPI
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
abstract class AutotweetAPI
{
	/**
	 * __construct
	 *
	 * @since	1.5
	 */
	private function __construct()
	{
		// Nothing to do
	}

	/**
	 * insertRequest
	 *
	 * @param   int     $ref_id           Param
	 * @param   string  $plugin           Param
	 * @param   date    $publish_up       Param
	 * @param   string  $description      Param
	 * @param   int     $typeinfo         Param
	 * @param   string  $url              Param
	 * @param   string  $image_url        Param
	 * @param   json    &$native_object   Param
	 * @param   object  &$advanced_attrs  Param
	 * @param   object  &$params          Param
	 *
	 * @return	mixed (bool or request Id)
	 */
	public static function insertRequest($ref_id, $plugin, $publish_up, $description, $typeinfo = 0, $url = '', $image_url = '', &$native_object = null, &$advanced_attrs = null, &$params = null)
	{
		if ($publish_up == 0)
		{
			$publish_up = JFactory::getDate()->toSql();
		}

		if ($advanced_attrs)
		{
			if ($advanced_attrs->postthis == PlgAutotweetBase::POSTTHIS_NO)
			{
				// Post this or not
				return null;
			}

			if (($image = $advanced_attrs->image) && (!empty($image)))
			{
				// This image
				if ($image == 'none')
				{
					$image_url = null;
				}
				else
				{
					$image_url = $image;
				}
			}

			if (($agenda = $advanced_attrs->agenda) && (count($agenda) > 0))
			{
				// The first date, it's the next date
				$publish_up = AdvancedattrsHelper::getNextAgendaDate($agenda);
			}
		}

		$result = RequestHelp::insertRequest($ref_id, $plugin, $publish_up, $description, $typeinfo, $url, $image_url, $native_object, $advanced_attrs, $params);

		return $result;
	}

	/**
	 * sampleJoocialIntegration
	 *
	 * @param   int  $productId  Param
	 *
	 * Example of how to save Joocial Advanced attributes
	 * Copy-paste into your extension, and customize freely
	 *
	 * @return	void
	 */
	private static function sampleJoocialIntegration($productId)
	{
		if (!defined('AUTOTWEET_API'))
		{
			include_once JPATH_ADMINISTRATOR . '/components/com_autotweet/api/autotweetapi.php';
		}

		// Joocial - Saving Advanced Attrs
		$input = new F0FInput;
		$autotweet_advanced = $input->get('autotweet_advanced_attrs', null, 'string');

		if ($autotweet_advanced)
		{
			$advanced_attrs = AdvancedattrsHelper::retrieveAdvancedAttrs($autotweet_advanced);

			if ($advanced_attrs)
			{
				AdvancedattrsHelper::saveAdvancedAttrs($advanced_attrs, $productId);
			}
		}
	}

	/**
	 * sampleAutoTweetNGIntegration
	 *
	 * @param   int    $productId  Param
	 * @param   array  &$data      Param
	 *
	 * Example of how to save AutoTweetNG Request
	 * Copy-paste into your extension, and customize freely
	 *
	 * @return	void
	 */
	private static function sampleAutoTweetNGIntegration($productId, &$data)
	{
		// If product is not published, nothing else to do
		if (!$data['published'])
		{
			return;
		}

		$typeinfo = 99;
		$native_object = json_encode($data);

		// Product Url
		$viewProductUrl = JRoute::_(EshopRoute::getProductRoute($productId, EshopHelper::getProductCategory($productId)));

		// Image Url
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select('image')
			->from('#__eshop_productimages')
			->where('product_id = ' . intval($productId))
			->order('ordering');
		$db->setQuery($query);

		$product_image = $db->loadResult();

		$image_url = null;

		if ($product_image && JFile::exists(JPATH_ROOT . '/media/com_eshop/products/' . $product_image))
		{
			$image_url = 'media/com_eshop/products/' . $product_image;
		}

		$langmgmt_default_language = EParameter::getComponentParam(CAUTOTWEETNG, 'langmgmt_default_language');

		if ($langmgmt_default_language)
		{
			$key = 'product_name_' . $langmgmt_default_language;

			if (array_key_exists($key, $data))
			{
				$title = $data[$key];
			}

			$key = 'product_desc_' . $langmgmt_default_language;

			if (array_key_exists($key, $data))
			{
				$description = TextUtil::cleanText($data[$key]);
			}
		}

		if (empty($title))
		{
			$title = $data['product_name'];
		}

		if (empty($description))
		{
			$description = TextUtil::cleanText($data['product_desc']);
		}

		if (empty($title))
		{
			return;
		}

		// Saving Advanced Attributes
		$autotweet_advanced = $data['autotweet_advanced_attrs'];
		$advanced_attrs = AdvancedattrsHelper::retrieveAdvancedAttrs($autotweet_advanced);
		AdvancedattrsHelper::saveAdvancedAttrs($advanced_attrs, $productId);

		// AutotweetAPI
		$id = self::insertRequest(
				$productId,
				'autotweetpost',
				JFactory::getDate()->toSql(),
				$title,
				$typeinfo,
				$viewProductUrl,
				$image_url,
				$native_object,
				$advanced_attrs
		);

		// Adding more information to the request

		$requestsModel = F0FModel::getTmpInstance('Requests', 'AutoTweetModel');

		$request = $requestsModel->getItem($id);
		$request = (array) $request;

		$request['xtform']->set('title', $title);
		$request['xtform']->set('article_text', $description);
		$request['xtform']->set('author', JFactory::getUser()->username);

		$requestsModel->save($request);
	}
}
