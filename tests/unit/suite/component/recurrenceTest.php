<?php
/**
 * JLoaderTest
 *
 * @package   Joomla.UnitTest
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license   GNU General Public License
 */
//require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Autoload.php';

require_once (BASEPATH.DS.'component'.DS.'site'.DS.'helpers'.DS.'recurrence.php');

/**
 * Test class for currency.
 * Generated by PHPUnit on 2009-10-16 at 23:32:06.
 *
 * @package	redFORM.UnitTest
 */
class recurrenceTest extends JoomlaTestCase
{	
	
	public function getTestGetNextData()
	{
		$data = array();
		
		// first
		$rrule = 'RRULE:FREQ=MONTHLY;INTERVAL=1;UNTIL=20200901T000000;WKST=MO;';
		$xref = new stdclass();
		$xref->dates = '2012-12-04';
		$xref->enddates = '2012-12-04';
		$xref->times = '14:00';
		$xref->endtimes = '16:00';
		$xref->registrationend = null;
		
		$expect = clone $xref;
		$expect->dates = '2013-01-04';
		$expect->enddates = '2013-01-04';
		
		$data['first'] = array($rrule, $xref, $expect, 'should be one month from initial');
		
		// continuing
		$expect2 = clone $expect;
		$expect2->dates = '2013-02-04';
		$expect2->enddates = '2013-02-04';
		
		
		$data['second'] = array($rrule, $expect, $expect2, 'should be one month from initial');
		return $data;
	}
	
	/**
	 * test get country name function
	 * 
	 * @param string $iso
	 * @return void
	 * @dataProvider getTestGetNextData
	 */
	public function testGetNext($rrule, $xref_data, $expect_next, $message)
	{
		$params = new JRegistry();
		$params->setValue('week_start', 'MO');
		
		$next = RedeventHelperRecurrence::getNext($rrule, $xref_data, $params);
 
		if ($expect_next === false) {
			$this->assertFalse($next);
		}
		else {
			$this->assertEquals(
				$next->dates,
				$expect_next->dates,
				$message
			);
		}
	}
}