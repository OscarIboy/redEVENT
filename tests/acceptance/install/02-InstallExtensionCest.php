<?php
/**
* @package     redFORM
* @subpackage  Cept
* @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

class InstallExtensionCest
{
	public function install(\AcceptanceTester $i)
	{
		$i->wantToTest('redEVENT installation in Joomla 3');
		$i->doAdministratorLogin();

		$i->amOnPage('/administrator/index.php?option=com_installer');
		$i->waitForText('Extensions: Install', 60, ['css' => 'H1']);

		$i->click(['link' => 'Install from Folder']);
		$i->comment('I enter the path');

        $path = $i->getConfiguration('extension folder') . 'tests/extension/redCORE';
        $i->installExtensionFromFolder($path);

		$path = $i->getConfiguration('extension folder') . 'tests/extension/redFORM';
		$i->installExtensionFromFolder($path);

		$pathEvent=  $i->getConfiguration('packages url') . 'redevent.zip';
		$i->installExtensionFromUrl($pathEvent);
	}
}
