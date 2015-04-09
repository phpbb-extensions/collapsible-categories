<?php
/**
 *
 * Collapsible Categories extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\collapsiblecategories\tests\dbal;

class simple_db_test extends \phpbb_database_test_case
{
	static protected function setup_extensions()
	{
		return array('phpbb/collapsiblecategories');
	}

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/users.xml');
	}

	public function test_column()
	{
		// Instantiate the dbal
		$this->db = $this->new_dbal();

		// Instantiate the db_tools class
		$db_tools = new \phpbb\db\tools($this->db);

		// Test the migration installs the collapsible_categories column
		$this->assertTrue($db_tools->sql_column_exists(USERS_TABLE, 'collapsible_categories'), 'Asserting that column "collapsible_categories" exists');
		$this->assertFalse($db_tools->sql_column_exists(USERS_TABLE, 'foo_bar'), 'Asserting that a dummy column does not exist');
	}
}
