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
<div id="contentcreation" class="tab-pane fade">
<?php

echo '<a id="#texthandling"></a>';
include_once 'feed_texthandling.php';

echo '<a id="#links"></a>';
include_once 'feed_links.php';

echo '<a id="#images"></a>';
include_once 'feed_images.php';

echo '<a id="#enclosures"></a>';
include_once 'feed_enclosures.php';

echo '<a id="#languages"></a>';
include_once 'feed_languages.php';

?>
</div>
