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

if ($this->params->get('my_app_id'))
{
	define('MY_APP_ID', $this->params->get('my_app_id'));
}

if ($this->params->get('my_app_secret'))
{
	define('MY_APP_SECRET', $this->params->get('my_app_secret'));
}

/*

These constants are a second way to define the parameters for every channel using this app.

define('MY_APP_ID', 'YOUR APP_ID HERE');
define('MY_APP_SECRET', 'YOUR APP_SECRET HERE');
define('MY_CANVAS_URL', 'http://apps.facebook.com/your-app-here');

*/

// To show debugging information
define('DEBUG_ENABLED', false);

$facebookapp = new FacebookApp;
$ok = $facebookapp->init();
?>

        <div class="jumbotron masthead">
            <div class="container-fluid">
                <p><br/></p>
                <h1><img src="ico/isologo-autotweet-20120831-75.png"/> AutoTweet NG Connector</h1>
                <p><br/></p>
            </div>
        </div>

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">

					<?php

					$ref = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);

					if ((!empty($ref)) && (!preg_match('/^http(s?):\/\/apps.facebook.com/', $ref)))
					{
						echo '<div class="alert alert-block alert-error">
							<button data-dismiss="alert" class="close" type="button">×</button>';
						echo '<p><i class="icon-fire"></i> Please, don\'t access the app directly from your browser.</p>
							<ul><li>You must call <b>AutoTweet NG Connector</b>
							from <b>AutoTweet</b> component backend in Joomla, clicking on
							the <b>Authorization Button</b>.</li>
							</ul>';
						echo '</div>';
					}

					if (!$ok)
					{
						echo '<div class="alert alert-block alert-error">
							<button data-dismiss="alert" class="close" type="button">×</button>';
						echo '<p><i class="icon-fire"></i> Wrong parameters:</p>
							<ul><li>You must call <b>AutoTweet NG Connector</b>
							from <b>AutoTweet</b> component backend in Joomla, clicking on
							the <b>Authorization Button</b>, or</li>
							<li>You must define MY_APP_ID, and MY_APP_SECRET in the Facebook App index.php.</li>
							</ul>';
						echo '</div>';
					}
					else
					{
						if (!$facebookapp->login())
						{
							?>
							<p><i class="icon-info-sign"></i> This application connects your account to your Joomla! AutoTweet NG installation.</p>
							<p>To post status messages from Joomla! to your
								<b>Facebook Status</b> (personal profile, Facebook page,
								group or event) you must <b>add this application and grant
									extended permissions</b> for AutoTweet.</p>
							<p><br/></p>
							<?php
							// Redirect to login and authorization
							echo '<a class="btn btn-info" onclick="top.location.href =\'' . $facebookapp->login_url . '\';" href="#">Authorize!</a>';
						}
						else
						{
							// Login Ok
							try
							{
								$facebookapp->facebook->setExtendedAccessToken();
								$extended_token = $_SESSION['fb_' . $facebookapp->APP_ID . '_access_token'];

								if (!$extended_token)
								{
									echo '<div class="alert alert-block alert-error">
										<button data-dismiss="alert" class="close" type="button">×</button>';
									echo '<p><i class="icon-fire"></i>
										Error getExtendedAccessToken</p>';
									echo '</div>';
								}

								$signed_request = $facebookapp->facebook->getSignedRequest();
								$exp = $signed_request['expires'];

								$user = $facebookapp->facebook->api('/me');
								$pages = $facebookapp->facebook->api('/me/accounts');
								$groups = $facebookapp->facebook->api('/me/groups');
								$events = $facebookapp->facebook->api('/me/events');

								echo '<div class="alert alert-info">';
								echo '<h2>Congratulations!</h2>';
								echo '<p><i class="icon-check"></i> You have authorized the application and granted the permissions.</p>';
								echo '<p>Now, you can create the <b>Facebook channel</b> in
									AutoTweet component backend, and select your Profile, App,
									Page, Group or Event. <i class="icon-hand-right"></i></p>';
								echo '<p><em>Please, copy and paste
									<span class="label label-info">User-ID</span> and
									<span class="label label-info">Access Token</span>
									in the new AutoTweet\'s <b>Facebook Account</b>.</em></p>';
								echo '</div>';
								?>
								<div class="facebook-tokens">
									<ul class="nav nav-tabs" id="myTab">
										<li class="active"><a data-toggle="tab" href="#home"><i class="icon-user"></i> Profile</a></li>
										<li><a data-toggle="tab" href="#profile">
											<i class="xticon xticon-home"></i> Pages and apps</a>
										</li>
										<li><a data-toggle="tab" href="#groups">
											<i class="xticon xticon-group"></i> Groups</a>
										</li>
										<li><a data-toggle="tab" href="#events">
											<i class="xticon xticon-calendar-o"></i> Events</a>
										</li>
									</ul>
									<div class="tab-content" id="myTabContent">
										<div id="home" class="tab-pane fade in active">
											<dl class="dl-horizontal">
												<dt>User Name</dt>
												<dd><?php echo $user['name']; ?></dd>
												<dt>Access Token</dt>
												<dd><small><?php echo $extended_token; ?></small></dd>
											</dl>
											<p><em>Please, copy and paste
													<span class="label label-info">User-ID</span>
													and <span class="label label-info">Access Token</span>
													in the new AutoTweet's <b>Facebook Account</b>.</em></p>
											<p><br/><br/></p>
										</div>
										<div id="profile" class="tab-pane fade">
											<table class="table">
												<thead>
													<tr>
														<th>Name</th>
													</tr>
												</thead>
												<tbody>
													<?php
													foreach ($pages['data'] as $page)
													{
														echo '<tr><td>' . $page['name'] . '</td></tr>';
													}
													?>
												</tbody>
											</table>
										</div>
										<div id="groups" class="tab-pane fade">
											<table class="table">
												<thead>
													<tr>
														<th>Name</th>
													</tr>
												</thead>
												<tbody>
			<?php
			foreach ($groups['data'] as $group)
			{
				echo '<tr><td>' . $group['name'] . '</td></tr>';
			}
			?>
												</tbody>
											</table>
										</div>
										<div id="events" class="tab-pane fade">
											<table class="table">
												<thead>
													<tr>
														<th>Name</th>
													</tr>
												</thead>
												<tbody>
			<?php
			foreach ($events['data'] as $event)
			{
				echo '<tr><td>' . $event['name'] . '</td></tr>';
			}
			?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<?php
							}
							catch (facebookphpsdk\FacebookApiException $e)
							{
								echo '<div class="alert alert-block alert-error">
									<button data-dismiss="alert" class="close" type="button">×</button>
									<i class="icon-fire"></i> Error: ';
								echo $e;
								echo '</div>';
							}
						}
					}
					?>

                </div>
            </div>

            <p><br/><br/><br/><br/><br/><br/><br/><br/><br/></p>
            <hr class="soften">

        </div>
