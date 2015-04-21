<?php

namespace phpbb\collapsiblecategories\tests\functional;

/**
 * @group functional
 */
class base_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('phpbb/collapsiblecategories');
	}

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	public function setUp()
	{
		parent::setUp();

		$this->db = $this->get_db();
	}

	public function test_user()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang('acp/users');

		$user_id = $this->create_user('testuser');

		$this->assertGreaterThan(0, $user_id);
	}
}
