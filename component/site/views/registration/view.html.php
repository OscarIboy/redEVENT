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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML registration View class of the redEvent component
 *
 * @package Joomla
 * @subpackage redEvent
 * @since 1.5
 */
class RedeventViewRegistration extends JView
{
	/**
	 * Creates the output for the registration view
	 *
 	 * @since 1.5
	 */
	function display($tpl = null)
	{
		$mainframe  = &Jfactory::getApplication();
		$document 	= &JFactory::getDocument();
		$user		    = &JFactory::getUser();
		$dispatcher = &JDispatcher::getInstance();
		
		$config     = redEVENTHelper::config();
		$acl        = UserAcl::getInstance();
		
		$event = $this->get('SessionDetails');	
	
		if ($this->getLayout() == 'confirmed')
		{
			$message = $event->confirmation_message;
			$document->setTitle($event->title.' - '.JText::_('REDEVENT_REGISTRATION_CONFIRMED_PAGE_TITLE'));
		}
		else if ($this->getLayout() == 'review')
		{
			$message = $event->review_message;
			$document->setTitle($event->title.' - '.JText::_('REDEVENT_REGISTRATION_REVIEW_PAGE_TITLE'));
		}
		else if ($this->getLayout() == 'edit')
		{
			return $this->_displayEdit($tpl);
		}
		else {
			echo 'layout not defined';
			return;
		}		
		/* This loads the tags replacer */
		JView::loadHelper('tags');
		/* Start the tag replacer */
		$tags = new redEVENT_tags();
		$message = $tags->ReplaceTags($message);
		
		$this->assignRef('tags',    $tags);
		$this->assignRef('message', $message);
		$this->assignRef('event',   $event);
		parent::display($tpl);
	}

	function _displayEdit($tpl = null)
	{
		$user = &JFactory::getUser();
		$acl  = new UserAcl();
		$xref = JRequest::getInt('xref');
		$submitter_id = JRequest::getInt('submitter_id');
		if (!$submitter_id) {
			JError::raise(0,'Registration id required');
			return false;
		}
		$model  = $this->getModel();
		$model->setXref($xref);
		$course = $this->get('SessionDetails');
		
		$registration = $model->getRegistration($submitter_id);
		if (!$registration) {
			JError::raise(0,$model->getError);
			return false;
		}
		
		if ($acl->canManageAttendees($registration->xref) && JRequest::getVar('task') == 'manageredit') {
			$action = JRoute::_(RedeventHelperRoute::getRegistrationRoute('managerupdate'));
		}
		else if ($registration->uid == $user->get('id')) {
			$action = JRoute::_(RedeventHelperRoute::getRegistrationRoute('update'));
		}
		else {
			JError::raiseError(403,'NOT AUTHORIZED');
			return false;
		}
		$rfcore = new RedformCore();
		$rfields = $rfcore->getFormFields($course->redform_id, array($submitter_id), 1);		
		
		
		$this->assign('action' ,  $action);
		$this->assign('rfields',  $rfields);	
		$this->assign('xref',     $xref);		
		parent::display($tpl);
	}
}
?>