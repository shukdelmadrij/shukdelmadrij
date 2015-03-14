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

$this->loadHelper('select');

JHtml::_('behavior.formvalidation');

?>

<div class="extly">
	<div class="extly-body">

		<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal form-validate">
			<input type="hidden" name="option" value="com_autotweet" />
			<input type="hidden" name="view" value="rules" />
			<input type="hidden" name="task" value="" />
			<?php

				echo EHtml::renderRoutingTags();

			?>
			<div class="row-fluid">

				<div class="span6">

					<fieldset class="basic">

						<legend><?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_OPTIONS'); ?></legend>

						<div class="control-group">
							<label for="name" class="control-label"><?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_NAME_TITLE'); ?> <span class="star">&#160;*</span> </label>
							<div class="controls">
								<input type="text" name="name" id="name" value="<?php echo htmlentities($this->item->name); ?>" class="required" maxlength="64" />
							</div>
						</div>

						<div class="control-group">
							<label for="plugin" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_PLUGIN_DESC'); ?>"> <?php
							echo JText::_('COM_AUTOTWEET_VIEW_PLUGIN_TITLE');
							?> <span class="star">&#160;*</span>
							</label>
							<div class="controls">
								<?php echo SelectControlHelper::plugins($this->item->plugin, 'plugin', array('class' => 'required')); ?>
							</div>
						</div>

						<div class="control-group">
							<label for="ruletype_id" class="required control-label" rel="tooltip" data-original-title="<?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_TYPE_DESC'); ?>"><?php
							echo JText::_('COM_AUTOTWEET_VIEW_TYPE_TITLE'); ?> <span class="star">&#160;*</span></label>
							<div class="controls">
								<?php echo SelectControlHelper::ruletypes($this->item->ruletype_id, 'ruletype_id', array('class' => 'required')); ?>
							</div>
						</div>

						<div class="control-group">
							<label for="channel_id" class="control-label required" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_CHANNEL_DESC');
							?>"> <?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_CHANNEL_TITLE');
							?>
							</label>
							<div class="controls">
								<?php echo SelectControlHelper::channels($this->item->channel_id, 'channel_id'); ?>
							</div>
						</div>

						<div class="control-group">
							<label for="cond" class="control-label" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_CONDITION_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_CONDITION_TITLE'); ?> </label>
							<div class="controls">
								<input type="text" name="cond" id="cond" value="<?php echo htmlentities($this->item->cond); ?>" maxlength="64" />
							</div>
						</div>

<?php

						echo EHtmlSelect::publishedControl($this->item->get('published'), 'published');

?>

						<div class="control-group">
							<label for="rule_id" class="control-label" rel="tooltip" data-original-title="<?php echo JText::_('JGLOBAL_FIELD_ID_DESC'); ?>"><?php
							echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?> </label>
							<div class="controls">
								<input type="text" name="id" id="rule_id" value="<?php echo $this->item->id; ?>" class="uneditable-input" readonly="readonly">
							</div>
						</div>

					</fieldset>

				</div>

				<div class="span6">

					<ul class="nav nav-tabs" id="ruletypeTabs">
						<li><a data-toggle="tab" href="#overrideconditions">
							<i class="xticon xticon-wrench"></i>
							<?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_OVERRIDECONDITIONS_TITLE'); ?></a>
						</li>
						<li><a data-toggle="tab" href="#advancedrmc">
							<i class="xticon xticon-pencil"></i>
							<?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_ADVANCED_RMC_TITLE'); ?></a>
						</li>
						<li><a data-toggle="tab" href="#advancedaddtext">
							<i class="xticon xticon-file-text-o"></i>
							<?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_ADVANCED_ADDTEXT_TITLE'); ?></a>
						</li>
						<li><a data-toggle="tab" href="#advancedreplace">
							<i class="xticon xticon-ellipsis-h"></i>
							<?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_ADVANCED_REPLACE_TITLE'); ?></a>
						</li>
					</ul>

					<div class="tab-content" id="fbChannelTabsContent">
						<div id="overrideconditions" class="tab-pane fade">

							<div class="control-group">
								<label class="control-label" for="autopublish" id="autopublish-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_AUTOPUBLISH_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_AUTOPUBLISH_TITLE'); ?></label>
								<div class="controls inline">
									<?php echo SelectControlHelper::autopublish($this->item->autopublish, 'autopublish'); ?>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="show_url" id="show_url-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_SHOWURL_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_SHOWURL_TITLE'); ?></label>
								<div class="controls inline">
									<?php echo SelectControlHelper::showurl($this->item->show_url, 'show_url'); ?>
								</div>
							</div>

							<?php
							if ((AUTOTWEETNG_JOOCIAL) && (EParameter::getComponentParam(CAUTOTWEETNG, 'targeting', false)))
							{
							?>
							<hr/>

							<div class="control-group">
								<label class="control-label" for="xtformtarget_id" id="target_id-lbl"><?php echo JText::_('COM_AUTOTWEET_VIEW_FBWACCOUNT_TARGETING_TITLE'); ?></label>
								<div class="controls">
									<?php echo SelectControlHelper::targets($this->item->xtform->get('target_id'), 'xtform[target_id]', null); ?>
								</div>
							</div>

							<?php
							}
							?>

						</div>

						<div id="advancedrmc" class="tab-pane fade">

							<div class="control-group">
								<label class=" required control-label" for="rmc_textpattern" id="access_token-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_ADVANCED_RMCTEXTPATTERN_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_ADVANCED_RMCTEXTPATTERN_TITLE'); ?>
								</label>
								<div class="controls">
									<textarea class="inputbox" rows="3" cols="40" id="rmc_textpattern" name="rmc_textpattern"><?php echo $this->item->rmc_textpattern; ?></textarea>
								</div>
							</div>

							<div class="alert">
								<button data-dismiss="alert" class="close" type="button">×</button>

								<p>
									<?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_ADVANCED_RMCTEXTPATTERN_DESC'); ?>
								</p>

								<h4>Examples</h4>

								<p>
									[message] / #Joomla - [fulltext,60]
								</p>
								<p>
									Extly: [introtext,45] #Joomla #AutoTweetNG [maincat]
								</p>
							</div>

						</div>

						<div id="advancedaddtext" class="tab-pane fade">

							<div class="control-group">
								<label class="control-label" for="show_static_text_id" id="show_static_text-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_SHOWSTATICTEXT_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_SHOWSTATICTEXT_TITLE'); ?></label>
								<div class="controls">
									<?php echo SelectControlHelper::showstatictext($this->item->show_static_text, 'show_static_text'); ?>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="statix_text" id="statix_text-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_STATICTEXT_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_STATICTEXT_TITLE'); ?></label>
								<div class="controls">
									<input type="text" maxlength="255" size="50" value="<?php echo htmlentities($this->item->statix_text); ?>" id="statix_text" name="statix_text">
								</div>
							</div>

						</div>

						<div id="advancedreplace" class="tab-pane fade">

							<div class="control-group">
								<label class="control-label" for="reg_ex" id="reg_ex-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_REPLACEREGEX_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_REPLACEREGEX_TITLE'); ?></label>
								<div class="controls">
									<input type="text" maxlength="4096" size="50" value="<?php echo htmlentities($this->item->reg_ex); ?>" id="reg_ex" name="reg_ex">
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="reg_replace" id="reg_replace-lbl" rel="tooltip" data-original-title="<?php
							echo JText::_('COM_AUTOTWEET_VIEW_RULE_REPLACETEXT_DESC');
							?>"><?php echo JText::_('COM_AUTOTWEET_VIEW_RULE_REPLACETEXT_TITLE'); ?></label>
								<div class="controls">
									<input type="text" maxlength="4096" size="50" value="<?php echo htmlentities($this->item->reg_replace); ?>" id="reg_replace" name="reg_replace">
								</div>
							</div>

							<div class="alert">
								<button data-dismiss="alert" class="close" type="button">×</button>
								<h4>Example 1</h4>

								<dl>
									<dt>Regular expression</dt>
									<dd>/ autotweetng/i</dd>
									<dt>Replacement text</dt>
									<dd> #AutoTweetNG</dd>
								</dl>

								<h4>Example 2</h4>

								<dl>
									<dt>Regular expression</dt>
									<dd>["/ autotweetng/i","/ jomsocial/i"]</dd>
									<dt>Replacement text</dt>
									<dd>[" #AutoTweetNG"," #JomSocial"]</dd>
								</dl>
							</div>

						</div>

					</div>

		<hr/>

		<div class="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php
			echo JText::_('COM_AUTOTWEET_VIEW_RULE_DATA_DESC');
			?>
			<br/>
			<i class="xticon xticon-thumbs-up"></i> <a href="http://www.extly.com/autotweetng-documentation-faq/282-autotweet-documentation-rules-engine.html#two-channels" target="_blank">
			A practical case: Two Channels and Two Categories</a>
		</div>

				</div>

			</div>
		</form>
	</div>
</div>
