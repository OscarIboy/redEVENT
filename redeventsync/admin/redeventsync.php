<?php
/**
 * @package     Redcomponent.redeventsync
 * @subpackage  com_redeventsync
 * @copyright   Copyright (C) 2013 redCOMPONENT.com
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die();

// Load FOF
include_once JPATH_LIBRARIES . '/fof/include.php';

// Register library prefix
JLoader::registerPrefix('R', JPATH_LIBRARIES . '/redcore');
RLoader::registerPrefix('RESync', JPATH_LIBRARIES . '/redeventsync');

if (!defined('FOF_INCLUDED'))
{
	JError::raiseError('500', 'FOF is not installed');
}

include_once JPATH_ADMINISTRATOR . '/components/com_redeventsync/defines.php';

FOFDispatcher::getTmpInstance('com_redeventsync')->dispatch();