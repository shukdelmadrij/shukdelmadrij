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
 * Copyright 2011 Google Inc.
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

/**
 * Class to hold information about an authenticated login.
 *
 * @author Brian Eaton <beaton@google.com>
 */
class Google_LoginTicket {
  const USER_ATTR = "id";

  // Information from id token envelope.
  private $envelope;

  // Information from id token payload.
  private $payload;

  /**
   * Creates a user based on the supplied token.
   *
   * @param string $envelope Header from a verified authentication token.
   * @param string $payload Information from a verified authentication token.
   */
  public function __construct($envelope, $payload) {
    $this->envelope = $envelope;
    $this->payload = $payload;
  }

  /**
   * Returns the numeric identifier for the user.
   * @throws Google_AuthException
   * @return
   */
  public function getUserId() {
    if (array_key_exists(self::USER_ATTR, $this->payload)) {
      return $this->payload[self::USER_ATTR];
    }
    throw new Google_AuthException("No user_id in token");
  }

  /**
   * Returns attributes from the login ticket.  This can contain
   * various information about the user session.
   * @return array
   */
  public function getAttributes() {
    return array("envelope" => $this->envelope, "payload" => $this->payload);
  }
}
