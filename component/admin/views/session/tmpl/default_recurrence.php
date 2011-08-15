<?php defined('_JEXEC') or die('Restricted access'); ?>
<div id="recurrence">
<?php $disabled = $this->xref->count ? ' disabled="disabled"' : ''; ?>
<fieldset class="adminform">
<legend><?php echo JText::_('RECURRENCE TYPE'); ?></legend>
<?php echo $this->lists['recurrence_type']; ?>
</fieldset>

<div id="xref_recurrence_repeat_common">

<fieldset class="adminform">
<legend><?php echo JText::_('REPEAT INTERVAL'); ?></legend>
<input type="text" name="recurrence_interval" value="<?php echo $this->xref->rrules->interval; ?>" class="hasTip" title="<?php echo JText::_('REPEAT INTERVAL TIP'); ?>"/><span id="repeat_object"></span>
</fieldset>

<fieldset class="adminform">
<legend><input id="rcount" type="radio" name="rutype" value="count" <?php echo ($this->xref->rrules->until_type == 'count') ? ' checked="checked"' : ''; ?><?php echo $disabled; ?>/><?php echo JText::_('REPEAT COUNT'); ?></legend>
<input type="text" id="recurrence_repeat_count" name="recurrence_repeat_count" value="<?php echo $this->xref->rrules->count; ?>" class="hasTip" title="<?php echo JText::_('REPEAT COUNT TIP'); ?>"<?php echo $disabled; ?> />
</fieldset>

<fieldset class="adminform">
<legend><input id="runtil" type="radio" name="rutype" value="until" <?php echo ($this->xref->rrules->until_type == 'until') ? ' checked="checked"' : ''; ?><?php echo $disabled; ?>/><?php echo JText::_('REPEAT UNTIL'); ?></legend>

<table class="admintable">
<tbody>
  <tr>
    <td class="hasTip" title="<?php echo JText::_('REPEAT UNTIL TIP'); ?>">
    <?php if ($this->xref->count):?>
    	<input type="text" id="recurrence_repeat_until" name="recurrence_repeat_until" value="<?php echo $this->xref->rrules->until; ?>" disabled="disabled" />    	
    <?php else: ?>
      <?php echo JHTML::calendar($this->xref->rrules->until, 'recurrence_repeat_until', 'recurrence_repeat_until'); ?>
    <?php endif; ?>
    </td>
  </tr>
</tbody>
</table>
</fieldset>

</div>

<div id="recurrence_repeat_weekly">
<fieldset class="adminform">
<legend><?php echo JText::_('RECURRENCE WEEK BY DAY'); ?></legend>

      <input type="checkbox" id="recurrence_week_byday0" name="wweekdays[]" value="SU" <?php echo (in_array('SU', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_week_byday0"><?php echo JText::_('SUNDAY_S'); ?></label> 
      <input type="checkbox" id="recurrence_week_byday1" name="wweekdays[]" value="MO" <?php echo (in_array('MO', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_week_byday1"><?php echo JText::_('MONDAY_M'); ?></label> 
      <input type="checkbox" id="recurrence_week_byday2" name="wweekdays[]" value="TU" <?php echo (in_array('TU', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_week_byday2"><?php echo JText::_('TUESDAY_T'); ?></label> 
      <input type="checkbox" id="recurrence_week_byday3" name="wweekdays[]" value="WE" <?php echo (in_array('WE', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_week_byday3"><?php echo JText::_('WEDNESDAY_W'); ?></label> 
      <input type="checkbox" id="recurrence_week_byday4" name="wweekdays[]" value="TH" <?php echo (in_array('TH', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_week_byday4"><?php echo JText::_('THURSDAY_T'); ?></label> 
      <input type="checkbox" id="recurrence_week_byday5" name="wweekdays[]" value="FR" <?php echo (in_array('FR', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_week_byday5"><?php echo JText::_('FRIDAY_F'); ?></label> 
      <input type="checkbox" id="recurrence_week_byday6" name="wweekdays[]" value="SA" <?php echo (in_array('SA', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_week_byday6"><?php echo JText::_('SATURDAY_S'); ?></label> 

</fieldset>
</div>

<div id="recurrence_repeat_monthly">

<fieldset class="adminform">
<legend><input id="monthtypebmd" type="radio" name="monthtype" value="bymonthday" <?php echo ($this->xref->rrules->monthtype == 'bymonthdays') ? ' checked="checked"' : ''; ?>/><?php echo JText::_('RECURRENCE MONTH BY MONTHDAY'); ?></legend>

<input type="text" name="bymonthdays" value="<?php echo implode(', ', $this->xref->rrules->bydays); ?>"/>
<label for="bymonthdays"><?php echo JText::_('BY MONTH DAY COMMA LIST')?></label>
<br>
<input type="checkbox" id="reverse_bymonthday" name="reverse_bymonthday" <?php echo ($this->xref->rrules->reverse_bydays) ? ' checked="checked"' : ''; ?>/><label for="reverse_bymonthday"><?php echo JText::_('REVERSE BY MONTH DAY'); ?></label> 
</fieldset>

<fieldset class="adminform">
<legend><input id="monthtypebd" type="radio" name="monthtype" value="byday" <?php echo ($this->xref->rrules->monthtype == 'bymonthdays') ? '' : ' checked="checked"'; ?>/><?php echo JText::_('RECURRENCE MONTH BY DAY'); ?></legend>

<table class="admintable">
<tbody>
  <tr>
    <td>
      <input type="checkbox" id="recurrence_month_week0" name="mweeks[]" value="1" <?php echo (in_array('1', $this->xref->rrules->weeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_week0"><?php echo JText::_('WEEK 1'); ?></label> 
      <input type="checkbox" id="recurrence_month_week1" name="mweeks[]" value="2" <?php echo (in_array('2', $this->xref->rrules->weeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_week1"><?php echo JText::_('WEEK 2'); ?></label> 
      <input type="checkbox" id="recurrence_month_week2" name="mweeks[]" value="3" <?php echo (in_array('3', $this->xref->rrules->weeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_week2"><?php echo JText::_('WEEK 3'); ?></label> 
      <input type="checkbox" id="recurrence_month_week3" name="mweeks[]" value="4" <?php echo (in_array('4', $this->xref->rrules->weeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_week3"><?php echo JText::_('WEEK 4'); ?></label> 
      <input type="checkbox" id="recurrence_month_week4" name="mweeks[]" value="5" <?php echo (in_array('5', $this->xref->rrules->weeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_week4"><?php echo JText::_('WEEK 5'); ?></label> 
    </td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" id="recurrence_month_byday0" name="mweekdays[]" value="SU" <?php echo (in_array('SU', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_byday0"><?php echo JText::_('SUNDAY_S'); ?></label> 
      <input type="checkbox" id="recurrence_month_byday1" name="mweekdays[]" value="MO" <?php echo (in_array('MO', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_byday1"><?php echo JText::_('MONDAY_M'); ?></label> 
      <input type="checkbox" id="recurrence_month_byday2" name="mweekdays[]" value="TU" <?php echo (in_array('TU', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_byday2"><?php echo JText::_('TUESDAY_T'); ?></label> 
      <input type="checkbox" id="recurrence_month_byday3" name="mweekdays[]" value="WE" <?php echo (in_array('WE', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_byday3"><?php echo JText::_('WEDNESDAY_W'); ?></label> 
      <input type="checkbox" id="recurrence_month_byday4" name="mweekdays[]" value="TH" <?php echo (in_array('TH', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_byday4"><?php echo JText::_('THURSDAY_T'); ?></label> 
      <input type="checkbox" id="recurrence_month_byday5" name="mweekdays[]" value="FR" <?php echo (in_array('FR', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_byday5"><?php echo JText::_('FRIDAY_F'); ?></label> 
      <input type="checkbox" id="recurrence_month_byday6" name="mweekdays[]" value="SA" <?php echo (in_array('SA', $this->xref->rrules->weekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_byday6"><?php echo JText::_('SATURDAY_S'); ?></label> 
    </td>
  </tr>
  <tr>
    <td><hr/><?php echo JText::_('REVERSE BY MONTH DAY'); ?></td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" id="recurrence_month_rweek0" name="mrweeks[]" value="1" <?php echo (in_array('1', $this->xref->rrules->rweeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rweek0"><?php echo JText::_('WEEK 1'); ?></label> 
      <input type="checkbox" id="recurrence_month_rweek0" name="mrweeks[]" value="2" <?php echo (in_array('2', $this->xref->rrules->rweeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rweek1"><?php echo JText::_('WEEK 2'); ?></label> 
      <input type="checkbox" id="recurrence_month_rweek0" name="mrweeks[]" value="3" <?php echo (in_array('3', $this->xref->rrules->rweeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rweek2"><?php echo JText::_('WEEK 3'); ?></label> 
      <input type="checkbox" id="recurrence_month_rweek0" name="mrweeks[]" value="4" <?php echo (in_array('4', $this->xref->rrules->rweeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rweek3"><?php echo JText::_('WEEK 4'); ?></label> 
      <input type="checkbox" id="recurrence_month_rweek0" name="mrweeks[]" value="5" <?php echo (in_array('5', $this->xref->rrules->rweeks)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rweek4"><?php echo JText::_('WEEK 5'); ?></label> 
    </td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" id="recurrence_month_rbyday0" name="mrweekdays[]" value="SU" <?php echo (in_array('SU', $this->xref->rrules->rweekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rbyday0"><?php echo JText::_('SUNDAY_S'); ?></label> 
      <input type="checkbox" id="recurrence_month_rbyday1" name="mrweekdays[]" value="MO" <?php echo (in_array('MO', $this->xref->rrules->rweekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rbyday0"><?php echo JText::_('MONDAY_M'); ?></label> 
      <input type="checkbox" id="recurrence_month_rbyday2" name="mrweekdays[]" value="TU" <?php echo (in_array('TU', $this->xref->rrules->rweekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rbyday0"><?php echo JText::_('TUESDAY_T'); ?></label> 
      <input type="checkbox" id="recurrence_month_rbyday3" name="mrweekdays[]" value="WE" <?php echo (in_array('WE', $this->xref->rrules->rweekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rbyday0"><?php echo JText::_('WEDNESDAY_W'); ?></label> 
      <input type="checkbox" id="recurrence_month_rbyday4" name="mrweekdays[]" value="TH" <?php echo (in_array('TH', $this->xref->rrules->rweekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rbyday0"><?php echo JText::_('THURSDAY_T'); ?></label> 
      <input type="checkbox" id="recurrence_month_rbyday5" name="mrweekdays[]" value="FR" <?php echo (in_array('FR', $this->xref->rrules->rweekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rbyday0"><?php echo JText::_('FRIDAY_F'); ?></label> 
      <input type="checkbox" id="recurrence_month_rbyday6" name="mrweekdays[]" value="SA" <?php echo (in_array('SA', $this->xref->rrules->rweekdays)) ? 'checked="checked"': '';?>/><label for="recurrence_month_rbyday0"><?php echo JText::_('SATURDAY_S'); ?></label> 
    </td>
  </tr>
</tbody>
</table>

</fieldset>
</div>

<div id="recurrence_repeat_yearly">

<fieldset class="adminform">
<legend><?php echo JText::_('RECURRENCE YEAR BY YEARDAY'); ?></legend>

<input type="text" name="byyeardays" value="<?php echo implode(', ', $this->xref->rrules->bydays); ?>"/>
<label for="byyeardays"><?php echo JText::_('BY YEAR DAY COMMA LIST')?></label>
<br>
<input type="checkbox" id="reverse_byyearday" name="reverse_byyearday" <?php echo ($this->xref->rrules->reverse_bydays) ? ' checked="checked"' : ''; ?>/><label for="reverse_byyearday"><?php echo JText::_('REVERSE BY YEAR DAY'); ?></label> 

</fieldset>
</div>

</div>