<?php
/**
 * @version 1.0 $Id: default.php 668 2009-08-24 13:41:04Z julien $
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
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="redevent">
	<form action="<?php echo $this->action; ?>" method="post" name="redform" enctype="multipart/form-data" onsubmit="return CheckSubmit(this);">
		<?php echo $this->rfields; ?>
		<input type="hidden" name="xref" value="<?php echo $this->xref; ?>"/>
		<div id="submit_button" style="display: block;">
		<input type="submit" id="redformsubmit" name="submit" value="<?php echo JText::_('COM_REDEVENT_Update'); ?>" />
		<input type="submit" id="redformcancel" name="cancel" value="<?php echo JText::_('COM_REDEVENT_Cancel'); ?>" />
		</div>
	</form>
</div>