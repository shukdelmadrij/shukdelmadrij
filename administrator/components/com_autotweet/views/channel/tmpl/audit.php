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
				<div class="row-fluid">
					<div class="span12">
						<hr />

							<div class="well">
								<div class="alert <?php echo $this->get('alert_style'); ?>">
								<?php echo JText::_('COM_AUTOTWEET_AUDIT_INFORMATION'); ?>
							</div>

							<dl class="dl-horizontal">
								<dt>
									<?php
									echo JText::_('COM_AUTOTWEET_CREATED_DATE');
									?>
								</dt>
								<dd>
									<?php
									echo $this->item->get('created');
									?>

									<?php
									$created = $this->item->get('created_by');

									if ($created)
									{
										echo JFactory::getUser($created)->get('username');
									}
									else
									{
										echo '-';
									}
									?>
								</dd>

								<dt>
									<?php
									echo JText::_('COM_AUTOTWEET_MODIFIED_DATE');
									?>
								</dt>
								<dd>
									<?php
									$modified = $this->item->get('modified');

									if ((int) $modified)
									{
										echo $modified;
									}
									?>

									<?php
									$modified_by = $this->item->get('modified_by');

									if ($modified_by)
									{
										echo JFactory::getUser($modified_by)->get('username');
									}
									else
									{
										echo '-';
									}
									?>
								</dd>

								<dt>
									<?php
									echo JText::_('COM_AUTOTWEET_RESULT_MESSAGE');
					?>
								</dt>
								<dd>
					<?php
						echo $alert_message;
					?>
								</dd>
							</dl>

						</div>

					</div>
				</div>
