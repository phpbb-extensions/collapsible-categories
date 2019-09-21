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

class set_categories_test extends operator_base
{
	/**
	 * Test set_user_categories()
	 *
	 * @param $forum_id
	 * @param $user_data
	 *
	 * @dataProvider set_categories_data
	 */
	public function test_set_user_categories($forum_id, $user_data)
	{
		// Set up the user data as a registered member
		$this->set_user(2, $user_data);

		// Set up the operator class
		$this->set_operator();

		// Assert that the db update result is true
		$this->assertTrue($this->operator->set_user_categories($forum_id));
	}

	/**
	 * Test set_user_categories() fails
	 */
	public function test_set_user_categories_fails()
	{
		// Set up the user data as a not-found registered member
		$this->set_user(10, array('foo_1'));

		// Set up the operator class
		$this->set_operator();

		// Assert that the db update result is false
		$this->assertFalse($this->operator->set_user_categories('bar_1'));
	}

	/**
	 * Test set_cookie_categories()
	 *
	 * @param $forum_id
	 * @param $user_data
	 * @param $expected
	 *
	 * @dataProvider set_categories_data
	 */
	public function test_set_cookie_categories($forum_id, $user_data, $expected)
	{
		// Set up the user data as a guest
		$this->set_user(0, $user_data);

		// Set up the operator class
		$this->set_operator();

		// Assert set_cookie() method is setting the expected data array
		$this->user->expects($this->once())
			->method('set_cookie')
			->with($this->anything(), json_encode($expected), $this->anything());

		// Assert set_cookie_categories() sets the expected data return result
		$this->assertTrue($this->operator->set_cookie_categories($forum_id));
	}
}
