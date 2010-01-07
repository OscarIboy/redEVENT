<?php
/**
 * @version 1.0 $Id: eventlist.php 1180 2009-10-13 18:43:13Z julien $
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
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

require_once('baseeventslist.php');

/**
 * Redevents Component my events Model
 *
 * @package Joomla
 * @subpackage Redevent
 * @since   2.0
 */
class RedeventModelMyevents extends RedeventModelBaseEventList
{
    /**
     * Events data array
     *
     * @var array
     */
    var $_events = null;

    /**
     * Events total
     *
     * @var integer
     */
    var $_total_events = null;

    var $_venues = null;

    var $_total_venues = null;

    var $_attending = null;

    var $_total_attending = null;

    var $_groups = null;
    
    /**
     * Pagination object
     *
     * @var object
     */
    var $_pagination_events = null;

    /**
     * Pagination object
     *
     * @var object
     */
    var $_pagination_venues = null;

    /**
     * Constructor
     *
     * @since 0.9
     */
    function __construct()
    {
        parent::__construct();

        global $mainframe;

        // Get the paramaters of the active menu item
        $params = & $mainframe->getParams('com_redevent');

        //get the number of events from database
        $limit 					= $mainframe->getUserStateFromRequest('com_redevent.myevents.limit', 'limit', $params->def('display_num', 0), 'int');
        $limitstart_events 		= JRequest::getVar('limitstart_events', 0, '', 'int');
        $limitstart_venues 		= JRequest::getVar('limitstart_venues', 0, '', 'int');
        $limitstart_attending 	= JRequest::getVar('limitstart_attending', 0, '', 'int');

        $this->setState('limit', $limit);
        $this->setState('limitstart_events', $limitstart_events);
        $this->setState('limitstart_venues', $limitstart_venues);
        $this->setState('limitstart_attending', $limitstart_attending);

        // Get the filter request variables
        $this->setState('filter_order', JRequest::getCmd('filter_order', 'x.dates'));
        $this->setState('filter_order_dir', JRequest::getCmd('filter_order_Dir', 'ASC'));
    }

    /**
     * Method to get the Events
     *
     * @access public
     * @return array
     */
    function & getEvents()
    {
        $pop = JRequest::getBool('pop');

        // Lets load the content if it doesn't already exist
        if ( empty($this->_events))
        {
            $query = $this->_buildQueryEvents();
            $pagination = $this->getEventsPagination();

            if ($pop)
            {
                $this->_events = $this->_getList($query);
            } else
            {
                $this->_events = $this->_getList($query, $pagination->limitstart, $pagination->limit);
            }
            $this->_events = $this->_categories($this->_events);
        }

        return $this->_events;
    }

    /**
     * Method to get the Events user is attending
     *
     * @access public
     * @return array
     */
    function & getAttending()
    {
        $pop = JRequest::getBool('pop');

        // Lets load the content if it doesn't already exist
        if ( empty($this->_attending))
        {
            $query = $this->_buildQueryAttending();
            $pagination = $this->getAttendingPagination();

            if ($pop)
            {
                $this->_attending = $this->_getList($query);
            } else
            {
                $this->_attending = $this->_getList($query, $pagination->limitstart, $pagination->limit);
            }
        }
        $this->_attending = $this->_categories($this->_attending);
        return $this->_attending;
    }

    /**
     * Method to get the Venues
     *
     * @access public
     * @return array
     */
    function & getVenues()
    {
        $pop = JRequest::getBool('pop');

        // Lets load the content if it doesn't already exist
        if ( empty($this->_venues))
        {
            $query = $this->_buildQueryVenues();
            $pagination = $this->getVenuesPagination();

            if ($pop)
            {
                $this->_venues = $this->_getList($query);
            } else
            {
                $this->_venues = $this->_getList($query, $pagination->limitstart, $pagination->limit);
            }
        }

        return $this->_venues;
    }

    /**
     * Method to get the Venues
     *
     * @access public
     * @return array
     */
    function & getGroups()
    {
        // Lets load the content if it doesn't already exist
        if ( empty($this->_groups))
        {
            $query = $this->_buildQueryGroups();
            $this->_groups = $this->_getList($query);
        }

        return $this->_groups;
    }
    
    /**
     * Total nr of events
     *
     * @access public
     * @return integer
     */
    function getTotalEvents()
    {
        // Lets load the total nr if it doesn't already exist
        if ( empty($this->_total_events))
        {
            $query = $this->_buildQueryEvents();
            $this->_total_events = $this->_getListCount($query);
        }

        return $this->_total_events;
    }

    /**
     * Total nr of events
     *
     * @access public
     * @return integer
     */
    function getTotalAttending()
    {
        // Lets load the total nr if it doesn't already exist
        if ( empty($this->_total_attending))
        {
            $query = $this->_buildQueryAttending();
            $this->_total_attending = $this->_getListCount($query);
        }

        return $this->_total_attending;
    }

    /**
     * Total nr of events
     *
     * @access public
     * @return integer
     */
    function getTotalVenues()
    {
        // Lets load the total nr if it doesn't already exist
        if ( empty($this->_total_venues))
        {
            $query = $this->_buildQueryVenues();
            $this->_total_venues = $this->_getListCount($query);
        }

        return $this->_total_venues;
    }

    /**
     * Method to get a pagination object for the events
     *
     * @access public
     * @return integer
     */
    function getEventsPagination()
    {
        // Lets load the content if it doesn't already exist
        if ( empty($this->_pagination_events))
        {
            jimport('joomla.html.pagination');
            $this->_pagination_events = new MyEventsPagination($this->getTotalEvents(), $this->getState('limitstart_events'), $this->getState('limit'));
        }

        return $this->_pagination_events;
    }

    /**
     * Method to get a pagination object for the venues
     *
     * @access public
     * @return integer
     */
    function getVenuesPagination()
    {
        // Lets load the content if it doesn't already exist
        if ( empty($this->_pagination_venues))
        {
            jimport('joomla.html.pagination');
            $this->_pagination_venues = new MyVenuesPagination($this->getTotalVenues(), $this->getState('limitstart_venues'), $this->getState('limit'));
        }

        return $this->_pagination_venues;
    }

    /**
     * Method to get a pagination object for the attending events
     *
     * @access public
     * @return integer
     */
    function getAttendingPagination()
    {
        // Lets load the content if it doesn't already exist
        if ( empty($this->_pagination_attending))
        {
            jimport('joomla.html.pagination');
            $this->_pagination_attending = new MyAttendingPagination($this->getTotalAttending(), $this->getState('limitstart_attending'), $this->getState('limit'));
        }

        return $this->_pagination_attending;
    }

    /**
     * Build the query
     *
     * @access private
     * @return string
     */
    function _buildQueryEvents()
    {
        // Get the WHERE and ORDER BY clauses for the query
        $where = $this->_buildEventListWhere();
        $orderby = $this->_buildEventListOrderBy();

        //Get Events from Database        
        $query = 'SELECT x.dates, x.enddates, x.times, x.endtimes, x.registrationend, x.id AS xref, x.maxattendees, x.maxwaitinglist, x.published, '
        . ' a.id, a.title, a.created, a.datdescription, a.registra, '
        . ' l.venue, l.city, l.state, l.url, l.id as locid, '
        . ' c.catname, c.id AS catid,'
        . ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
        . ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', l.id, l.alias) ELSE l.id END as venueslug, '
        . ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as categoryslug '
        . ' FROM #__redevent_event_venue_xref AS x'
        . ' LEFT JOIN #__redevent_events AS a ON a.id = x.eventid'
        . ' LEFT JOIN #__redevent_venues AS l ON l.id = x.venueid'
        . ' LEFT JOIN #__redevent_event_category_xref AS xcat ON xcat.event_id = a.id'
        . ' LEFT JOIN #__redevent_categories AS c ON c.id = xcat.category_id'
        . ' LEFT JOIN #__redevent_groups AS g ON g.id = x.groupid '
        . ' LEFT JOIN #__redevent_groupmembers AS gm ON gm.group_id = g.id '
        . $where
        . ' GROUP BY (x.id) '
        . $orderby
        ;
        
        return $query;
    }

    /**
     * Build the query
     *
     * @access private
     * @return string
     */
    function _buildQueryAttending()
    {
        // Get the WHERE and ORDER BY clauses for the query
        $where = $this->_buildEventListAttendingWhere();
        $orderby = $this->_buildEventListOrderBy();

        //Get Events from Database
        $query = 'SELECT x.dates, x.enddates, x.times, x.endtimes, x.registrationend, x.id AS xref, x.maxattendees, x.maxwaitinglist, '
        . ' a.id, a.title, a.created, a.datdescription, a.registra, '
        . ' l.venue, l.city, l.state, l.url, l.id as locid, '
        . ' c.catname, c.id AS catid,'
        . ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
        . ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', l.id, l.alias) ELSE l.id END as venueslug, '
        . ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as categoryslug '
        . ' FROM #__redevent_event_venue_xref AS x'
        . ' INNER JOIN #__redevent_register AS r ON r.xref = x.id '
        . ' LEFT JOIN #__redevent_events AS a ON a.id = x.eventid'
        . ' LEFT JOIN #__redevent_venues AS l ON l.id = x.venueid'
        . ' LEFT JOIN #__redevent_event_category_xref AS xcat ON xcat.event_id = a.id'
        . ' LEFT JOIN #__redevent_categories AS c ON c.id = xcat.category_id'
        . $where
        . ' GROUP BY (x.id) '
        . $orderby
        ;

        return $query;
    }

    /**
     * Build the query
     *
     * @access private
     * @return string
     */
    function _buildQueryVenues()
    {
        $user = & JFactory::getUser();
        //Get Events from Database
        $query = 'SELECT l.id, l.venue, l.city, l.state, l.url, l.published, '
        . ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', l.id, l.alias) ELSE l.id END as venueslug'
        . ' FROM #__redevent_venues AS l '
        . ' LEFT JOIN #__redevent_groups_venues AS gv ON gv.venue_id = l.id '
        . ' LEFT JOIN #__redevent_groups AS g ON g.id = gv.group_id '
        . ' LEFT JOIN #__redevent_groupmembers AS gm ON gm.group_id = g.id '
        . ' WHERE (l.created_by = '.$this->_db->Quote($user->id) .' OR (gm.member = '. $this->_db->Quote($user->id) .' AND gv.accesslevel > 0 AND gm.edit_venues = 1))'
        . ' GROUP BY (l.id) '
        .' ORDER BY l.venue ASC '
        ;

        return $query;
    }

    /**
     * Build the query
     *
     * @access private
     * @return string
     */
    function _buildQueryGroups()
    {
        $user = & JFactory::getUser();
        //Get Events from Database
        $query = 'SELECT g.id, g.name '
        . ' FROM #__redevent_groups AS g '
        . ' INNER JOIN #__redevent_groupmembers AS gm ON gm.group_id = g.id '
        .' WHERE gm.member = '. $this->_db->Quote($user->id)
        .' ORDER BY g.name ASC '
        ;

        return $query;
    }
    
    /**
     * Build the order clause
     *
     * @access private
     * @return string
     */
    function _buildEventListOrderBy()
    {
        $filter_order = $this->getState('filter_order');
        $filter_order_dir = $this->getState('filter_order_dir');

        $orderby = ' ORDER BY '.$filter_order.' '.$filter_order_dir.', x.dates, x.times';

        return $orderby;
    }

    /**
     * Build the where clause
     *
     * @access private
     * @return string
     */
    function _buildEventListWhere()
    {
        global $mainframe;

        $user = & JFactory::getUser();
        $gid = (int)$user->get('aid');

        // Get the paramaters of the active menu item
        $params = & $mainframe->getParams();

        $task = JRequest::getWord('task');

        // First thing we need to do is to select only needed events
        if ($task == 'archive')
        {
            $where = ' WHERE x.published = -1';
        } else
        {
            $where = ' WHERE x.published > -1';
        }

        // then if the user is the owner of the event or member of admin group
        $where .= ' AND (a.created_by = '. $this->_db->Quote($user->id). ' OR (gm.member = '. $this->_db->Quote($user->id) .' AND gm.add_xrefs > 0)) ';

        // Second is to only select events assigned to category the user has access to
        $where .= ' AND c.access <= '.$gid;

        /*
         * If we have a filter, and this is enabled... lets tack the AND clause
         * for the filter onto the WHERE clause of the item query.
         */
        if ($params->get('filter'))
        {
            $filter = JRequest::getString('filter', '', 'request');
            $filter_type = JRequest::getWord('filter_type', '', 'request');

            if ($filter)
            {
                // clean filter variables
                $filter = JString::strtolower($filter);
                $filter = $this->_db->Quote('%'.$this->_db->getEscaped($filter, true).'%', false);
                $filter_type = JString::strtolower($filter_type);

                switch($filter_type)
                {
                    case 'title':
                        $where .= ' AND LOWER( a.title ) LIKE '.$filter;
                        break;

                    case 'venue':
                        $where .= ' AND LOWER( l.venue ) LIKE '.$filter;
                        break;

                    case 'city':
                        $where .= ' AND LOWER( l.city ) LIKE '.$filter;
                        break;

                    case 'type':
                        $where .= ' AND LOWER( c.catname ) LIKE '.$filter;
                        break;
                }
            }
        }
        return $where;
    }

    /**
     * Build the where clause
     *
     * @access private
     * @return string
     */
    function _buildEventListAttendingWhere()
    {
        global $mainframe;

        $user = & JFactory::getUser();
        $gid = (int)$user->get('aid');

        // Get the paramaters of the active menu item
        $params = & $mainframe->getParams();

        $task = JRequest::getWord('task');

        // First thing we need to do is to select only needed events
        if ($task == 'archive')
        {
            $where = ' WHERE x.published = -1';
        } else
        {
            $where = ' WHERE x.published = 1';
        }

        // then if the user is attending the event
        $where .= ' AND r.uid = '.$this->_db->Quote($user->id);

        // Second is to only select events assigned to category the user has access to
        $where .= ' AND c.access <= '.$gid;

        return $where;
    }    

  /**
   * check if user is allowed to addxrefs
   * @return boolean
   */
	function getCanAddXref()
  {
  	$user = & JFactory::getUser();
  	
  	$query = ' SELECT gm.id '
  	       . ' FROM #__redevent_groups AS g '
  	       . ' INNER JOIN #__redevent_groupmembers AS gm ON gm.group_id = g.id '
  	       . ' WHERE gm.member = '. $this->_db->Quote($user->get('id'))
  	       . '   AND (gm.add_xrefs > 0 OR gm.add_events > 0) '
  	       ;
  	$this->_db->setQuery($query);
  	$res = $this->_db->loadObjectList();
  	return count($res) > 0;
  } 
}

class MyEventsPagination extends JPagination
{
    /**
     * Create and return the pagination data object
     *
     * @access  public
     * @return  object  Pagination data object
     * @since 1.5
     */
    function _buildDataObject()
    {
        // Initialize variables
        $data = new stdClass ();

        $data->all = new JPaginationObject(JText::_('View All'));
        if (!$this->_viewall)
        {
            $data->all->base = '0';
            $data->all->link = JRoute::_("&limitstart_events=");
        }

        // Set the start and previous data objects
        $data->start = new JPaginationObject(JText::_('Start'));
        $data->previous = new JPaginationObject(JText::_('Prev'));

        if ($this->get('pages.current') > 1)
        {
            $page = ($this->get('pages.current')-2)*$this->limit;

            $page = $page == 0?'':$page; //set the empty for removal from route

            $data->start->base = '0';
            $data->start->link = JRoute::_("&limitstart_events=");
            $data->previous->base = $page;
            $data->previous->link = JRoute::_("&limitstart_events=".$page);
        }

        // Set the next and end data objects
        $data->next = new JPaginationObject(JText::_('Next'));
        $data->end = new JPaginationObject(JText::_('End'));

        if ($this->get('pages.current') < $this->get('pages.total'))
        {
            $next = $this->get('pages.current')*$this->limit;
            $end = ($this->get('pages.total')-1)*$this->limit;

            $data->next->base = $next;
            $data->next->link = JRoute::_("&limitstart_events=".$next);
            $data->end->base = $end;
            $data->end->link = JRoute::_("&limitstart_events=".$end);
        }

        $data->pages = array ();
        $stop = $this->get('pages.stop');
        for ($i = $this->get('pages.start'); $i <= $stop; $i++)
        {
            $offset = ($i-1)*$this->limit;

            $offset = $offset == 0?'':$offset; //set the empty for removal from route

            $data->pages[$i] = new JPaginationObject($i);
            if ($i != $this->get('pages.current') || $this->_viewall)
            {
                $data->pages[$i]->base = $offset;
                $data->pages[$i]->link = JRoute::_("&limitstart_events=".$offset);
            }
        }
        return $data;
    }

    function _list_footer($list)
    {
        // Initialize variables
        $html = "<div class=\"list-footer\">\n";

        $html .= "\n<div class=\"limit\">".JText::_('Display Num').$list['limitfield']."</div>";
        $html .= $list['pageslinks'];
        $html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";

        $html .= "\n<input type=\"hidden\" name=\"limitstart_events\" value=\"".$list['limitstart']."\" />";
        $html .= "\n</div>";

        return $html;
    }

}


class MyAttendingPagination extends JPagination
{

    /**
     * Create and return the pagination data object
     *
     * @access  public
     * @return  object  Pagination data object
     * @since 1.5
     */
    function _buildDataObject()
    {
        // Initialize variables
        $data = new stdClass ();

        $data->all = new JPaginationObject(JText::_('View All'));
        if (!$this->_viewall)
        {
            $data->all->base = '0';
            $data->all->link = JRoute::_("&limitstart_attending=");
        }

        // Set the start and previous data objects
        $data->start = new JPaginationObject(JText::_('Start'));
        $data->previous = new JPaginationObject(JText::_('Prev'));

        if ($this->get('pages.current') > 1)
        {
            $page = ($this->get('pages.current')-2)*$this->limit;

            $page = $page == 0?'':$page; //set the empty for removal from route

            $data->start->base = '0';
            $data->start->link = JRoute::_("&limitstart_attending=");
            $data->previous->base = $page;
            $data->previous->link = JRoute::_("&limitstart_attending=".$page);
        }

        // Set the next and end data objects
        $data->next = new JPaginationObject(JText::_('Next'));
        $data->end = new JPaginationObject(JText::_('End'));

        if ($this->get('pages.current') < $this->get('pages.total'))
        {
            $next = $this->get('pages.current')*$this->limit;
            $end = ($this->get('pages.total')-1)*$this->limit;

            $data->next->base = $next;
            $data->next->link = JRoute::_("&limitstart_attending=".$next);
            $data->end->base = $end;
            $data->end->link = JRoute::_("&limitstart_attending=".$end);
        }

        $data->pages = array ();
        $stop = $this->get('pages.stop');
        for ($i = $this->get('pages.start'); $i <= $stop; $i++)
        {
            $offset = ($i-1)*$this->limit;

            $offset = $offset == 0?'':$offset; //set the empty for removal from route

            $data->pages[$i] = new JPaginationObject($i);
            if ($i != $this->get('pages.current') || $this->_viewall)
            {
                $data->pages[$i]->base = $offset;
                $data->pages[$i]->link = JRoute::_("&limitstart_attending=".$offset);
            }
        }
        return $data;
    }

    function _list_footer($list)
    {
        // Initialize variables
        $html = "<div class=\"list-footer\">\n";

        $html .= "\n<div class=\"limit\">".JText::_('Display Num').$list['limitfield']."</div>";
        $html .= $list['pageslinks'];
        $html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";

        $html .= "\n<input type=\"hidden\" name=\"limitstart_attending\" value=\"".$list['limitstart']."\" />";
        $html .= "\n</div>";

        return $html;
    }

}

class MyVenuesPagination extends JPagination
{

    /**
     * Create and return the pagination data object
     *
     * @access  public
     * @return  object  Pagination data object
     * @since 1.5
     */
    function _buildDataObject()
    {
        // Initialize variables
        $data = new stdClass ();

        $data->all = new JPaginationObject(JText::_('View All'));
        if (!$this->_viewall)
        {
            $data->all->base = '0';
            $data->all->link = JRoute::_("&limitstart_venues=");
        }

        // Set the start and previous data objects
        $data->start = new JPaginationObject(JText::_('Start'));
        $data->previous = new JPaginationObject(JText::_('Prev'));

        if ($this->get('pages.current') > 1)
        {
            $page = ($this->get('pages.current')-2)*$this->limit;

            $page = $page == 0?'':$page; //set the empty for removal from route

            $data->start->base = '0';
            $data->start->link = JRoute::_("&limitstart_venues=");
            $data->previous->base = $page;
            $data->previous->link = JRoute::_("&limitstart_venues=".$page);
        }

        // Set the next and end data objects
        $data->next = new JPaginationObject(JText::_('Next'));
        $data->end = new JPaginationObject(JText::_('End'));

        if ($this->get('pages.current') < $this->get('pages.total'))
        {
            $next = $this->get('pages.current')*$this->limit;
            $end = ($this->get('pages.total')-1)*$this->limit;

            $data->next->base = $next;
            $data->next->link = JRoute::_("&limitstart_venues=".$next);
            $data->end->base = $end;
            $data->end->link = JRoute::_("&limitstart_venues=".$end);
        }

        $data->pages = array ();
        $stop = $this->get('pages.stop');
        for ($i = $this->get('pages.start'); $i <= $stop; $i++)
        {
            $offset = ($i-1)*$this->limit;

            $offset = $offset == 0?'':$offset; //set the empty for removal from route

            $data->pages[$i] = new JPaginationObject($i);
            if ($i != $this->get('pages.current') || $this->_viewall)
            {
                $data->pages[$i]->base = $offset;
                $data->pages[$i]->link = JRoute::_("&limitstart_venues=".$offset);
            }
        }
        return $data;
    }

    function _list_footer($list)
    {
        // Initialize variables
        $html = "<div class=\"list-footer\">\n";

        $html .= "\n<div class=\"limit\">".JText::_('Display Num').$list['limitfield']."</div>";
        $html .= $list['pageslinks'];
        $html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";

        $html .= "\n<input type=\"hidden\" name=\"limitstart_venues\" value=\"".$list['limitstart']."\" />";
        $html .= "\n</div>";

        return $html;
    }
}
?>
