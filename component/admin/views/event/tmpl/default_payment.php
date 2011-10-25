<?php $infoimage = JHTML::image('components/com_redevent/assets/images/icon-16-hint.png', JText::_('COM_REDEVENT_NOTES' ) ); ?>
<table class="adminform">
	<tr>
		<td colspan="2">
			<span class="editlinktip hasTip" title="<?php echo JText::_('COM_REDEVENT_PAYMENTPROCESSING' ); ?>::<?php echo JText::_('COM_REDEVENT_PAYMENTPROCESSING_INFO'); ?>">
				<?php echo $infoimage; ?>
			</span><label for="paymentprocessing"><?php echo JText::_('COM_REDEVENT_PAYMENTPROCESSING'); ?></label>
			
			<div id="paymentprocessing_screen">
				<?php echo $this->printTags('paymentprocessing'); ?>
				<?php echo $this->editor->display( 'paymentprocessing',  $this->row->paymentprocessing, '100%;', '350', '75', '20', array('pagebreak', 'readmore', 'image') ) ; ?>
			</div>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<span class="editlinktip hasTip" title="<?php echo JText::_('COM_REDEVENT_PAYMENTACCEPTED' ); ?>::<?php echo JText::_('COM_REDEVENT_PAYMENTACCEPTED_INFO'); ?>">
				<?php echo $infoimage; ?>
			</span><label for="paymentaccepted"><?php echo JText::_('COM_REDEVENT_PAYMENTACCEPTED'); ?></label>
			
			<div id="paymentaccepted_screen">
				<?php echo $this->printTags('paymentaccepted'); ?>
				<?php echo $this->editor->display( 'paymentaccepted',  $this->row->paymentaccepted, '100%;', '350', '75', '20', array('pagebreak', 'readmore', 'image') ) ; ?>
			</div>
		</td>
	</tr>
</table>
