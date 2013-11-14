<?php
/**
 * @package     Redcomponent.redeventsync
 * @subpackage  com_redeventsync
 * @copyright   Copyright (C) 2013 redCOMPONENT.com
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die();

require_once 'abstractmessage.php';

/**
 * redEVENT sync Customersrq Model
 *
 * @package  RED.redeventsync
 * @since    2.5
 */
class RedeventsyncModelCustomersrq extends RedeventsyncModelAbstractmessage
{
	/**
	 * process CreateAttendeeRQ request
	 *
	 * @param   SimpleXMLElement  $xml  xml data for the object
	 *
	 * @return boolean
	 */
	protected function processCustomerRQ(SimpleXMLElement $xml)
	{
		require_once JPATH_SITE . '/components/com_redmember/lib/redmemberlib.php';

		$transaction_id = (int) $xml->TransactionId;

		try
		{
			$email = (string) $xml->Emailaddress;
			$row = JTable::getInstance('RedEvent_eventvenuexref', '');

			// Find user
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('id');
			$query->from('#__users');
			$query->where('email = ' . $db->quote($email));

			$db->setQuery($query);
			$user_id = $db->loadResult();

			if (!$user_id)
			{
				throw new Exception('User not found');
			}

			$user = redmemberlib::getUserData($user_id);

			// Log
			$this->log(REDEVENTSYNC_LOG_DIRECTION_INCOMING, $transaction_id,
				$xml, 'ok');
		}
		catch (Exception $e)
		{
			$response = new SimpleXMLElement('<CustomerRS/>');
			$response->addChild('TransactionId', $transaction_id);

			$errors = new SimpleXMLElement('<Errors/>');
			$errors->addChild('Error', $e->getMessage());
			$this->appendElement($response, $errors);

			$this->addResponse($response);

			// Log
			$this->log(REDEVENTSYNC_LOG_DIRECTION_OUTGOING, $transaction_id,
				$response, 'error');

			return false;
		}

		// Generate xml from user data
		$response = new SimpleXMLElement('<CustomerRS/>');
		$response->addChild('TransactionId', $transaction_id);

		$success = new SimpleXMLElement('<Success/>');
		$success->addChild('Emailaddress', $user->email);
		$success->addChild('Firstname',    '');
		$success->addChild('Lastname',     $user->name);
		$success->addChild('Address1',     '');
		$success->addChild('Address2',     '');
		$success->addChild('Address3',     '');
		$success->addChild('City',         '');
		$success->addChild('Zipcode',      '');
		$success->addChild('Countrycode',  '');
		$success->addChild('Phonenumber',  '');
		$success->addChild('Mobilephonenumber', '');
		$success->addChild('Company',      '');
		$success->addChild('PointOfSales', '');
		$success->addChild('Salesman',     '');
		$success->addChild('Description',  '');

		$this->appendElement($response, $success);

		// Log
		$this->log(REDEVENTSYNC_LOG_DIRECTION_OUTGOING, $transaction_id,
			$response, 'ok');

		$this->addResponse($response);

		return true;
	}

	/**
	 * Init response message if applicable
	 *
	 * @return void
	 */
	protected function initResponse()
	{
		$this->response = new SimpleXMLElement('<CustomersRS xmlns="http://www.redcomponent.com/redevent"/>');
	}
}
