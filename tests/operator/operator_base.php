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

class operator_base extends \phpbb_database_test_case
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\user */
	protected $user;

	/** @var \phpbb\collapsiblecategories\operator\operator */
	protected $operator;

	protected static function setup_extensions()
	{
		return array('phpbb/collapsiblecategories');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/fixtures/users.xml');
	}

	protected function setUp(): void
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->db = $this->new_dbal();
		$this->config = new \phpbb\config\config(array('cookie_name' => 'test'));
		$this->user = $this->getMockBuilder('\phpbb\user')
			->setConstructorArgs(array(
				new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
				'\phpbb\datetime'
			))
			->getMock();
		$this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * Set a cookie super global for testing purposes
	 *
	 * @param $name
	 * @param $data
	 */
	public function set_cookie($name, $data)
	{
		$_COOKIE[$name] = htmlspecialchars(json_encode($data));
	}

	/**
	 * Set up user data properties
	 *
	 * @param $user_id
	 * @param $data
	 */
	public function set_user($user_id, $data)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['is_registered'] = $user_id > 1;
		$this->user->data['collapsible_categories'] = json_encode($data);
	}

	/**
	 * Set up an instance of the operator class
	 */
	public function set_operator()
	{
		// We need to set up the request class and enable super globals at this point
		// because it needs to happen after we have defined a test $_COOKIE value.
		$this->request = new \phpbb\request\request($this->getMockBuilder('\phpbb\request\type_cast_helper_interface')->getMock());
		$this->request->enable_super_globals();

		$this->operator = new \phpbb\collapsiblecategories\operator\operator($this->config, $this->db, $this->controller_helper, $this->request, $this->user);

		self::assertInstanceOf('\phpbb\collapsiblecategories\operator\operator', $this->operator);
	}

	/**
	 * Data set for get category methods
	 *
	 * @return array
	 */
	public static function get_categories_data()
	{
		return array(
			array(array()),
			array(array('foo_1')),
			array(array('foo_1', 'bar_1')),
		);
	}

	/**
	 * Data set for set category methods
	 *
	 * @return array
	 */
	public static function set_categories_data()
	{
		return array(
			array('foo_1', array(), array('foo_1')), // add new forum to an empty set
			array('bar_1', array(), array('bar_1')), // add new forum to an empty set
			array('foo_1', array('foo_1'), array()), // remove forum from current set
			array('bar_1', array('bar_1'), array()), // remove forum from current set
			array('bar_1', array('foo_1'), array('foo_1', 'bar_1')), // add new forum to current set
			array('foo_1', array('bar_1'), array('bar_1', 'foo_1')), // add new forum to current set
			array('bar_1', array('foo_1', 'bar_1'), array('foo_1')), // remove forum from current set
			array('foo_1', array('bar_1', 'foo_1'), array('bar_1')), // remove forum from current set
		);
	}
}
