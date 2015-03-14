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
<h3>
	<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSTEMINFO_TITLE'); ?>
</h3>
<?php

if (is_array($this->sysinfo))
{
	?>

<table class="table table-bordered table-condensed">
	<tbody>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_PHP] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_PHP_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_PHP');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_PHP] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_MYSQL] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_MYSQL_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_MYSQL');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_MYSQL] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_CURL] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_CURL_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_CURL');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_CURL] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_SSL] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_SSL_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_SSL');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_SSL] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_TIMESTAMP] < 2 ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_TIMESTAMP_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_TIMESTAMP');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_TIMESTAMP] < 2 ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL . ' (' . $this->sysinfo[UpdateNgHelper::SYSINFO_TIMESTAMP] . ')';
			?></td>
		</tr>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_JSON] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_JSON_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_JSON');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_JSON] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_HMAC] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_HMAC_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_HMAC');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_HMAC] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>

		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_NPECLOAUTH] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_NPECLOAUTH_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_NPECLOAUTH');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_NPECLOAUTH] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>

		<?php
			if (defined('AUTOTWEETNG_JOOCIAL'))
			{
		?>
		<tr class="<?php echo $this->sysinfo[UpdateNgHelper::SYSINFO_TIDY] ? 'info' : 'error'; ?>">
			<td>
				<a rel="tooltip" data-toggle="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_TIDY_DESC'); ?>">
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_ABOUT_SYSINFO_TIDY');
			?>
				</a>
			</td>
			<td><?php
			echo $this->sysinfo[UpdateNgHelper::SYSINFO_TIDY] ? UpdateNgHelper::SYSINFO_OK : UpdateNgHelper::SYSINFO_FAIL;
			?></td>
		</tr>
		<?php
			}
		?>

	</tbody>
</table>

<?php
}
else
{
	echo '<p class="text-error">' . $this->sysinfo . '</p>';
}
