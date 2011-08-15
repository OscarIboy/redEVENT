<?php
/**
 * @version 1.0 $Id$
 * @package Joomla
 * @subpackage redEVENT
 * @copyright redEVENT (C) 2008 redCOMPONENT.com / EventList (C) 2005 - 2008 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * redEVENT is based on EventList made by Christoph Lukes from schlu.net
 * redEVENT can be downloaded from www.redcomponent.com
 * redEVENT is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * redEVENT is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with redEVENT; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.calendar');
?>
<table class="fieldstable">
	<tbody>
		<tr>
			<td class="key">
				<label for="a_id"><?php echo JText::_( 'VENUE' ).':'; ?></label>
			</td>
			<td>
				<input type="text" id="a_name" value="<?php echo $this->row->venue; ?>" disabled="disabled" />
				<div class='re_buttons floattext'>
					<a class="re_venue_select vmodal" title="<?php echo JText::_('SELECT'); ?>" 
					   href="<?php echo JRoute::_('index.php?view=editevent&layout=selectvenue&tmpl=component'); ?>"
					   rel="{handler: 'iframe', size: {x: 650, y: 375}}">
					   	<span><?php echo JText::_('SELECT')?></span>
					</a> 
					<input class="inputbox required" type="hidden" id="a_id" name="venueid" value="<?php echo $this->row->venueid; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('COM_REDEVENT_SESSION_TITLE_TIP'); ?>"><label
				for="session_title"><?php echo JText::_('COM_REDEVENT_SESSION_TITLE_LABEL') .': '; ?></label></td>
			<td><input class="inputbox" type="text" id="session_title" name="session_title" value="<?php echo $this->row->session_title; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('EDIT XREF START DATE TIP'); ?>"><label
				for="dates"><?php echo JText::_('DATE') .': '; ?></label></td>
			<td><?php echo $this->calendar($this->row->dates, 'dates', 'dates', '%Y-%m-%d', 'updateend', 'class="inputbox validate-startdate required"'); ?>
			</td>
		</tr>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('EDIT XREF START TIME TIP'); ?>"><label
				for="times"><?php echo JText::_('TIME') .': '; ?></label></td>
			<td><input type="text" size="8" maxlength="8" name="times" id="times"
				value="<?php echo $this->row->times; ?>" /></td>
		</tr>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('EDIT XREF END DATE TIP'); ?>"><label
				for="enddates"><?php echo JText::_('ENDDATE') .': '; ?></label></td>
			<td><?php echo $this->calendar($this->row->enddates, 'enddates', 'enddates'); ?>
			</td>
		</tr>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('EDIT XREF END TIME TIP'); ?>"><label
				for="endtimes"><?php echo JText::_('ENDTIMES') .': '; ?></label></td>
			<td><input type="text" size="8" maxlength="8" name="endtimes"
				id="endtimes" value="<?php echo $this->row->endtimes; ?>" /></td>
		</tr>
		<?php if ($this->params->get('edit_registration_end')): ?>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('EDIT XREF REGISTRATION END TIP'); ?>"><label
				for="registrationend"><?php echo JText::_('EDIT XREF REGISTRATION END') .': '; ?></label>
			</td>
			<td><?php echo JHTML::calendar($this->row->registrationend, 'registrationend', 'registrationend', '%Y-%m-%d %H:%M'); ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php if ($this->params->get('edit_registration', 0)) :?>
	  <tr>
	    <td class="key hasTip" title="<?php echo JText::_('EDIT XREF MAXIMUM_ATTENDEES TIP'); ?>">
	      <label for="maxattendees"><?php echo JText::_( 'EDIT XREF MAXIMUM_ATTENDEES' ) .': '; ?></label>
	    </td>
	    <td>
	      <input type="text" size="8" maxlength="8" name="maxattendees" id="maxattendees" value="<?php echo $this->row->maxattendees; ?>" /> 
	    </td>
	  </tr>
	  <tr>
	    <td class="key hasTip" title="<?php echo JText::_('EDIT XREF MAXIMUM_WAITINGLIST TIP'); ?>">
	      <label for="maxwaitinglist"><?php echo JText::_( 'EDIT XREF MAXIMUM_WAITINGLIST' ) .': '; ?></label>
	    </td>
	    <td>
	      <input type="text" size="8" maxlength="8" name="maxwaitinglist" id="maxwaitinglist" value="<?php echo $this->row->maxwaitinglist; ?>" /> 
	    </td>
	  </tr>
		<?php endif; ?>
		<?php if ($this->params->get('edit_price', 0)): ?>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('EDIT XREF COURSE PRICE TIP'); ?>"><label
				for="course_price"><?php echo JText::_( 'EDIT XREF COURSE PRICE' ) .': '; ?></label>
			</td>
			<td>
		    <table>
					<?php foreach ((array)$this->prices as $k => $r): ?>
				  <tr>
				  	<td><?php echo JHTML::_('select.genericlist', $this->pricegroupsoptions, 'pricegroup[]', '', 'value', 'text', $r->pricegroup_id); ?></td>
				  	<td><input type="text" name="price[]" class="price-val" value="<?php echo $r->price; ?>" size="10" /> <button type="button" class="price-button remove-price"><?php echo Jtext::_('COM_REDEVENT_REMOVE'); ?></button></td>
				  </tr>
				  <?php endforeach; ?>
				  <tr id="trnewprice">
				  	<td><?php echo JHTML::_('select.genericlist', $this->pricegroupsoptions, 'pricegroup[]', array('id' => 'newprice', 'class' => 'newprice')); ?></td>
				  	<td><input type="text" name="price[]" class="price-val" value="0.00" size="10" /> <button type="button" class="price-button" id="add-price"><?php echo Jtext::_('add'); ?></button></td>  	
				  </tr>
		    </table>
			</td>
		</tr>
		<?php endif; ?>
		<?php if ($this->params->get('edit_credits', 0)): ?>
		<tr>
			<td class="key hasTip"
				title="<?php echo JText::_('EDIT XREF COURSE CREDIT TIP'); ?>"><label
				for="course_credit"><?php echo JText::_( 'EDIT XREF COURSE CREDIT' ) .': '; ?></label>
			</td>
			<td><input type="text" size="8" maxlength="8" name="course_credit"
				id="course_credit" value="<?php echo $this->row->course_credit; ?>" />
			</td>
		</tr>
		<?php endif; ?>
		
		<?php if ($this->params->get('edit_customs', 0) && count($this->xcustoms)): ?>
    <?php foreach ($this->xcustoms as $field): ?>
    <tr>
      <td class="key">
        <label for="custom<?php echo $field->id; ?>" class="hasTip" title="<?php echo JText::_($field->get('name')).'::'. $field->get('tips'); ?>">
          <?php echo JText::_( $field->name ); ?>:
        </label>
      </td>
      <td>
        <?php echo $field->render(); ?>
        <?php echo ($field->required? ' '.JText::_('Required') : '' ); ?>
      </td>   
    </tr>
    <?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<?php if ($this->params->get('edit_roles', 0)): ?>
<?php echo $this->loadTemplate('roles'); ?>
<?php endif;?>