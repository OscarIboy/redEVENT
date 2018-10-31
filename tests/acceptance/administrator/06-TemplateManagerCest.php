<?php
/**
 * @package     Redevent
 * @subpackage  Tests
 * @copyright   Copyright (C) 2008 - 2018 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Step\Acceptance\Administrator\TemplateManagerSteps;
use Faker\Generator;
use Faker\Factory;
use Page\Acceptance\Administrator\AbstractPage;
class CreateTemplateCest
{
    /**
     * @var   Generator
     * @since 1.0.0
     */
    protected $faker;

    /**
     * @var   string
     * @since 1.0.0
     */
    protected $nameTemplate1;

    /**
     * @var   string
     * @since 1.0.0
     */
    protected $nameTemplate2;

    /**
     * 04-CategoryManagerCest constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->faker         = Factory::create();
        $this->nameTemplate1 = $this->faker->bothify("Template Name 1 ##??");
        $this->nameTemplate2 = $this->faker->bothify("Template Name 2 ##??");
    }
    public function _before(\AcceptanceTester $i)
    {
        $i->doAdministratorLogin();
    }

    /**
     * @param TemplateManagerSteps $I
     * @throws Exception
     */
	public function allCaseTemplate(TemplateManagerSteps $I)
	{
		$I->wantToTest('Add a template 1 in redEVENT');
		$I->createMinimalRegistrationForm(['name' => 'Registration']);
		$I->createTemplate(
			array(
				'name' =>$this->nameTemplate1,
				'meta_description' => 'This is the meta description of the event [event_title], session at [venue]',
				'meta_keywords' => 'some keywords, [event_title], [venue]',
				'redform' => 'Registration'
			)
		);
        $I->waitForText(AbstractPage::$messageSaveSuccess, 30, AbstractPage::$message);
        $I->wantToTest('Add a template 2 in redEVENT');
        $I->createMinimalRegistrationForm(['name' => 'Registration']);
        $I->createTemplate(
            array(
                'name' =>$this->nameTemplate2,
                'meta_description' => 'This is the meta description of the event [event_title], session at [venue]',
                'meta_keywords' => 'some keywords, [event_title], [venue]',
                'redform' => 'Registration'
            )
        );
        $I->waitForText(AbstractPage::$messageSaveSuccess, 30, AbstractPage::$message);
        $I->searchTemplate($this->nameTemplate1);
        $I->dontSee($this->nameTemplate2);
        $I->buttonClear($this->nameTemplate1,$this->nameTemplate2);
        $I->wantToTest('delete a category 1 in redEVENT');
        $I->deleteTemplate($this->nameTemplate1);
        $I->wantToTest('delete a category 2 in redEVENT');
        $I->deleteTemplate($this->nameTemplate2);
	}
}
