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

<form action="index.php" method="post" name="adminForm">

    	<div id="elconfig-document">
			<div id="page-basic">
				<?php require_once(dirname(__FILE__).DS.'el.settings_basic.html'); ?>
			</div>

			<div id="page-usercontrol">
				<?php require_once(dirname(__FILE__).DS.'el.settings_usercontrol.html'); ?>
			</div>

			<div id="page-details">
				<?php require_once(dirname(__FILE__).DS.'el.settings_detailspage.html'); ?>
			</div>

			<div id="page-layout">
				<?php require_once(dirname(__FILE__).DS.'el.settings_layout.html'); ?>
			</div>

			<div id="page-parameters">
				<?php require_once(dirname(__FILE__).DS.'el.settings_parameters.html'); ?>
			</div>
			
			<div id="page-signup">
				<?php require_once(dirname(__FILE__).DS.'el.settings_signup.html'); ?>
			</div>
		</div>
		<div class="clr"></div>

		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="id" value="1">
		<input type="hidden" name="lastupdate" value="<?php echo $this->elsettings->lastupdate; ?>">
		<input type="hidden" name="option" value="com_redevent">
		<input type="hidden" name="controller" value="settings">
		</form>

		<p class="copyright">
			<?php echo ELAdmin::footer( ); ?>
		</p>