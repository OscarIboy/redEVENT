<?php
/**
 * @package    Redevent.Site
 *
 * @copyright  Copyright (C) 2008 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die( 'Restricted access' );

defined( '_JEXEC' ) or die( 'Restricted access' );

echo RLayoutHelper::render(
	'sessionlist.table',
	array(
		'params' => $this->params,
		'columns' => $this->columns,
		'customs' => $this->customs,
		'rows' => $this->rows,
		'print' => true
	)
);
