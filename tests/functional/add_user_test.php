<?php
/**
 *
 * Collapsible Categories extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\collapsiblecategories\tests\functional;

/**
 * @group functional
 */
class add_user_test extends \phpbb_functional_test_case
{
	/**
	 * Define the extensions to be tested
	 *
	 * @return array vendor/name of extension(s) to test
	 */
	static protected function setup_extensions()
	{
		return array('phpbb/collapsiblecategories');
	}

	/**
	 * Test adding a new user. We need to test that inserts on the user table
	 * do not fail due to the TEXT column we added with a null default value.
	 */
	public function test_add_user()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang('acp/users');

		// Assert a user is successfully created
		$this->assertGreaterThan(0, $this->create_user('testuser'));
	}
}
