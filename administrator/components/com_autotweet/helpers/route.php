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
 * RouteHelp
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class RouteHelp
{
	// Routing always by Backend-Frontend Query
	const ROUTING_MODE_COMPATIBILITY = 0;

	// Routing by Backend-Frontend Query or just JRoute on Front-end
	//  (some components may return edition page, e.g. SobiPro or EasyBlog)
	const ROUTING_MODE_PERFORMANCE = 1;

	// Routing by Site Application
	const ROUTING_MODE_EXPERIMENTAL = 2;

	// Language management
	const LANGMGMT_REMOVELANG = 1;
	const LANGMGMT_REPLACELANG = 2;
	const LANGMGMT_SEF_VAR = '&lang=';

	protected $langmgmt_enabled = 0;

	protected $langmgmt_default_language = '';

	protected $routing_mode = 0;

	// Disable URL routing when wrong URLs are returned by Joomla
	protected $urlrouting_enabled = 1;

	protected $validate_url = 1;

	protected $base_url = '';

	private static $_instance = null;

	/**
	 * RouteHelp
	 *
	 */
	protected function __construct()
	{
		$this->langmgmt_enabled = EParameter::getComponentParam(CAUTOTWEETNG, 'langmgmt_enabled', 0);
		$this->langmgmt_default_language = EParameter::getComponentParam(CAUTOTWEETNG, 'langmgmt_default_language', '');

		$this->routing_mode = EParameter::getComponentParam(CAUTOTWEETNG, 'routing_mode', 0);

		// Base url overwrite
		$this->base_url = EParameter::getComponentParam(CAUTOTWEETNG, 'base_url', '');

		// Legacy Invalid base_url initialization
		if ($this->base_url == 'http://')
		{
			$this->base_url = '';
		}

		// Base Url Check
		if (!empty($this->base_url))
		{
			$this->base_url = $this->validateUrl($this->base_url);
		}

		// Disable URL routing when wrong URLs are returned by Joomla
		$this->urlrouting_enabled = EParameter::getComponentParam(CAUTOTWEETNG, 'urlrouting_enabled', 1);

		$this->validate_url = EParameter::getComponentParam(CAUTOTWEETNG, 'validate_url', 1);
	}

	/**
	 * getInstance
	 *
	 * @return	Instance
	 */
	public static function &getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new RouteHelp;
		}

		return self::$_instance;
	}

	/**
	 * getAbsoluteUrl
	 *
	 * @param   string  $url       Param
	 * @param   string  $is_image  Param
	 *
	 * @return	string
	 */
	public function getAbsoluteUrl($url, $is_image = false)
	{
		$is_absolute_url = (JString::substr($url, 0, 4) == 'http');

		if ($is_absolute_url)
		{
			return $url;
		}
		else
		{
			if ($is_image)
			{
				return $this->routeImageUrl($url);
			}
			else
			{
				return $this->routeUrl($url);
			}
		}
	}

	/**
	 * Routes the URL.
	 * This is a substitute for the original Joomla route function JRoute::_
	 * because JRoute::_ does work from frontend only and has some special behavoir
	 * with image URLs.
	 *
	 * @param   string  $url  Param
	 *
	 * @return	String
	 */
	public function routeUrl($url)
	{
		$logger = AutotweetLogger::getInstance();

		$logger->log(JLog::INFO, 'internal url = ' . $url);

		if (!empty($url))
		{
			// Get (sef) url for frontend and backend
			if ($this->urlrouting_enabled)
			{
				$url = $this->build($url);
				$logger->log(JLog::INFO, 'routeURL: routed url = ' . $url);
			}
			else
			{
				$logger->log(JLog::WARNING, 'routeURL: url routing disabled');
			}

			// Check for language management mode and correct url language if needed
			if ($this->langmgmt_enabled)
			{
				$url = $this->correctUrlLang($url);
				$logger->log(JLog::INFO, 'routeURL: language corrected url = ' . $url);
			}

			$url = $this->createAbsoluteUrl($url);
			$url = $this->validateUrl($url);
		}

		return $url;
	}

	/**
	 * Routes the Image.
	 *
	 * @param   string  $filename  Param
	 *
	 * @return	String
	 */
	public function routeImageUrl($filename)
	{
		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'routeImageUrl filename = ' . $filename);

		$url = '';

		if (!empty($filename))
		{
			$url = implode("/", array_map("rawurlencode", explode("/", $filename)));
			$url = $this->createAbsoluteUrl($url);

			if (filter_var($url, FILTER_VALIDATE_URL) === false)
			{
				$logger->log(JLog::ERROR, 'routeURL: invalid image url = ' . $url);
				$url = null;
			}
			else
			{
				$logger->log(JLog::INFO, 'routeImageUrl: final image url = ' . $url);
			}
		}

		return $url;
	}

	/**
	 * build
	 *
	 * Route/build the URL.
	 * This is a substitute for the original Joomla route function JRoute::_
	 * because JRoute::_ does work from frontend only for SEF urls.
	 * Works also for JoomSEF and sh404sef.
	 *
	 * @param   string  $url  Param
	 *
	 * @return	object
	 */
	public function build($url)
	{
		if (strpos($url, 'index.php?') === false)
		{
			return $url;
		}

		// Multilanguage support
		$url = $this->defineMultilanguageTag($url);

		if ($this->routing_mode == self::ROUTING_MODE_COMPATIBILITY)
		{
			return $this->_frontSiteSefQuery($url);
		}
		elseif ($this->routing_mode == self::ROUTING_MODE_PERFORMANCE)
		{
			if ((JFactory::getApplication()->isAdmin()) || (defined('AUTOTWEET_CRONJOB_RUNNING')))
			{
				return $this->_frontSiteSefQuery($url);
			}
			else
			{
				return JRoute::_($url, false);
			}
		}
		elseif ($this->routing_mode == self::ROUTING_MODE_EXPERIMENTAL)
		{
			return $this->_siteApplicationSefQuery($url);
		}

		return $url;
	}

	/**
	 * _frontSiteSefQuery
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 */
	private function _frontSiteSefQuery($url)
	{
		$base_url = $this->getRoot();
		$url_as_param = urlencode(base64_encode($url));
		$callsef = $base_url . '/index.php?option=com_autotweet&view=sef&task=route&url=' . $url_as_param;

		// Get the url
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $callsef);
		curl_setopt($c, CURLOPT_HEADER, 0);
		curl_setopt($c, CURLOPT_NOBODY, 0);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($c, CURLOPT_TIMEOUT, 40);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);

		$logger = AutotweetLogger::getInstance();
		$logger->log(JLog::INFO, 'Calling SEF Router: ' . $callsef);

		$sefurl = curl_exec($c);
		$sefurl = base64_decode($sefurl);
		$result_code = curl_getinfo($c);

		$logger->log(JLog::INFO, '--> result: ' . $sefurl);

		// REDIRECT Case: Ok, one more chance
		if (((int) $result_code['http_code'] >= 300)
			&& ((int) $result_code['http_code'] < 400)
			&& (array_key_exists('redirect_url', $result_code)))
		{
			$redirect_url = $result_code['redirect_url'];
			$callsef = $redirect_url;
			curl_setopt($c, CURLOPT_URL, $callsef);

			$sefurl = curl_exec($c);
			$sefurl = base64_decode($sefurl);

			$result_code = curl_getinfo($c);

			$logger->log(JLog::INFO, 'REDIRECT Calling SEF Router: ' . $redirect_url);
			$logger->log(JLog::INFO, '--> result: ' . $sefurl);
		}

		// Error handling
		if (curl_errno($c))
		{
			$sefurl = JRoute::_($url, false);

			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::WARNING, 'Error routing SEF URL via frontend request - curl_error: ' . curl_errno($c) . ' ' . curl_error($c));
		}
		elseif (((int) $result_code['http_code'] < 200) ||
				((int) $result_code['http_code'] >= 300))
		{
			// Non-200 http_code cases
			$logger = AutotweetLogger::getInstance();
			$logger->log(JLog::WARNING, 'Error routing SEF URL via frontend request - http error: ' . $result_code['http_code'] . ' - callurl = ' . $url . ' - return url = ' . $sefurl);
			$sefurl = JRoute::_($url, false);
		}
		else
		{
			// In backend we need to remove some parts from the url
			$sefurl = str_replace('/components/com_autotweet/', '/', $sefurl);
		}

		// Something odd has happened
		if (empty($sefurl))
		{
			$logger->log(JLog::WARNING, 'Error routing SEF URL via frontend request - http error: ' . $result_code['http_code'], $result_code);
		}

		curl_close($c);

		return $sefurl;
	}

	/**
	 * _siteApplicationSefQuery
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 */
	private function _siteApplicationSefQuery($url)
	{
		// In the back end we need to set the application to the site app instead
		JFactory::$application = JApplication::getInstance('site');
		JFactory::$application->initialise();

		$sefurl = JRoute::_($url, false);

		// Set the appilcation back to the administartor app
		JFactory::$application = JApplication::getInstance('administrator');

		return $sefurl;
	}

	/**
	 * defineMultilanguageTag
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 */
	protected function defineMultilanguageTag($url)
	{
		$languages = JLanguageHelper::getLanguages('lang_code');

		// Multilanguage site ?
		if (count($languages) > 1)
		{
			$uri = JUri::getInstance();

			if (($uri->parse($url)) && (!$uri->hasVar('lang')))
			{
				$logger = AutotweetLogger::getInstance();
				$logger->log(JLog::INFO, 'defineMultilanguageTag: ' . $uri->toString());

				$tag = JFactory::getLanguage()->getTag();
				$langCode = $languages[$tag]->sef;
				$uri->setVar('lang', $langCode);
				$url = $uri->toString();
			}
		}

		return $url;
	}

	/**
	 * Helps with the Joomla url hell and creates corect url savely for frontend, backend and images.
	 *
	 * @param   string  $site_url  Param
	 *
	 * @return	string
	 */
	protected function createAbsoluteUrl($site_url)
	{
		// Just in case
		$site_url = str_replace('/administrator', '', $site_url);

		if ($this->hasPath($site_url))
		{
			$baseurl = $this->getHost();
			$url = $baseurl . $site_url;
		}
		else
		{
			// Sometimes different value for backend and frontend post (one with slash)
			if (JString::substr($site_url, 0, 1) != '/')
			{
				// Remove slash at the beginning
				$site_url = '/' . $site_url;
			}

			$baseurl = $this->getRoot();
			$url = $baseurl . $site_url;
		}

		return $url;
	}

	/**
	 * correctUrlLang.
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 */
	protected function correctUrlLang($url)
	{
		$logger = AutotweetLogger::getInstance();

		if (empty($this->langmgmt_default_language))
		{
			$logger->log(JLog::WARNING, 'correctUrlLang: default language not set in AutoTweet parameters.');

			return $url;
		}

		$langSefValue = $this->getLanguageSefValue($this->langmgmt_default_language);
		$langSefValues = $this->getLanguageSefValues();

		// Lang Url variable defined
		if (JString::strpos($url, self::LANGMGMT_SEF_VAR) !== false)
		{
			// Case 1: check for lang tag in non SEF url - http://blabla.com/index.php?option=com_content&view=article&id=999&Itemid=42&lang=en
			if (self::LANGMGMT_REPLACELANG == $this->langmgmt_enabled)
			{
				// Replace language tag with default language
				$replace = self::LANGMGMT_SEF_VAR . $langSefValue;
			}
			else
			{
				// Remove language from URL
				$replace = '';
			}

			foreach ($langSefValues as $sefValue)
			{
				$search = self::LANGMGMT_SEF_VAR . $sefValue;
				$r_count = 0;
				$tmp_url = str_ireplace($search, $replace, $url, $r_count);

				if (0 < (int) $r_count)
				{
					$url = $tmp_url;
					break;
				}
			}

			if ((int) $r_count != 1)
			{
				$logger->log(JLog::WARNING, 'correctUrlLang: wrong language code found in URL.');
			}
		}
		else
		{
			// Case 2: check for lang tag in SEF url - http://blabla.com/en/extensions-for-joomla
			if (self::LANGMGMT_REPLACELANG == $this->langmgmt_enabled)
			{
				// Replace language tag with default language
				$replace = '/' . $langSefValue . '/';
			}
			else
			{
				// Remove language from URL
				$replace = '/';
			}

			foreach ($langSefValues as $sefValue)
			{
				$search = '/' . $sefValue . '/';
				$r_count = 0;
				$tmp_url = str_ireplace($search, $replace, $url, $r_count);

				if ((int) $r_count > 0)
				{
					$url = $tmp_url;
					break;
				}
			}

			if ((int) $r_count > 1)
			{
				$logger->log(JLog::WARNING, 'correctUrlLang: multiple language codes found in URL.');
			}
		}

		return $url;
	}

	/**
	 * getLanguageSefValue.
	 *
	 * @param   string  $lang_code  Param
	 *
	 * @return	string
	 */
	protected function getLanguageSefValue($lang_code)
	{
		$languages = JLanguageHelper::getLanguages('lang_code');

		return $languages[$lang_code]->sef;
	}

	/**
	 * getLanguageSefValues.
	 *
	 * @return	array
	 */
	protected function getLanguageSefValues()
	{
		$languages = JLanguageHelper::getLanguages('lang_code');
		$tags = array();

		foreach ($languages as $language)
		{
			$tags[] = $language->sef;
		}

		return $tags;
	}

	/**
	 * getRoot.
	 *
	 * @return	string
	 */
	public function getRoot()
	{
		if (!empty($this->base_url))
		{
			$baseurl = $this->base_url;
		}
		else
		{
			try
			{
				$baseurl = JUri::root();
			}
			catch (Exception $e)
			{
				$baseurl = 'http://undefined-domain.com';
			}
		}

		// Correct base url (when installed in subfolder...)
		if (JString::substr($baseurl, -1) == '/')
		{
			$baseurl = JString::substr($baseurl, 0, JString::strlen($baseurl) - 1);
		}

		// In backend we need to remove administrator from URL as it is added even though we set the application to the site app
		// $baseurl = str_replace('/administrator', '', $baseurl);

		// Forced front-end SSL
		$jconfig = JFactory::getConfig();

		if (EXTLY_J3)
		{
			$force_ssl = $jconfig->get('force_ssl');
		}
		else
		{
			$force_ssl = $jconfig->getValue('config.force_ssl');
		}

		if (($force_ssl == 2) && (strpos($baseurl, 'http:') === 0))
		{
			$baseurl = str_replace('http:', 'https:', $baseurl);
		}

		return $baseurl;
	}

	/**
	 * getHost.
	 *
	 * @return	string
	 */
	protected function getHost()
	{
		$baseurl = $this->getRoot();
		$uri = JUri::getInstance();

		if ($uri->parse($baseurl))
		{
			$host = $uri->toString(
					array(
							'scheme',
							'host',
							'port'
				)
			);

			return $host;
		}

		return null;
	}

	/**
	 * getPath.
	 *
	 * @return	string
	 */
	protected function getPath()
	{
		$baseurl = $this->getRoot();
		$uri = JUri::getInstance();

		if ($uri->parse($baseurl))
		{
			$path = $uri->toString(
					array(
							'path'
				)
			);

			return $path;
		}

		return null;
	}

	/**
	 * hasPath.
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 */
	protected function hasPath($url)
	{
		$path = $this->getPath();
		$l = strlen($path);

		return (($l > 0) && (JString::substr($url, 0, $l) == $path));
	}

	/**
	 * validateUrl.
	 *
	 * @param   string  $url  Param
	 *
	 * @return	string
	 */
	public function validateUrl($url)
	{
		if ($this->validate_url)
		{
			$logger = AutotweetLogger::getInstance();

			if (filter_var($url, FILTER_VALIDATE_URL) === false)
			{
				// Second chance -  gruber / Liberal Regex Pattern for Web URLs
				// https://gist.github.com/gruber/8891611 https://gist.github.com/gruber/249502
				if (!preg_match('#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#i', $url))
				{
					$logger->log(JLog::ERROR, 'ValidateUrl: invalid url = ' . $url);
					$url = null;

					$app = JFactory::getApplication();
					$app->enqueueMessage(JText::_('COM_AUTOTWEET_COMPARAM_VALIDATE_URL_ERROR'), 'error');
				}
				else
				{
					$logger->log(JLog::INFO, 'ValidateUrl: OK - Second chance - url = ' . $url);
				}
			}
			else
			{
				$logger->log(JLog::INFO, 'ValidateUrl: OK url = ' . $url);
			}
		}

		return $url;
	}
}
