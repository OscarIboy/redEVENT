<?php
/**
 * @package     Redevent.Frontend
 * @subpackage  Plugins
 *
 * @copyright   Copyright (C) 2008 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Load redEVENT library
$redeventLoader = JPATH_LIBRARIES . '/redevent/bootstrap.php';

if (!file_exists($redeventLoader))
{
	throw new Exception(JText::_('COM_REDEVENT_INIT_FAILED'), 404);
}

include_once $redeventLoader;

RedeventBootstrap::bootstrap();

// Import library dependencies
jimport('joomla.plugin.plugin');

include_once('resimplelist/model.php');

class plgContentRESimplelist extends JPlugin {

	protected $_db;

	protected $_customs;

	protected $_model;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   11.1
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	* Plugin that loads events lists within content
	*
	* @param	string	The context of the content being passed to the plugin.
	* @param	object	The article object.  Note $article->text is also available
	* @param	object	The article params
	* @param	int		The 'page' number
	*/
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins/content/resimplelist/resimplelist.css');

		// Do we have matches for the plugin
		if (!preg_match_all('/{RESimplelist([\s]+[^}]*)*}/i', $article->text, $matches, PREG_SET_ORDER))
		{
			return;
		}

		$search  = array();
		$replace = array();

		foreach ($matches as $match)
		{
			$settings = array();

			// Get params
			if (isset($match[1]))
			{
				$match_params = trim($match[1]);

				if (!empty($match_params))
				{
					preg_match_all('/([^=\s]+)=["\']([^"\']*)[\'"]/', $match_params, $match_params_array, PREG_SET_ORDER);

					foreach ($match_params_array as $m)
					{
						$property = strtolower($m[1]);

						if (!isset($settings[$property]))
						{
							$settings[$property] = array();
						}
						$settings[$property][] = $m[2];
					}
				}
			}

			$search[]  = $match[0];
			$replace[] = $this->_getList($settings);
		}

		$article->text = str_replace($search, $replace, $article->text);
	}

	protected function _getList($settings = array())
	{
		$this->_model = new plgReSimplistModel();
		$this->_model->setLimit($this->params->get('max_events', 20));
		$this->_model->setLimitStart(0);

		$filtercustoms = array();
		foreach ($settings as $key => $val)
		{
			switch ($key)
			{
				case 'archived':
					$this->_model->setState($key, 1);
					break;
				case 'featured':
					$this->_model->setState($key, 1);
					break;
				case 'type':
					$this->_model->setState($key, $val[0]);
					break;
				case 'city':
				case 'venueid':
				case 'categoryid':
				case 'eventid':
					// there might be several values separated by comma
					$values = array();
					foreach ($val as $v)
					{
						$parts = explode(",", $v);
						foreach ($parts as $p) {
							$values[] = trim($p);
						}
					}
					$this->_model->setState($key, $values);
					break;
				default:
					if (preg_match("/^custom[0-9+]$/", $key))
					{
						$filtercustoms[$key] = $val[0];
					}
					break;
			}
		}
		if (count($filtercustoms)) {
			$this->_model->setState('customs', $filtercustoms);
		}

		$res = $this->_model->getData();

		if (!$res) {
			return '';
		}

		if (isset($settings['cols']))
		{
			$cols = array();

			foreach(explode(",", $settings['cols'][0]) as $c)
			{
				$cols[] = trim($c);
			}
		}
		else
		{
			$cols = array( 'date', 'title', 'venue', 'city', 'category');
		}

		$i = 0;
		ob_start();
		?>
		<table class="plg_resimplelist" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<?php
				foreach ($cols as $c)
				{
					switch ($c)
					{
						case 'date':
							echo '<th>'.JText::_('PLG_RESIMPLELIST_TABLE_TH_STARTDATE').'</th>';
							break;
						case 'title':
							echo '<th>'.JText::_('PLG_RESIMPLELIST_TABLE_TH_TITLE').'</th>';
							break;
						case 'venue':
							echo '<th>'.JText::_('PLG_RESIMPLELIST_TABLE_TH_VENUE').'</th>';
							break;
						case 'city':
							echo '<th>'.JText::_('PLG_RESIMPLELIST_TABLE_TH_CITY').'</th>';
							break;
						case 'category':
							echo '<th>'.JText::_('PLG_RESIMPLELIST_TABLE_TH_CATEGORY').'</th>';
							break;
						case 'places':
							echo '<th>'.JText::_('PLG_RESIMPLELIST_TABLE_TH_PLACES').'</th>';
							break;
						case 'price':
							echo '<th>'.JText::_('PLG_RESIMPLELIST_TABLE_TH_PRICE').'</th>';
							break;
						default:
							if (strstr($c, 'custom') && $f = $this->_getCustom($c)) {
								echo '<th>'.$f->name.'</th>';
							}
							break;

					}
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($res as $event): ?>
		<?php $link = JRoute::_(RedeventHelperRoute::getDetailsRoute($event->id, $event->xref)); ?>
			<tr class="sectiontableentry<?php echo $i+1; ?>">
				<?php
				foreach ($cols as $c)
				{
					switch ($c)
					{
						case 'date':
							echo '<td>'.$this->_formatEventDateTime($event).'</td>';
							break;
						case 'title':
							echo '<td>'.JHTML::link($link, $event->title).'</td>';
							break;
						case 'venue':
							echo '<td>'.$event->venue.'</td>';
							break;
						case 'city':
							echo '<td>'.$event->city.'</td>';
							break;
						case 'category':
							echo '<td>'.$this->_formatCategories($event).'</td>';
							break;
						case 'places':
							echo '<td>'.RedeventHelper::getRemainingPlaces($event).'</td>';
							break;
						case 'price':
							echo '<td>'.RedeventHelperOutput::formatListPrices($event->prices).'</td>';
							break;
						default:
							if (strstr($c, 'custom') && $f = $this->_getCustom($c)) {
								echo '<td>'.$event->$c.'</td>';
							}
							break;
					}
				}
				?>
			</tr>
			<?php $i = 1 - $i;?>
		<?php endforeach; ?>
		</tbody>
		</table>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * return formatted event date and time (start and end), or false if open date
	 *
	 * @param object $event
	 * @return string or false for open date
	 */
	protected function _formatEventDateTime($event)
	{
		if (!RedeventHelper::isValidDate($event->dates))
		{
			// Open dates
			$date = '<span class="event-date open-date">'.JText::_('PLG_RESIMPLELIST_OPEN_DATE').'</span>';
			return $date;
		}

		// Is this a full day(s) event ?
		$allday = '00:00:00' == $event->times && '00:00:00' == $event->endtimes;

		$date = '<span class="event-date">';
		$date .= '<span class="event-start">';
		$date .= '<span class="event-day">'.self::_formatdate($event->dates, $event->times).'</span>';

		if (!$allday && $this->params->get('show_time', 1))
		{
			$date .= ' <span class="event-time">'.self::_formattime($event->dates, $event->times).'</span>';
		}

		$date .= '</span>';

		if ($allday)
		{
			if ($this->params->get('show_end', 1) &RedeventHelper::isValidDate($event->enddates))
			{
				if (strtotime($event->enddates. ' -1 day') != strtotime($event->dates)
				    && strtotime($event->enddates) != strtotime($event->dates))
				    // All day is written as midnight to midnight, so remove last day
				{
					$date .= ' <span class="event-end"><span class="event-day">'.self::_formatdate(strftime('Y-m-d', strtotime($event->enddates. ' -1 day')), $event->endtimes).'</span></span>';
				}
			}
		}
		elseif ($this->params->get('show_end', 1))
		{
			if (RedeventHelper::isValidDate($event->enddates) && strtotime($event->enddates) != strtotime($event->dates))
			{
				$date .= ' <span class="event-end"><span class="event-day">'.self::_formatdate($event->enddates, $event->endtimes).'</span>';
				if ($this->params->get('show_time', 1)) {
					$date .= ' <span class="event-time">'.self::_formattime($event->dates, $event->endtimes).'</span>';
				}
				$date .= '</span>';
			}
			else if ($this->params->get('show_time', 1))
			{
				$date .= ' <span class="event-time">'.self::_formattime($event->dates, $event->endtimes).'</span>';
			}
		}
		$date .= '</span>';

		return $date;
	}


	/**
	 * Formats date
	 *
	 * @param string $date
	 * @param string $time
	 *
	 * @return string $formatdate
	 *
	 * @since 0.9
	 */
	protected function _formatdate($date, $time)
	{
		if(!RedeventHelper::isValidDate($date))
		{
			return JText::_('OPEN DATE');
		}

		if(!$time)
		{
			$time = '00:00:00';
		}

		//Format date
		$formatdate = strftime($this->params->get('date_format', '%b %d, %Y'), strtotime($date.' '.$time));

		return $formatdate;
	}

	/**
	 * Formats time
	 *
	 * @param string $date
	 * @param string $time
	 *
	 * @return string $formattime
	 *
	 * @since 0.9
	 */
	protected function _formattime($date, $time)
	{
		if(!$time)
		{
			return;
		}

		//Format time
		$formattime = strftime( $this->params->get('time_format', '%H:%I'), strtotime( $date.' '.$time ));

		return $formattime;
	}

	/**
	 * return formatted categories for display
	 *
	 * @param object $event
	 * @return string
	 */
	protected function _formatCategories($event)
	{
		$cats = array();
		foreach ($event->categories as $cat)
		{
			$cats[] = JHTML::link(RedeventHelperRoute::getCategoryEventsRoute($cat->slug), $cat->name);
		}
		return implode("<br/>", $cats);
	}

	/**
	 * return custom field or false if not found
	 *
	 * @param string $dbfield name of field in tables (custom<id>)
	 * @return mixed object field or false
	 */
	protected function _getCustom($dbfield)
	{
		if (is_null($this->_customs)) {
			$this->_customs = $this->_model->getListCustomFields();
		}
		foreach ((array) $this->_customs as $f)
		{
			if ('custom'.$f->id == $dbfield) {
				return $f;
			}
		}
		return false;
	}

}
