<?php
/**
 * @package     Redevent.Library
 * @subpackage  Entity.twig
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

JLoader::import('reditem.library');

use Aesir\Entity\Twig\AbstractTwigEntity;
use Aesir\Entity\Twig\Traits;

defined('_JEXEC') or die;

/**
 * redEVENT event Twig Entity.
 *
 * @since  3.2.0
 */
final class RedeventEntityTwigEvent extends AbstractTwigEntity
{
	/**
	 * Constructor.
	 *
	 * @param   \RedeventEntityEvent  $entity  The entity
	 */
	public function __construct(\RedeventEntityEvent $entity)
	{
		$this->entity = $entity;
	}

	/**
	 * is utilized for reading data from inaccessible members.
	 *
	 * @param   string  $name  string
	 *
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->entity->$name))
		{
			return $this->entity->$name;
		}

		throw new \RuntimeException('unsupported property in __get: ' . $name);
	}

	/**
	 * is triggered by calling isset() or empty() on inaccessible members.
	 *
	 * @param   string  $name  string
	 *
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->entity->$name);
	}

	/**
	 * Get duration max in days
	 *
	 * @return int
	 */
	public function getDurationMax()
	{
		if (!$sessions = $this->getSessions())
		{
			return false;
		}

		return array_reduce(
			$sessions,
			function($value, $session)
			{
				return max($value, $session->getDurationDays());
			}
		);
	}

	/**
	 * Get duration min in days
	 *
	 * @return int
	 */
	public function getDurationMin()
	{
		if (!$sessions = $this->getSessions())
		{
			return false;
		}

		return array_reduce(
			$sessions,
			function($value, $session)
			{
				$duration = $session->getDurationDays();

				if (!$duration)
				{
					return $value;
				}

				return $value ? min($value, $duration) : $duration;
			}
		);
	}

	/**
	 * Return signup form
	 *
	 * @return string
	 */
	public function getSignupform()
	{
		$helper = new \RedeventTagsRegistrationEvent($this->entity->id);

		return $helper->getHtml();
	}

	/**
	 * Return signup url
	 *
	 * @return string
	 */
	public function getSignuplink()
	{
		return \JRoute::_(\RedeventHelperRoute::getSignupRoute('webform', $this->entity->id));
	}

	/**
	 * Return sessions
	 *
	 * @param   int     $published  publish state
	 * @param   string  $ordering   ordering
	 * @param   bool    $featured   filtered featured
	 *
	 * @return array|bool
	 */
	public function getSessions($published = 1, $ordering = 'dates.asc', $featured = false)
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__redevent_event_venue_xref')
			->where('eventid = ' . $this->entity->id);

		switch ($ordering)
		{
			case 'dates.desc':
				$query->order('dates DESC, times DESC');
				break;
			case 'dates.asc':
			default:
				$query->order('dates ASC, times ASC');
		}

		if (is_numeric($published))
		{
			$query->where('published = ' . $published);
		}

		if ($featured)
		{
			$query->where('featured = 1');
		}

		$db->setQuery($query);

		if (!$res = $db->loadObjectList())
		{
			return false;
		}

		return \RedeventEntitySession::loadArray($res);
	}
}
