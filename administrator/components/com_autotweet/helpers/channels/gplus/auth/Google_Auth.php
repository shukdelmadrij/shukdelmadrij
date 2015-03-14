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

namespace google_api;

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * google-api-php-client
 * Google API PHP Client 0.6.7
 * https://code.google.com/p/google-api-php-client/
 */

/*
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once "Google_AuthNone.php";
require_once "Google_OAuth2.php";

/**
 * Abstract class for the Authentication in the API client
 * @author Chris Chabot <chabotc@google.com>
 *
 */
abstract class Google_Auth {
  abstract public function authenticate($service);
  abstract public function sign(Google_HttpRequest $request);
  abstract public function createAuthUrl($scope);

  abstract public function getAccessToken();
  abstract public function setAccessToken($accessToken);
  abstract public function setDeveloperKey($developerKey);
  abstract public function refreshToken($refreshToken);
  abstract public function revokeToken();
}
