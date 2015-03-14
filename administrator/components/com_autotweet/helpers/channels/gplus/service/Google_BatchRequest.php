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
 * Copyright 2012 Google Inc.
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
 * @author Chirag Shah <chirags@google.com>
 */
class Google_BatchRequest {
  /** @var string Multipart Boundary. */
  private $boundary;

  /** @var array service requests to be executed. */
  private $requests = array();

  public function __construct($boundary = false) {
    $boundary = (false == $boundary) ? mt_rand() : $boundary;
    $this->boundary = str_replace('"', '', $boundary);
  }

  public function add(Google_HttpRequest $request, $key = false) {
    if (false == $key) {
      $key = mt_rand();
    }

    $this->requests[$key] = $request;
  }

  public function execute() {
    $body = '';

    /** @var Google_HttpRequest $req */
    foreach($this->requests as $key => $req) {
      $body .= "--{$this->boundary}\n";
      $body .= $req->toBatchString($key) . "\n";
    }

    $body = rtrim($body);
    $body .= "\n--{$this->boundary}--";

    global $apiConfig;
    $url = $apiConfig['basePath'] . '/batch';
    $httpRequest = new Google_HttpRequest($url, 'POST');
    $httpRequest->setRequestHeaders(array(
        'Content-Type' => 'multipart/mixed; boundary=' . $this->boundary));

    $httpRequest->setPostBody($body);
    $response = Google_Client::$io->makeRequest($httpRequest);

    $response = $this->parseResponse($response);
    return $response;
  }

  public function parseResponse(Google_HttpRequest $response) {
    $contentType = $response->getResponseHeader('content-type');
    $contentType = explode(';', $contentType);
    $boundary = false;
    foreach($contentType as $part) {
      $part = (explode('=', $part, 2));
      if (isset($part[0]) && 'boundary' == trim($part[0])) {
        $boundary = $part[1];
      }
    }

    $body = $response->getResponseBody();
    if ($body) {
      $body = str_replace("--$boundary--", "--$boundary", $body);
      $parts = explode("--$boundary", $body);
      $responses = array();

      foreach($parts as $part) {
        $part = trim($part);
        if (!empty($part)) {
          list($metaHeaders, $part) = explode("\r\n\r\n", $part, 2);
          $metaHeaders = Google_CurlIO::parseResponseHeaders($metaHeaders);

          $status = substr($part, 0, strpos($part, "\n"));
          $status = explode(" ", $status);
          $status = $status[1];

          list($partHeaders, $partBody) = Google_CurlIO::parseHttpResponse($part, false);
          $response = new Google_HttpRequest("");
          $response->setResponseHttpCode($status);
          $response->setResponseHeaders($partHeaders);
          $response->setResponseBody($partBody);
          $response = Google_REST::decodeHttpResponse($response);

          // Need content id.
          $responses[$metaHeaders['content-id']] = $response;
        }
      }

      return $responses;
    }

    return null;
  }
}