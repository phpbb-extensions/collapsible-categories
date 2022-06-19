<?php
/**
 *
 * Collapsible Categories extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\collapsiblecategories\tests\operator;

class get_categories_test extends operator_base
{
	/**
	 * Test get_user_categories()
	 *
	 * @param $data
	 *
	 * @dataProvider get_categories_data
	 */
	public function test_get_user_categories($data)
	{
		// Set up the user data as a guest
		$this->set_user(0, $data);

		// Set up the operator class
		$this->set_operator();

		// Assert the get the expected data
		self::assertEquals($data, $this->operator->get_user_categories());
	}

	/**
	 * Test get_cookie_categories()
	 *
	 * @param $data
	 *
	 * @dataProvider get_categories_data
	 */
	public function test_get_cookie_categories($data)
	{
		// Set up the cookie as a super global
		$this->set_cookie($this->config['cookie_name'] . '_ccat', $data);

		// Set up the operator class
		$this->set_operator();

		// Assert we get the cookie, and it has the expected data
		self::assertEquals($data, $this->operator->get_cookie_categories());
	}
}
