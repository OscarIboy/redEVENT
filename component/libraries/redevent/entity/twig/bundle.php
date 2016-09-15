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
 * redEVENT Bundle Twig Entity.
 *
 * @since  3.2.0
 */
final class RedeventEntityTwigBundle extends AbstractTwigEntity
{
	/**
	 * Constructor.
	 *
	 * @param   \RedeventEntityBundle  $entity  The entity
	 */
	public function __construct(\RedeventEntityBundle $entity)
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

		throw new RuntimeException('unsupported property in __get: ' . $name);
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
	 * is triggered when invoking inaccessible methods in an object context.
	 *
	 * @param   method  $name       method name
	 * @param   array   $arguments  arguments
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		if (is_callable(array($this->entity, 'get' . ucfirst($name))))
		{
			return call_user_func_array(array($this->entity, 'get' . ucfirst($name)), $arguments);
		}
	}

	/**
	 * Get next session
	 *
	 * @return RedeventEntityTwigSession
	 */
	public function getNext()
	{
		if ($session = $this->entity->getNextSession())
		{
			return new RedeventEntityTwigSession($session);
		}

		return false;
	}
}
