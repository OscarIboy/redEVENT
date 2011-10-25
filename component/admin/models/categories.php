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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * EventList Component Categories Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class RedEventModelCategories extends JModel
{
	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Categorie id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

		$limit		= $mainframe->getUserStateFromRequest( $option.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

	}

	/**
	 * Method to set the category identifier
	 *
	 * @access	public
	 * @param	int Category identifier
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id	 = $id;
		$this->_data = null;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			
			$k = 0;
			$count = count($this->_data);
			for($i = 0; $i < $count; $i++)
			{
				$category =& $this->_data[$i];

				$category->assignedevents = $this->_countcatevents( $category->id );

				$k = 1 - $k;
			}
		}

		return $this->_data;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the categories
	 *
	 * @access private
	 * @return integer
	 * @since 0.9
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

    $query = ' SELECT c.*, c.catname, (COUNT(parent.catname) - 1) AS depth, c.access, c.groupid, '
           . ' u.name AS editor, g.name AS groupname, gr.name AS catgroup, p.catname as parent_name '
           . ' FROM #__redevent_categories AS parent, #__redevent_categories AS c '
           . ' LEFT JOIN #__redevent_categories AS p ON c.parent_id = p.id '
           . ' LEFT JOIN #__groups AS g ON g.id = c.access'
           . ' LEFT JOIN #__users AS u ON u.id = c.checked_out'
           . ' LEFT JOIN #__redevent_groups AS gr ON gr.id = c.groupid'
           . $where
           . ' GROUP BY c.id '
           . $orderby
           ;
           
		return $query;
	}

	/**
	 * Method to build the orderby clause of the query for the categories
	 *
	 * @access private
	 * @return string
	 * @since 0.9
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_order', 		'filter_order', 	'c.lft', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.categories.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', c.ordering';

		return $orderby;
	}

	/**
	 * Method to build the where clause of the query for the categories
	 *
	 * @access private
	 * @return string
	 * @since 0.9
	 */
	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.categories.search', 'search', '', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();
    $where[] = 'c.lft BETWEEN parent.lft AND parent.rgt';

		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'c.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'c.published = 0';
			}
		}

		if ($search) {
			$where[] = ' LOWER(c.catname) LIKE \'%'.$search.'%\' ';
		}

		$where 		= ' WHERE '. implode( ' AND ', $where );

		return $where;
	}

	/**
	 * Method to (un)publish a category
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__redevent_categories'
				. ' SET published = ' . (int) $publish
				. ' WHERE id IN ('. $cids .')'
				. ' AND ( checked_out = 0 OR ( checked_out = ' . (int) $user->get('id'). ' ) )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to move a category
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function move($direction)
	{
		$row =& JTable::getInstance('redevent_categories', '');

		if (!$row->load( $this->_id ) ) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to order categories
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function saveorder($cid = array(), $order)
	{
		$row =& JTable::getInstance('redevent_categories', '');

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}
	
	/**
	 * Method to count the nr of assigned events to the category
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function _countcatevents($id)
	{
		$query = 'SELECT COUNT( * )'
				.' FROM #__redevent_event_category_xref AS x'
				.' WHERE x.category_id = ' . (int)$id
				;
					
		$this->_db->setQuery($query);
		$number = $this->_db->loadResult();
    	
    	return $number;
	}
	

	/**
	 * Method to remove a category
	 *
	 * @access	public
	 * @return	string $msg
	 * @since	0.9
	 */
	function delete($cid)
	{
		$cids = implode( ',', $cid );

		$query = 'SELECT c.id, c.catname, COUNT( xcat.event_id ) AS numcat'
				. ' FROM #__redevent_categories AS c'
        . ' LEFT JOIN #__redevent_event_category_xref AS xcat ON xcat.category_id = c.id'
				. ' WHERE c.id IN ('. $cids .')'
				. ' GROUP BY c.id'
				;
		$this->_db->setQuery( $query );

		if (!($rows = $this->_db->loadObjectList())) {
			RedeventError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		$err = array();
		$cid = array();
		foreach ($rows as $row) {
			if ($row->numcat == 0) {
				$cid[] = $row->id;
			} else {
				$err[] = $row->catname;
			}
		}

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__redevent_categories'
					. ' WHERE id IN ('. $cids .')';

			$this->_db->setQuery( $query );

			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			// rebuild the tree
      $table = JTable::getInstance('redevent_categories', '');
      $table->rebuildTree();
		}

		if (count( $err )) {
			$cids 	= implode( ', ', $err );
    		$msg 	= JText::sprintf( 'COM_REDEVENT_EVENT_ASSIGNED_CATEGORY_S', $cids );
    		return $msg;
		} else {
			$total 	= count( $cid );
			$msg 	= $total.' '.JText::_('COM_REDEVENT_CATEGORIES_DELETED');
			return $msg;
		}
	}
	
  /**
	 * export venues
   *
	 * @param array $categories filter
	 * @return array
	 */
	public function export($categories = null)
	{
		$where = array();
		
		if (count($where)) {
			$where = ' WHERE '.implode(' AND ', $where);
		}
		else {
			$where = '';
		}
				
		$query = ' SELECT c.id, c.catname, c.alias, c.catdescription, c.meta_description, c.meta_keywords,  '
		       . ' c.color, c.image, c.private, c.published, c.access,  '
		       . ' c.groupid, c.event_template, c.ordering  '
		       . ' FROM #__redevent_categories AS c '
		       . $where
		       ;
    $this->_db->setQuery($query);
    
    $results = $this->_db->loadAssocList();
    
    return $results;
	}
	
  /**
	 * import categories in database
	 * 
	 * @param array $records
	 * @param boolean $replace existing categories with same id
	 * @return boolean true on success
	 */
	public function import($records, $replace = 0)
	{
		$count = array('added' => 0, 'updated' => 0);
		
		$current = null; // current event for sessions
		foreach ($records as $r)
		{			
			$v = Jtable::getInstance('RedEvent_categories', '');	
			$v->bind($r);
			if (!$replace) {
				$v->id = null;
				$update = 0;
			}
			else if ($v->id) {
				$update = 1;
			}
			// store !
			if (!$v->check()) {
				JError::raiseWarning(0, JText::_('COM_REDEVENT_IMPORT_ERROR').': '.$v->getError());
				continue;
			}
			if (!$v->store()) {
				JError::raiseWarning(0, JText::_('COM_REDEVENT_IMPORT_ERROR').': '.$v->getError());
				continue;
			}
		}
		return $count;
	}
}
?>