<?php
/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 */
function RedEventBuildRoute(&$query)
{
	$segments = array();

	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}

	if(isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	};

	if(isset($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	};

	if(isset($query['returnid']))
	{
		$segments[] = $query['returnid'];
		unset($query['returnid']);
	};

	return $segments;
}

function RedEventParseRoute($segments)
{
	$vars = array();
	//Handle View and Identifier
	switch($segments[0])
	{
		case 'categoryevents':
		{
			$id = explode(':', $segments[1]);
			$vars['id'] = $id[0];
			$vars['view'] = 'categoryevents';

			$count = count($segments);
			if($count > 2) {
				$vars['task'] = $segments[2];
			}

		} break;

		case 'details':
		{
			$id = explode(':', $segments[1]);
			$vars['id'] = $id[0];
			$vars['view'] = 'details';

		} break;
		
		case 'venueevents':
		{
			$id = explode(':', $segments[1]);
			$vars['id'] = $id[0];
			$vars['view'] = 'venueevents';
			$count = count($segments);
			if($count > 2) {
				$vars['task'] = $segments[2];
			}

		} break;

		case 'editevent':
		{
			$count = count($segments);
			
			$vars['view'] = 'editevent';

			if($count == 3) {
				$vars['id'] = $segments[1];
				$vars['returnid'] = $segments[2];
			}

		} break;

		case 'editvenue':
		{
			$count = count($segments);

			$vars['view'] = 'editvenue';
			
			if($count == 3) {
				$vars['id'] = $segments[1];
				$vars['returnid'] = $segments[2];
			}

		} break;

		case 'redevent':
		{
			$vars['view'] = 'redevent';
			
			$count = count($segments);
			if($count == 2) {
				$vars['task'] = $segments[1];
			}

		} break;

		case 'categoriesdetailed':
		{
			$vars['view'] = 'categoriesdetailed';
			
			$count = count($segments);
			if($count == 2) {
				$vars['task'] = $segments[1];
			}

		} break;

		case 'categories':
		{
			$vars['view'] = 'categories';

			$count = count($segments);
			if($count == 2) {
				$vars['task'] = $segments[1];
			}

		} break;

		case 'venues':
		{
			$vars['view'] = 'venues';
			
			$count = count($segments);
			if($count == 2) {
				$vars['task'] = $segments[1];
			}

		} break;
		
		case 'day':
		{
			$vars['view'] = 'day';
			
			$count = count($segments);
			if($count == 2) {
				$vars['id'] = $segments[1];
			}

		} break;
		
	}

	return $vars;
}
?>