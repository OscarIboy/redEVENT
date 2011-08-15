<?php
/**
 * @version 2.0
 * @package Joomla
 * @subpackage RedEvent search module
 * @copyright (C) 2011 redCOMPONENT.com
 * @license GNU/GPL, see LICENCE.php
 * RedEvent is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * RedEvent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with RedEvent; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_redevent'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_redevent'.DS.'helpers'.DS.'customfields.php');

/**
 * RedEvent Module Search helper
 *
 * @package Joomla
 * @subpackage RedEvent search Module
 * @since		0.9
 */
class modRedEventSearchHelper
{	
	private $_db = null;
	
	public function __construct()
	{
		$this->_db = &Jfactory::getDBO();
	}
	
	/**
	 * get list of categories as options, according to acl
	 * 
	 * @return array
	 */
	function getCategoriesOptions()
	{
		$app = &JFactory::getApplication();
		
		$acl = &UserAcl::getInstance();		
		$gids = $acl->getUserGroupsIds();
		if (!is_array($gids) || !count($gids)) {
			$gids = array(0);
		}
		$gids = implode(',', $gids);
			
		//Get Events from Database
		$query  = ' SELECT c.id '
		        . ' FROM #__redevent_event_venue_xref AS x'
		        . ' INNER JOIN #__redevent_events AS a ON a.id = x.eventid'
		        . ' INNER JOIN #__redevent_venues AS l ON l.id = x.venueid'
		        . ' LEFT JOIN #__redevent_venue_category_xref AS xvcat ON l.id = xvcat.venue_id'
		        . ' LEFT JOIN #__redevent_venues_categories AS vc ON xvcat.category_id = vc.id'
            . ' INNER JOIN #__redevent_event_category_xref AS xcat ON xcat.event_id = a.id'
	          . ' INNER JOIN #__redevent_categories AS c ON c.id = xcat.category_id'
	          
	          . ' LEFT JOIN #__redevent_groups_venues AS gv ON gv.venue_id = l.id AND gv.group_id IN ('.$gids.')'
	          . ' LEFT JOIN #__redevent_groups_venues_categories AS gvc ON gvc.category_id = vc.id AND gvc.group_id IN ('.$gids.')'
	          . ' LEFT JOIN #__redevent_groups_categories AS gc ON gc.category_id = c.id AND gc.group_id IN ('.$gids.')'
		        ;	
		
		$where = array();		
		$where[] = ' x.published = 1';
		
    //acl
		$where[] = ' (l.private = 0 OR gv.id IS NOT NULL) ';
		$where[] = ' (c.private = 0 OR gc.id IS NOT NULL) ';
		$where[] = ' (vc.private = 0 OR vc.private IS NULL OR gvc.id IS NOT NULL) ';
    
    if (count($where)) {
    	$query .= ' WHERE '. implode(' AND ', $where);
    }
    $query .= ' GROUP BY c.id ';
    
		$this->_db->setQuery($query);
		$res = $this->_db->loadResultArray();
		
		return redEVENTHelper::getEventsCatOptions(true, false, $res);
	}
	
  /**
	 * get venues options

	 * @return array
	 */
	function getVenuesOptions()
	{
		$app = &JFactory::getApplication();
		
		$acl = &UserAcl::getInstance();		
		$gids = $acl->getUserGroupsIds();
		if (!is_array($gids) || !count($gids)) {
			$gids = array(0);
		}
		$gids = implode(',', $gids);
		
		$query = ' SELECT DISTINCT v.id AS value, '
           . ' CASE WHEN CHAR_LENGTH(v.city) AND v.city <> v.venue THEN CONCAT_WS(\' - \', v.venue, v.city) ELSE v.venue END as text '
		       . ' FROM #__redevent_venues AS v '
		       . ' LEFT JOIN #__redevent_venue_category_xref AS xcat ON xcat.venue_id = v.id '
		       . ' LEFT JOIN #__redevent_venues_categories AS vcat ON vcat.id = xcat.category_id '
		       
		       . ' LEFT JOIN #__redevent_groups_venues AS gv ON gv.venue_id = v.id AND gv.group_id IN ('.$gids.')'
		       . ' LEFT JOIN #__redevent_groups_venues_categories AS gvc ON gvc.category_id = vcat.id AND gvc.group_id IN ('.$gids.')'
		       ;
		$where = array();
		
    //acl
		$where[] = ' (v.private = 0 OR gv.id IS NOT NULL) ';
		$where[] = ' (vcat.id IS NULL OR vcat.private = 0 OR gvc.id IS NOT NULL) ';
		
    if (count($where)) {
    	$query .= ' WHERE '. implode(' AND ', $where);
    }
    $query .= ' ORDER BY v.venue ';
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		return $res;
	}
	
	function getCustomFilters()
	{		
		$query = ' SELECT f.* FROM #__redevent_fields AS f '
           . ' WHERE f.published = 1 '
           . '   AND f.searchable = 1 '
//           . '   AND f.object_key = '. $this->_db->Quote("redevent.event")
           . ' ORDER BY f.ordering ASC '
           ;
    $this->_db->setQuery($query);
    $rows = $this->_db->loadObjectList();
    
    $filters = array();
    foreach ($rows as $r) {
    	$field = redEVENTcustomHelper::getCustomField($r->type);
    	$field->bind($r);
    	$filters[] = $field;
    }
    return $filters;
	}
}