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
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table id="textlibrary" class="adminlist" cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead>
	<tr>
		<th width="20">
			<?php echo JText::_('ID'); ?>
		</th>
		<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ); ?>);" />
		</th> 
		<th>
			<?php echo JText::_('TEXT_TAG'); ?>
		</th>
		<th>
			<?php echo JText::_('TEXT_DESCRIPTION'); ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
		$row = $this->rows[$i]; 
		$checked = JHTML::_('grid.checkedout',  $row, $i);
		?>
		<tr>
		<td>
			<?php echo $row->id; ?>
		</td>
		<td>
			<?php echo $checked; ?>
		</td>
		<td>
			[<?php echo $row->text_name; ?>]
		</td>
			<td><?php echo $row->text_description; ?>
		</td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
<input type="hidden" name="option" value="com_redevent" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="controller" value="textlibrary" />
<input type="hidden" name="view" value="textlibrary" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="" />
</form>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>
<script type="text/javascript">
	jQuery("table#textlibrary tr:even").addClass("row0");
	jQuery("table#textlibrary tr:odd").addClass("row1");
</script>