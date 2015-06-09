<?php

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class OptionsPresenter extends \PresenterTestCase
{

	public function __construct()
	{
		$this->openPresenter('Admin:Options:');
	}

	public function setUp()
	{
		$this->logIn(1, 'superadmin'); //TODO: lépe (?)
	}

	public function testRenderGeneral()
	{
		$this->checkAction('general');
	}

	public function testRenderGeneralLoggedOut()
	{
		$this->logOut();
		$this->checkRedirect('general');
	}

	public function testRenderSeo()
	{
		$this->checkAction('seo');
	}

	public function testRenderSeoLoggedOut()
	{
		$this->logOut();
		$this->checkRedirect('seo');
	}

}

(new OptionsPresenter())->run();