<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Redevent Component Controller
 *
 * @package Joomla
 * @subpackage redEVENT
 * @since 0.9
*/
class RedeventControllerFront extends JControllerLegacy
{

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  A JController object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// If filter is set, put the filter values as get variable so that the user can go back without warning
		$this->checkfilter();

		$view = $this->input->get('view', '');

		$method = '_display' . ucfirst($view);

		if (method_exists($this, $method))
		{
			return $this->$method();
		}

		$input = JFactory::getApplication()->input;

		// Default list layout for certain views
		switch ($view)
		{
			case 'categoryevents':
			case 'simplelist':
			case 'venueevents':
				if (!$input->get('layout'))
				{
					$input->def('layout', JFactory::getApplication()->getParams('com_redevent')->get('default_list_layout', 'table'));
				}
				break;

			case 'frontadmin':
				if (!$input->get('tmpl'))
				{
					$input->set('tmpl', 'component');
				}
				break;
		}

		parent::display();
	}

	/**
	 * Check if there are post filter, then redirect to get
	 *
	 * @return void
	 */
	protected function checkfilter()
	{
		$post = $this->input->post->getArray();
		$uri = Jfactory::getUri();

		// Do not modify it if not proper view...
		$myuri = clone($uri);
		$vars = 0;

		foreach ($post as $filter => $v)
		{
			switch ($filter)
			{
				case 'filter_category':
				case 'filter_multicategory':
				case 'filter_venuecategory':
				case 'filter_order':
				case 'filter_order_Dir':
				case 'filter':
				case 'filter_type':
				case 'filter_venue':
				case 'filter_multivenue':
				case 'filter_date':
				case 'layout':
				case 'task':
				case 'limit':
				case 'showfilters':
					if ($v)
					{
						$myuri->setVar($filter, $v);
						$vars++;
					}
					break;

				case 'filtercustom':
					$filt = array();

					foreach ((array) $v as $n => $val)
					{
						if (is_array($val))
						{
							$r = array();

							foreach ($val as $sub)
							{
								if ($sub)
								{
									$r[] = $sub;
								}
							}

							$myuri->setVar("filtercustom[$n]", $r);
						}
						else
						{
							if ($val)
							{
								$filt[$n] = $val;
							}
						}
					}

					if (count($filt))
					{
						$myuri->setVar($filter, $filt);
						$vars++;
					}

					break;
			}
		}

		if ($vars)
		{
			switch ($this->input->get('view', ''))
			{
				case 'categoryevents':
				case 'day':
				case 'featured':
				case 'search':
				case 'simplelist':
				case 'venueevents':
				case 'venuesmap':
				case 'week':
					$this->setRedirect(JRoute::_($myuri->toString(), false));
					$this->redirect();
			}
		}
	}
}