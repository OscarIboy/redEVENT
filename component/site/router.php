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
	  $view = $query['view'];
		$segments[] = $query['view'];
		unset($query['view']);
	}
	else {
	  $view = '';
	}

//	if (isset($query['controller']))
//	{
//	  $controller = $query['controller'];
//		$segments[] = 'c'.$query['controller'];
//		unset($query['controller']);
//    
//    if(isset($query['task']))
//    {
//    	$segments[] = $query['task'];
//    	unset($query['task']);
//    };
//	}
	
	switch ($view) {
	  
	  case 'confirmation':
	    break;
	  case 'editevent':
    	if(isset($query['id']))
    	{
    		$segments[] = $query['id'];
    		unset($query['id']);
    	};
    	if(isset($query['xref']))
    	{
    		$segments[] = $query['xref'];
    		unset($query['xref']);
    	};
	  	break;
	  case 'calendar':
	  case 'categoryevents':
	  case 'details':
	  case 'search':
	  case 'upcomingevents':
	  case 'venuecategory':
	  case 'venuesmap':
	  case 'categories':
	  case 'confirmation':
	  case 'myevents':
	  case 'signup':
	  case 'upcomingvenueevents':
	  case 'venueevents':
	  case 'categoriesdetailed':
	  case 'day':
	  case 'editvenue':
	  case 'payment':
	  case 'simplelist':
	  case 'venue':
	  case 'venues':
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
    	break;
    	
	  case 'attendees':	  	
    	if(isset($query['controller']))
    	{
    		$segments[] = $query['controller'];
    		unset($query['controller']);
    	};	
    	if(isset($query['task']))
    	{
    		$segments[] = $query['task'];
    		unset($query['task']);
    	};
    	if(isset($query['xref']))
    	{
    		$segments[] = $query['xref'];
    		unset($query['xref']);
    	};
    	break;
	}

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
		
		case 'venue':
		{
			$id = explode(':', $segments[1]);
			$vars['id'] = $id[0];
			$vars['view'] = 'venue';
		} break;

		case 'details':
		{
			$vars['view'] = 'details';
      $count = count($segments);
			if ($count > 1) 
			{
				$id = explode(':', $segments[1]);
				$vars['id'] = $id[0];
	      if($count > 2) {
	        $vars['task'] = $segments[2];
	      }
			}

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

			if ($count > 1) {
				$vars['id'] = $segments[1];
			}

			if($count > 2) {
				$vars['xref'] = $segments[2];
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

		case 'simplelist':
		{
			$vars['view'] = 'simplelist';
			
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
		
    case 'upcomingvenueevents':
    {
      $vars['view'] = 'upcomingvenueevents';
      
      $count = count($segments);
      if($count == 2) {
        $vars['id'] = $segments[1];
      }

    } break;
		
    case 'attendees':
    {
      $vars['view'] = 'attendees';
      
      $count = count($segments);
      if($count > 1) {
        $vars['controller'] = $segments[1];
      }
      if($count > 2) {
        $vars['task'] = $segments[2];
      }
      if($count > 3) {
        $vars['xref'] = $segments[3];
      }

    } break;
    
    case 'confirmation':
    case 'signup':
    case 'calendar':
    case 'payment':
    case 'search':
    case 'upcomingevents':
    case 'venue':
    case 'venuecategory':
    case 'venuesmap':
    case 'myevents':
      $vars['view'] = $segments[0];      
      break;
      
//    case 'cregistration':
//    	$vars['controller'] = $segments[0];
//    	break;
    	
//		default:
//      $vars['view'] = $segments[0];
//			
//			break;
	}

	return $vars;
}
?>