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
 
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.tooltip');
$colspan = 14;
?>

<form action="index.php" method="post" name="adminForm">

	<table class="adminlist" cellspacing="1">
		<tr>
		  	<td width="80%">
				<b><?php echo JText::_( 'DATE' ).':'; ?></b>&nbsp;<?php echo (redEVENTHelper::isValidDate($this->event->dates) ? $this->event->dates : Jtext::_('OPEN DATE')); ?><br />
				<b><?php echo JText::_( 'EVENT TITLE' ).':'; ?></b>&nbsp;<?php echo htmlspecialchars($this->event->title, ENT_QUOTES, 'UTF-8'); ?>
			</td>
			<td width="20%">
				<div class="button2-left"><div class="blank"><a title="<?php echo JText::_('PRINT'); ?>" onclick="window.open('index.php?option=com_redevent&amp;view=attendees&amp;layout=print&amp;task=print&amp;tmpl=component&amp;xref=<?php echo $this->event->xref; ?>', 'popup', 'width=750,height=400,scrollbars=yes,toolbar=no,status=no,resizable=yes,menubar=no,location=no,directories=no,top=10,left=10')"><?php echo JText::_('PRINT'); ?></a></div></div>
				<div class="button2-left"><div class="blank"><?php echo JHTML::link('index.php?option=com_redevent&view=attendees&xref='.$this->event->xref.'&form_id='.$this->event->redform_id.'&format=csv' ,JText::_('CSV EXPORT')); ?></div></div>
			</td>
		  </tr>
	</table>
	<br />
	
	<table class="adminform">
		<tr>
			 <td width="100%">
			 	<?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
				<!-- <input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" /> -->
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="this.form.getElementById('filter').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
		</tr>
	</table>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="5"><?php echo JText::_( 'Num' ); ?></th>
				<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'REGDATE', 'r.uregdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'CONFIRMDATE', 'r.confirmdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'IP ADDRESS', 'r.uip', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'UNIQUE ID', 'r.uid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'USERNAME', 'u.username', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JText::_( 'REMOVE USER' ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'CONFIRMED', 'r.confirmed', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'WAITINGLIST', 'r.waitinglist', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<?php foreach ((array) $this->rf_fields as $f):?>
					<?php $colspan++; ?>
					<th class="title"><?php echo JHTML::_('grid.sort',  $f->field, 'f.field_'.$f->id, $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<?php endforeach;?>
        <th class="title"><?php echo JText::_( 'ANSWERS' ); ?></th>
				<?php if ($this->form->activatepayment): ?>
	        <th class="title"><?php echo JText::_( 'PRICE' ); ?></th>
					<th class="title"><?php echo JHTML::_('grid.sort', 'PAYMENT', 'p.paid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
					<?php $colspan += 2; ?>
        <?php endif; ?>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>

		<tbody>
			<?php
			$k = 0;
			for($i=0, $n=count( $this->rows ); $i < $n; $i++) 
			{
				$row = &$this->rows[$i];
	
				$link 		= 'index.php?option=com_redevent&controller=attendees&task=edit&xref='. $row->xref.'&cid[]='.$row->id;
				$checked 	= JHTML::_('grid.checkedout', $row, $i );
   			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td>
					<?php
						if ( $row->checked_out && ( $row->checked_out != $this->user->get('id') ) ) {
							echo JHTML::Date( $row->uregdate, JText::_( 'DATE_FORMAT_LC2' ) );
						} else {
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT MEMBER' );?>::<?php echo $row->name; ?>">
					<a href="<?php echo $link; ?>">
					<?php echo JHTML::Date( $row->uregdate, JText::_( 'DATE_FORMAT_LC2' ) ); ?>
					</a></span>
					<?php } ?>
				</td>
				<td><?php echo ($row->confirmdate) ? JHTML::Date( $row->confirmdate, JText::_( 'DATE_FORMAT_LC2' ) ) : '-'; ?></td>
				<td><?php echo $row->uip == 'DISABLED' ? JText::_( 'DISABLED' ) : $row->uip; ?></td>
				<td><?php echo $row->course_code .'-'. $row->xref .'-'. $row->attendee_id; ?></td>
				<td><?php echo $row->name; ?></td>
				<td style="text-align: center;"><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','remove')"><img src="images/publish_x.png" width="16" height="16" border="0" alt="Delete" /></a></td>
				<td>
				  <?php 
				  //echo $row->confirmed == 0 ? JText::_('NO') : JText::_('YES'); 
				  if (!$row->confirmed) {
            echo JHTML::link('javascript: void(0);', JHTML::_('image.administrator', 'publish_x.png'), array('onclick' => 'return listItemTask(\'cb'.$i.'\', \'confirmattendees\');'));
				  }
          else {
            echo JHTML::link('javascript: void(0);', JHTML::_('image.administrator', 'tick.png'), array('onclick' => 'return listItemTask(\'cb'.$i.'\', \'unconfirmattendees\');'));
          }
				  ?>
				</td>
				<td><?php // echo $row->waitinglist == 0 ? JText::_('NO') : JText::_('YES'); ?>
          <?php 
          //echo $row->confirmed == 0 ? JText::_('NO') : JText::_('YES'); 
          if (!$row->waitinglist) {
            echo JHTML::link('javascript: void(0);', 
                             JHTML::_('image.administrator', 'publish_x.png'), 
                             array('onclick' => 'return listItemTask(\'cb'.$i.'\', \'onwaiting\');', 'title' => JText::_('PUT ON WAITING LIST')));
          }
          else {
            echo JHTML::link( 'javascript: void(0);', 
                              JHTML::_('image.administrator', 'tick.png'), 
                              array('onclick' => 'return listItemTask(\'cb'.$i.'\', \'offwaiting\');', 'title' => JText::_('PUT OFF WAITING LIST')));
          }
          ?>
        </td>
				
        <?php foreach ((array) $this->rf_fields as $f):?>
					<?php $fname = 'field_'.$f->id; ?>
					<td><?php echo $row->$fname; ?></td>
				<?php endforeach;?>
        
        <td><a href="<?php echo JRoute::_('index.php?option=com_redevent&view=attendeeanswers&tmpl=component&submitter_id='. $row->submitter_id); ?>" class="answersmodal"><?php echo JText::_('view')?></a></td>
				
				<?php if ($this->form->activatepayment): ?>
					<td>
						<?php echo $row->price; ?>
					</td>
					<td class="price <?php echo ($row->paid ? 'paid' : 'unpaid'); ?>">
						<?php if (!$row->paid): ?>
						<span class="hasTip" title="<?php echo JText::_('REGISTRATION_NOT_PAID').'::'.$row->status; ?>"><?php echo JHTML::_('image.administrator', 'publish_x.png'); ?><?php echo $row->status; ?></span>
						<?php echo ' '.JHTML::link(JURI::root().'/index.php?option=com_redform&controller=payment&task=select&key='.$row->submit_key, JText::_('link')); ?>
						<?php else: ?>
						<span class="hasTip" title="<?php echo JText::_('REGISTRATION_PAID').'::'.$row->status; ?>"><?php echo JHTML::_('image.administrator', 'tick.png'); ?></span>
						<?php endif; ?>						
					</td>
				<?php endif; ?>
			</tr>
			<?php $k = 1 - $k; } ?>
		</tbody>

	</table>

	<p class="copyright">
		<?php echo ELAdmin::footer( ); ?>

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_redevent" />
		<input type="hidden" name="controller" value="attendees" />
		<input type="hidden" name="view" value="attendees" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="form_id" value="<?php echo $this->event->redform_id; ?>" />
		<input type="hidden" name="eventid" value="<?php echo $this->event->eventid; ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
	</p>
</form>