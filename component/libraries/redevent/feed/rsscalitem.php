<?php
/**
 * @package    Redevent.Library
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * helper for rss item
 *
 * @package  Redevent.Library
 * @since    3.0
 */
class RedeventFeedRsscalitem
{
	/**
	 * Get Item from session data
	 *
	 * @param   object  $session  session data
	 *
	 * @return JFeedItem
	 */
	public static function getItem($session)
	{
		$config = RedeventHelper::config();

		$title = RedeventHelper::getSessionFullTitle($session);
		$link = JRoute::_(JURI::base() . RedeventHelperRoute::getDetailsRoute($session->slug, $session->xslug));

		$item = new rsscalItem($title, $link);
		$item->addElement('ev:location', $session->venue);
		$item->addElement('dc:subject', $title);

		if (!empty($session->categories))
		{
			$category = array();

			foreach ($session->categories AS $cat)
			{
				$category[] = $cat->name;
			}

			$item->addElement('ev:type', implode(', ', $category));
		}

		if (RedeventHelper::isValidDate($session->dates))
		{
			$date = $session->dates;

			if (RedeventHelper::isValidTime($session->times))
			{
				$date .= ' ' . $session->times;
			}

			$item->addElement('ev:startdate', JFactory::getDate($date)->toISO8601());
		}

		if (RedeventHelper::isValidDate($session->enddates))
		{
			$date = $session->enddates;

			if (RedeventHelper::isValidTime($session->endtimes))
			{
				$date .= ' ' . $session->endtimes;
			}

			$item->addElement('ev:enddate', JFactory::getDate($date)->toISO8601());
		}

		return $item;
	}
}