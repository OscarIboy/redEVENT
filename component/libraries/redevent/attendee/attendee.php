<?php
/**
 * @package    Redevent.Library
 * @copyright  Copyright (C) 2008 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * attendee class - helper for managing attendees
 *
 * @package  Redevent.Library
 * @since    2.5
 */
class RedeventAttendee extends JObject
{
	protected $username;

	protected $fullname;

	protected $email;

	protected $id;

	protected $db;

	/**
	 * data from db
	 * @var object
	 */
	protected $data;

	/**
	 * redform answers
	 *
	 * @var array
	 */
	protected $answers;

	/**
	 * events data, caching for when several attendees are called
	 * @var array
	 */
	static protected $sessions = array();

	/**
	 * array of 'attending' registrations for events sessions data
	 * @var array
	 */
	static protected $attending = array();

	/**
	 * Constructor
	 *
	 * @param   int  $id  attendee id
	 */
	public function __construct($id = null)
	{
		if ($id)
		{
			$this->setId($id);
		}

		$this->db = JFactory::getDbo();
	}

	/**
	 * Set username
	 *
	 * @param   string  $name  username
	 *
	 * @return void
	 */
	public function setUsername($name)
	{
		$this->username = $name;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		if (!$this->username)
		{
			$answers = $this->getAnswers();

			foreach ($answers as $a)
			{
				if ($a->fieldtype == 'username' && $a->answer)
				{
					$this->username = $a->answer;

					return $this->username;
				}
			}

			// Still there... look for user ?
			if ($this->load()->uid)
			{
				$this->username = JFactory::getUser()->get('username');

				return $this->username;
			}
		}

		return $this->username;
	}

	/**
	 * Set Fullname
	 *
	 * @param   string  $name  Fullname
	 *
	 * @return void
	 */
	public function setFullname($name)
	{
		if (!$this->fullname)
		{
			$answers = $this->getAnswers();

			foreach ($answers as $a)
			{
				if ($a->fieldtype == 'fullname' && $a->answer)
				{
					$this->fullname = $a->answer;

					return $this->fullname;
				}
			}

			// Still there... look for user ?
			if ($this->load()->uid)
			{
				$this->fullname = JFactory::getUser()->get('name');

				return $this->fullname;
			}
		}

		$this->fullname = $name;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getFullname()
	{
		return $this->fullname;
	}

	/**
	 * Set email
	 *
	 * @param   string  $email  email
	 *
	 * @return void
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		if (!$this->email)
		{
			$answers = $this->getAnswers();

			foreach ($answers as $a)
			{
				if ($a->fieldtype == 'email' && JMailHelper::isEmailAddress($a->getValue()))
				{
					$this->email = $a->getValue();

					return $this->email;
				}
			}

			// Still there... look for user ?
			if ($this->load()->uid)
			{
				$this->email = JFactory::getUser()->get('email');

				return $this->email;
			}
		}

		return $this->email;
	}

	/**
	 * Set id
	 *
	 * @param   int  $id  attendee id
	 *
	 * @return void
	 */
	public function setId($id)
	{
		$this->id = (int) $id;
	}

	/**
	 * get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * loads data from the db
	 *
	 * @return object
	 */
	public function load()
	{
		if (empty($this->data))
		{
			$query = ' SELECT r.* '
				. ' FROM #__redevent_register AS r '
				. ' WHERE r.id = ' . $this->db->Quote($this->id);
			$this->db->setQuery($query);
			$res = $this->db->loadObject();
			$this->data = $res;
		}

		return $this->data;
	}

	/**
	 * confirms attendee registration
	 *
	 * @return true on success
	 */
	public function confirm()
	{
		$current = $this->load();

		if ($current->confirmed)
		{
			return true;
		}

		// First, changed status to confirmed
		$query = ' UPDATE #__redevent_register '
			. ' SET confirmed = 1, confirmdate = ' . $this->db->Quote(gmdate('Y-m-d H:i:s'))
			. '   , paymentstart = ' . $this->db->Quote(gmdate('Y-m-d H:i:s'))
			. ' WHERE id = ' . $this->id;
		$this->db->setQuery($query);
		$res = $this->db->query();

		if (!$res)
		{
			$this->setError(JText::_('COM_REDEVENT_REGISTRATION_FAILED_CONFIRM_REGISTRATION'));

			return false;
		}

		// Now, handle waiting list
		$session = $this->getSessionDetails();

		if ($session->maxattendees == 0)
		{
			// No waiting list
			// Send attending email
			$this->sendWaitinglistStatusEmail(0);
			$this->sendWLAdminNotification(0);

			return true;
		}

		$attendees = $this->getAttending();

		if (count($attendees) > $session->maxattendees)
		{
			// Put this attendee on WL
			$this->toggleWaitingListStatus(1);
		}
		else
		{
			$this->addToAttending();

			// Send attending email
			$this->sendWaitinglistStatusEmail(0);
			$this->sendWLAdminNotification(0);
		}

		return true;
	}

	/**
	 * toggles waiting list status
	 *
	 * @param   int  $waiting  0 for attending, 1 for waiting
	 *
	 * @return true on success
	 */
	public function toggleWaitingListStatus($waiting = null)
	{
		$data = $this->load();

		if (is_null($waiting))
		{
			$waiting = $data->waitinglist ? 0 : 1;
		}

		$query = $this->db->getQuery(true);

		$query->update('#__redevent_register')
			->set('waitinglist = ' . $waiting)
			->set('paymentstart = NOW()')
			->where('id = ' . $this->db->Quote($this->id));

		$this->db->setQuery($query);

		if (!$this->db->execute())
		{
			$this->setError(JText::_('COM_REDEVENT_FAILED_UPDATING_WAITINGLIST_STATUS'));

			return false;
		}

		try
		{
			$this->sendWaitinglistStatusEmail($waiting);
			$this->sendWLAdminNotification($waiting);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * send waiting list status emails
	 *
	 * @param   int  $waiting  status: 0 for attending, 1 for waiting
	 *
	 * @return boolean true on success
	 *
	 * @throws Exception
	 */
	public function sendWaitinglistStatusEmail($waiting = 0)
	{
		$config = RedeventHelper::config();

		if ($config->get('disable_waitinglist_status_email', 0))
		{
			return true;
		}

		$app = JFactory::getApplication();
		$data = $this->load();
		$session = $this->getSessionDetails();

		$sid = $data->sid;

		$rfcore = RdfCore::getInstance();
		$emails = $rfcore->getSidContactEmails($sid);

		$valid_emails = false;

		foreach ($emails as $e)
		{
			if (JMailHelper::isEmailAddress($e['email']))
			{
				$valid_emails = true;
				break;
			}
		}

		// Stop if no valid emails
		if (!$valid_emails)
		{
			return true;
		}

		if (empty($this->taghelper))
		{
			$this->taghelper = new RedeventTags;
			$this->taghelper->setXref($data->xref);
			$this->taghelper->setSubmitkey($data->submit_key);
		}

		if ($waiting == 0)
		{
			if ($session->notify_off_list_subject)
			{
				$subject = $session->notify_off_list_subject;
				$body = $session->notify_off_list_body;
			}
			elseif ($session->notify_subject)
			{
				$subject = $session->notify_subject;
				$body = $session->notify_body;
			}
			else
			{
				$subject = JText::_('COM_REDEVENT_WL_DEFAULT_NOTIFY_OFF_SUBJECT');
				$body = JText::_('COM_REDEVENT_WL_DEFAULT_NOTIFY_OFF_BODY');
			}

			$body = $this->taghelper->ReplaceTags($body);
			$subject = $this->taghelper->ReplaceTags($subject);
		}
		else
		{
			if ($session->notify_on_list_body)
			{
				$subject = $session->notify_on_list_subject;
				$body = $session->notify_on_list_body;
			}
			else
			{
				$subject = JText::_('COM_REDEVENT_WL_DEFAULT_NOTIFY_ON_SUBJECT');
				$body = JText::_('COM_REDEVENT_WL_DEFAULT_NOTIFY_ON_BODY');
			}

			$body = $this->taghelper->ReplaceTags($body);
			$subject = $this->taghelper->ReplaceTags($subject);
		}

		if (empty($subject))
		{
			// Not sending !
			throw new Exception(JText::_('COM_REDEVENT_WL_NOTIFICATION_MISSING_SUBJECT'));
		}

		// Update image paths in body
		$body = RedeventHelperOutput::ImgRelAbs($body);

		$mailer = JFactory::getMailer();

		$sender = array($app->getCfg('mailfrom'), $app->getCfg('sitename'));
		$mailer->setSender($sender);
		$mailer->addReplyTo($sender);

		foreach ($emails as $email)
		{
			/* Add the email address */
			$mailer->AddAddress($email['email'], $email['fullname']);
		}

		/* Mail submitter */
		$htmlmsg = '<html><head><title></title></title></head><body>' . $body . '</body></html>';
		$mailer->setBody($htmlmsg);
		$mailer->setSubject($subject);
		$mailer->IsHTML(true);

		/* Send the mail */
		if (!$mailer->Send())
		{
			RedeventHelperLog::simpleLog(JText::_('COM_REDEVENT_REGISTRATION_FAILED_SENDING_WAITING_LIST_STATUS_EMAIL'));
			throw new Exception(JText::_('COM_REDEVENT_REGISTRATION_FAILED_SENDING_WAITING_LIST_STATUS_EMAIL'));

			return false;
		}

		return true;
	}

	/**
	 * send waiting list status change notification to event admins
	 *
	 * @param   int  $waiting  0 for attending, 1 for waiting
	 *
	 * @return boolean true on success
	 */
	public function sendWLAdminNotification($waiting = 0)
	{
		$params = JComponentHelper::getParams('com_redevent');

		if (!$params->get('wl_notify_admin', 0))
		{
			// Never notify admins
			return true;
		}
		elseif ($params->get('wl_notify_admin', 0) == 1 && $waiting == 1)
		{
			// Only for people begin added to attending
			return true;
		}
		elseif ($params->get('wl_notify_admin', 0) == 2 && $waiting == 0)
		{
			// Only for people being added to waiting list
			return true;
		}

		$app = JFactory::getApplication();
		$tags = new RedeventTags;
		$tags->setXref($this->getXref());
		$tags->addOptions(array('sids' => array($this->load()->sid)));
		$event = $this->getSessionDetails();

		// Recipients
		$recipients = $this->getAdminEmails();

		if (!count($recipients))
		{
			return true;
		}

		$mailer = JFactory::getMailer();
		$sender = array($app->getCfg('mailfrom'), $app->getCfg('sitename'));
		$mailer->setSender($sender);
		$mailer->addReplyTo($sender);

		foreach ($recipients as $r)
		{
			$mailer->addAddress($r['email'], $r['name']);
		}

		$subject = $tags->ReplaceTags($waiting ? $params->get('wl_notify_admin_waiting_subject') : $params->get('wl_notify_admin_attending_subject'));
		$body = $tags->ReplaceTags($waiting ? $params->get('wl_notify_admin_waiting_body') : $params->get('wl_notify_admin_attending_body'));
		$body = RedeventHelperOutput::ImgRelAbs($body);

		$mailer->setSubject($subject);
		$mailer->MsgHTML($body);

		if (!$mailer->send())
		{
			RedeventHelperLog::simplelog(JText::_('COM_REDEVENT_ERROR_REGISTRATION_MANAGERS_NOTIFICATION_FAILED'));
			$this->setError(JText::_('COM_REDEVENT_ERROR_REGISTRATION_MANAGERS_NOTIFICATION_FAILED'));

			return false;
		}

		return true;
	}

	/**
	 * return email for the registration admins
	 *
	 * @return array
	 */
	public function getAdminEmails()
	{
		$params = JComponentHelper::getParams('com_redevent');
		$event = $this->getSessionDetails();

		$recipients = array();

		// Default recipients
		$default = $params->get('registration_default_recipients');

		if (!empty($default))
		{
			if (strstr($default, ';'))
			{
				$addresses = explode(";", $default);
			}
			else
			{
				$addresses = explode(",", $default);
			}

			foreach ($addresses as $a)
			{
				$a = trim($a);

				if (JMailHelper::isEmailAddress($a))
				{
					$recipients[] = array('email' => $a, 'name' => '');
				}
			}
		}

		// Creator
		if ($params->get('registration_notify_creator', 1))
		{
			if (JMailHelper::isEmailAddress($event->creator_email))
			{
				$recipients[] = array('email' => $event->creator_email, 'name' => $event->creator_name);
			}
		}

		// Venue recipients
		if (!empty($event->venue_email))
		{
			$recipients[] = array('email' => $event->venue_email, 'name' => $event->venue);
		}

		// Group recipients
		$gprecipients = $this->getXrefRegistrationRecipients();

		if ($gprecipients)
		{
			foreach ($gprecipients AS $r)
			{
				if (JMailHelper::isEmailAddress($r->email))
				{
					$recipients[] = array('email' => $r->email, 'name' => $r->name);
				}
			}
		}

		// Redform recipients
		$rfrecipients = $this->getRFRecipients();

		foreach ((array) $rfrecipients as $r)
		{
			if (JMailHelper::isEmailAddress($r))
			{
				$recipients[] = array('email' => $r, 'name' => '');
			}
		}

		// Custom recipients
		$customrecipients = array();

		JPluginHelper::importPlugin('redevent');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onGetRegistrationAdminEmails', array($this->id, &$customrecipients));

		foreach ((array) $customrecipients as $r)
		{
			if (JMailHelper::isEmailAddress($r['email']))
			{
				$recipients[] = array('email' => $r['email'], 'name' => $r['name']);
			}
		}

		return $recipients;
	}

	/**
	 * Replace text tags
	 *
	 * @param   string  $text  text to replace
	 *
	 * @return string
	 */
	public function replaceTags($text)
	{
		$data = $this->load();

		$tags = new RedeventTags;
		$tags->setSubmitkey($data->submit_key);
		$tags->setXref($this->getXref());
		$tags->addOptions(array('sids' => array($data->sid)));

		$text = $tags->ReplaceTags($text);

		return $text;
	}

	/**
	 * return selected redform recipients emails if any
	 *
	 * @return string
	 */
	protected function getRFRecipients()
	{
		$answers = $this->getAnswers();

		$emails = array();

		foreach ($answers as $f)
		{
			if ($f->fieldtype == 'recipients' && $f->answer)
			{
				$email = explode('~~~', $f->answer);
				$emails = array_merge($emails, $email);
			}
		}

		return count($emails) ? $emails : false;
	}

	/**
	 * get redform answers for this attendee
	 *
	 * @return array
	 */
	protected function getAnswers()
	{
		if (empty($this->answers))
		{
			$rfcore = RdfCore::getInstance();
			$sid = $this->load()->sid;
			$sidsanswers = $rfcore->getAnswers(array($sid));
			$this->answers = $sidsanswers->getSubmissionBySid($sid);
		}

		return $this->answers;
	}

	/**
	 * returns attendee event session info
	 *
	 * @return object
	 */
	protected function getSessionDetails()
	{
		$xref = $this->getXref();

		if (!isset(self::$sessions[$xref]))
		{
			$query = 'SELECT a.id AS did, x.id AS xref, a.title, a.datdescription, a.meta_keywords, a.meta_description, a.datimage, '
				. ' a.registra, a.unregistra, a.activate, a.notify, a.redform_id as form_id, '
				. ' a.notify_confirm_body, a.notify_confirm_subject, a.notify_subject, a.notify_body, '
				. ' a.notify_off_list_subject, a.notify_off_list_body, a.notify_on_list_subject, a.notify_on_list_body, '
				. ' x.*, a.created_by, a.redform_id, x.maxwaitinglist, x.maxattendees, a.juser, a.show_names, a.showfields, '
				. ' a.submission_type_email, a.submission_type_external, a.submission_type_phone,'
				. ' v.venue, v.email as venue_email,'
				. ' u.name AS creator_name, u.email AS creator_email, '
				. ' a.confirmation_message, a.review_message, '
				. " IF (x.course_credit = 0, '', x.course_credit) AS course_credit, a.course_code, a.submission_types, c.name AS catname, c.published, c.access,"
				. ' CASE WHEN CHAR_LENGTH(x.title) THEN CONCAT_WS(\' - \', a.title, x.title) ELSE a.title END as full_title, '
				. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as categoryslug '
				. ' FROM #__redevent_events AS a'
				. ' LEFT JOIN #__redevent_event_venue_xref AS x ON x.eventid = a.id'
				. ' LEFT JOIN #__redevent_venues AS v ON x.venueid = v.id'
				. ' LEFT JOIN #__redevent_event_category_xref AS xcat ON xcat.event_id = a.id'
				. ' LEFT JOIN #__redevent_categories AS c ON c.id = xcat.category_id'
				. ' LEFT JOIN #__users AS u ON a.created_by = u.id '
				. ' WHERE x.id = ' . $xref;
			$this->db->setQuery($query);
			self::$sessions[$xref] = $this->db->loadObject();
		}

		return self::$sessions[$xref];
	}

	/**
	 * return attendee event session xref
	 *
	 * @return int
	 */
	public function getXref()
	{
		return $this->load()->xref;
	}

	/**
	 * return redform submitted files path if any
	 *
	 * @return array
	 */
	protected function getRFFiles()
	{
		$files = array();
		$answers = $this->getAnswers();

		foreach ($answers as $f)
		{
			if ($f->fieldtype == 'fileupload')
			{
				$path = $f->answer;

				if (!empty($path) && file_exists($path))
				{
					$files[] = $path;
				}
			}
		}

		return $files;
	}

	/**
	 * returns array of ids of currently attending (confirmed, not on wl, not cancelled) register_id
	 *
	 * @return array;
	 */
	protected function getAttending()
	{
		if (!isset(self::$attending[$this->getXref()]))
		{
			$query = ' SELECT r.id '
				. ' FROM #__redevent_register AS r '
				. ' WHERE r.xref = ' . $this->getXref()
				. '   AND r.confirmed = 1 '
				. '   AND r.cancelled = 0 '
				. '   AND r.waitinglist = 0 ';
			$this->db->setQuery($query);
			self::$attending[$this->getXref()] = $this->db->loadColumn();
		}

		return self::$attending[$this->getXref()];
	}

	/**
	 * add id to the list of attending attendees
	 *
	 * @return void
	 */
	protected function addToAttending()
	{
		self::$attending[$this->getXref()][] = $this->id;
	}

	/**
	 * returns registration recipients from groups acl
	 *
	 * @return array
	 */
	protected function getXrefRegistrationRecipients()
	{
		$event = $this->getSessionDetails();
		$usersIds = RedeventUserAcl::getXrefRegistrationRecipients($event->xref);

		if (!$usersIds)
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('u.name, u.email');
		$query->from('#__users AS u');
		$query->where('u.id IN (' . implode(",", $usersIds) . ')');

		$db->setQuery($query);
		$xref_group_recipients = $db->loadObjectList();

		return $xref_group_recipients;
	}

	/**
	 * Send e-mail confirmations
	 *
	 * @return boolean
	 */
	public function sendNotificationEmail()
	{
		$mainframe = JFactory::getApplication();
		$eventsettings = $this->getSessionDetails();

		/**
		 * Send a submission mail to the attendee and/or contact person
		 * This will only work if the contact person has an e-mail address
		 **/
		if (isset($eventsettings->notify) && $eventsettings->notify)
		{
			/* Load the mailer */
			$mailer = JFactory::getMailer();
			$mailer->isHTML(true);
			$mailer->From = $mainframe->getCfg('mailfrom');
			$mailer->FromName = $mainframe->getCfg('sitename');
			$mailer->AddReplyTo(array($mainframe->getCfg('mailfrom'), $mainframe->getCfg('sitename')));

			$tags = new RedeventTags;
			$tags->setXref($this->getXref());
			$tags->addOptions(array('sids' => array($this->load()->sid)));

			$rfcore = RdfCore::getInstance();
			$emails = $rfcore->getSidContactEmails($this->load()->sid);

			/* build activation link */
			// TODO: use the route helper !
			$url = JRoute::_(
				JURI::root() . 'index.php?option=com_redevent&controller=registration&task=activate'
				. '&confirmid=' . str_replace(".", "_", $this->data->uip)
				. 'x' . $this->data->xref
				. 'x' . $this->data->uid
				. 'x' . $this->data->id
				. 'x' . $this->data->submit_key
			);
			$activatelink = '<a href="' . $url . '">' . JText::_('COM_REDEVENT_Activate') . '</a>';
			$cancellink = JRoute::_(
				JURI::root() . 'index.php?option=com_redevent&task=cancelreg'
				. '&rid=' . $this->data->id . '&xref=' . $this->data->xref . '&submit_key=' . $this->data->submit_key
			);

			/* Mail attendee */
			$htmlmsg = '<html><head><title></title></title></head><body>';
			$htmlmsg .= $eventsettings->notify_body;
			$htmlmsg .= '</body></html>';

			$htmlmsg = $tags->ReplaceTags($htmlmsg);
			$htmlmsg = str_replace('[activatelink]', $activatelink, $htmlmsg);
			$htmlmsg = str_replace('[cancellink]', $cancellink, $htmlmsg);
			$htmlmsg = str_replace('[fullname]', $this->getFullname(), $htmlmsg);

			// Convert urls
			$htmlmsg = RedeventHelperOutput::ImgRelAbs($htmlmsg);

			$mailer->setBody($htmlmsg);
			$subject = $tags->ReplaceTags($eventsettings->notify_subject);
			$mailer->setSubject($subject);

			foreach ($emails as $email)
			{
				/* Add the email address */
				$mailer->AddAddress($email['email'], $email['fullname']);
			}

			/* send */
			if (!$mailer->Send())
			{
				RedeventHelperLog::simpleLog('Error sending notify message to submitted attendants');

				return false;
			}
		}

		return true;
	}

	/**
	 * Notify managers
	 *
	 * @param   bool  $unreg  is this unregistration ?
	 *
	 * @return bool
	 */
	public function notifyManagers($unreg = false)
	{
		jimport('joomla.mail.helper');
		$app = JFactory::getApplication();
		$params = $app->getParams('com_redevent');

		$tags = new RedeventTags;
		$tags->setXref($this->getXref());
		$tags->setSubmitkey($this->load()->submit_key);
		$tags->addOptions(array('sids' => array($this->load()->sid)));

		$event = $this->getSessionDetails();

		$recipients = $this->getAdminEmails();

		if (!count($recipients))
		{
			return true;
		}

		$mailer = JFactory::getMailer();

		if ($this->getEmail() && $params->get('allow_email_aliasing', 1))
		{
			$sender = array($this->getEmail(), $this->getFullname());
		}
		else
		{
			// Default to site settings
			$sender = array($app->getCfg('mailfrom'), $app->getCfg('sitename'));
		}

		$mailer->setSender($sender);
		$mailer->addReplyTo($sender);

		foreach ($recipients as $r)
		{
			$mailer->addAddress($r['email'], $r['name']);
		}

		$mail = '<HTML><HEAD>
			<STYLE TYPE="text/css">
			<!--
			  table.formanswers , table.formanswers td, table.formanswers th
				{
				    border-color: darkgrey;
				    border-style: solid;
				    text-align:left;
				}
				table.formanswers
				{
				    border-width: 0 0 1px 1px;
				    border-spacing: 0;
				    border-collapse: collapse;
				    padding: 5px;
				}
				table.formanswers td, table.formanswers th
				{
				    margin: 0;
				    padding: 4px;
				    border-width: 1px 1px 0 0;
				}
			-->
			</STYLE>
			</head>
			<BODY bgcolor="#FFFFFF">
			' . $tags->ReplaceTags($unreg ? $params->get('unregistration_notification_body') : $params->get('registration_notification_body')) . '
			</body>
			</html>';

		// Convert urls
		$mail = RedeventHelperOutput::ImgRelAbs($mail);

		if (!$unreg && $params->get('registration_notification_attach_rfuploads', 1))
		{
			// Files submitted through redform
			$files = $this->getRFFiles();
			$filessize = 0;

			foreach ($files as $f)
			{
				$filessize += filesize($f);
			}

			if ($filessize < $params->get('registration_notification_attach_rfuploads_maxsize', 1500) * 1000)
			{
				foreach ($files as $f)
				{
					$mailer->addAttachment($f);
				}
			}
		}

		$mailer->setSubject(
			$tags->ReplaceTags($unreg ? $params->get('unregistration_notification_subject') : $params->get('registration_notification_subject'))
		);
		$mailer->MsgHTML($mail);

		if (!$mailer->send())
		{
			RedeventHelperLog::simplelog(JText::_('COM_REDEVENT_ERROR_REGISTRATION_MANAGERS_NOTIFICATION_FAILED'));
			$this->setError(JText::_('COM_REDEVENT_ERROR_REGISTRATION_MANAGERS_NOTIFICATION_FAILED'));

			return false;
		}

		return true;
	}
}
