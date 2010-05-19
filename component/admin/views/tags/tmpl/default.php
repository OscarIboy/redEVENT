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
<h2><?php echo JText::_('REDEVENT_TAGS_TITLE'); ?></h2>

<?php echo $this->tabs->startPane( 'tagstabs' ); ?>
<?php foreach ($this->tags as $section => $tags): ?>

	<?php echo $this->tabs->startPanel( JText::_($section), $section ); ?>
	<table class="tagstable adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('REDEVENT_TAGS_NAME')?></th>
				<th><?php echo JText::_('REDEVENT_TAGS_DESCRIPTION')?></th>
			</tr>
		</thead>
		<tbody>
			<?php $k = 0; ?>
			<?php foreach ($tags as $tag): ?>
			<tr class="<?php echo ($k ? 'row1' : 'row0'); ?>">
				<td>[<?php echo $tag->name ?>]</td>
				<td><?php echo $tag->description ?></td>
			</tr>
			<?php $k = 1 - $k; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->tabs->endPanel(); ?>

<?php endforeach; ?>
<?php echo $this->tabs->endPane(); ?>