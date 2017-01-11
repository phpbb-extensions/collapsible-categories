<?php
/**
 *
 * Collapsible Categories extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\collapsiblecategories\tests\event;

class listener_test extends \phpbb_test_case
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\collapsiblecategories\event\listener */
	protected $listener;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\collapsiblecategories\operator\operator_interface */
	protected $operator;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Setup test environment
	 */
	public function setUp()
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		$this->controller_helper->expects($this->any())
			->method('route')
			->willReturnCallback(function ($route, array $params = array()) {
				return $route . '#' . serialize($params);
			});

		$this->user = $this->getMock('\phpbb\user', array(), array(
			new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
			'\phpbb\datetime'
		));

		// Stub of the operator class
		$this->operator = new \phpbb\collapsiblecategories\operator\operator(
			$this->getMock('\phpbb\config\config', array(), array(array())),
			$this->getMock('\phpbb\db\driver\driver_interface'),
			$this->controller_helper,
			$this->getMock('\phpbb\request\request'),
			$this->user
		);

		// Stub of the template class
		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
	}

	/**
	 * Create our event listener
	 */
	protected function set_listener()
	{
		$this->listener = new \phpbb\collapsiblecategories\event\listener(
			$this->operator,
			$this->template
		);
	}

	/**
	 * Test the event listener is constructed correctly
	 */
	public function test_construct()
	{
		$this->set_listener();
		$this->assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
	}

	/**
	 * Test the event listener is subscribing events
	 */
	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.display_forums_modify_category_template_vars',
			'core.display_forums_modify_template_vars',
		), array_keys(\phpbb\collapsiblecategories\event\listener::getSubscribedEvents()));
	}

	/**
	 * Data set for test_show_collapsible_categories
	 *
	 * @return array
	 */
	public function show_collapsible_categories_data()
	{
		return array(
			array( // Forum 1 is not in the collapsed array
				array('fid_2', 'fid_3'),
				array(
					'cat_row'	=> array(),
					'row'		=> array('forum_id' => 1),
				),
				array(
					'S_FORUM_HIDDEN' => false,
					'U_COLLAPSE_URL' => 'phpbb_collapsiblecategories_main_controller#a:2:{s:8:"forum_id";s:5:"fid_1";s:4:"hash";s:8:"e454b5ca";}',
				),
			),
			array( // Forum 1 is in the collapsed array
				array('fid_1', 'fid_2', 'fid_3'),
				array(
					'cat_row'	=> array(),
					'row'		=> array('forum_id' => 1),
				),
				array(
					'S_FORUM_HIDDEN' => true,
					'U_COLLAPSE_URL' => 'phpbb_collapsiblecategories_main_controller#a:2:{s:8:"forum_id";s:5:"fid_1";s:4:"hash";s:8:"e454b5ca";}',
				),
			),
			array( // Forum 1 is not in the collapsed array (with additional template data mixed in)
				array('fid_2', 'fid_3'),
				array(
					'cat_row'	=> array('FOO1' => 'BAR1'),
					'row'		=> array('forum_id' => 1),
				),
				array(
					'FOO1' => 'BAR1',
					'S_FORUM_HIDDEN' => false,
					'U_COLLAPSE_URL' => 'phpbb_collapsiblecategories_main_controller#a:2:{s:8:"forum_id";s:5:"fid_1";s:4:"hash";s:8:"e454b5ca";}',
				),
			),
			array( // Forum 1 is not in the collapsed array (with additional template data mixed in)
				array('fid_2', 'fid_3'),
				array(
					'cat_row'	=> array('FOO2' => 'BAR2'),
					'row'		=> array('forum_id' => 1),
				),
				array(
					'FOO2' => 'BAR2',
					'S_FORUM_HIDDEN' => false,
					'U_COLLAPSE_URL' => 'phpbb_collapsiblecategories_main_controller#a:2:{s:8:"forum_id";s:5:"fid_1";s:4:"hash";s:8:"e454b5ca";}',
				),
			),
			array( // Un-categorized forum 1 is not in the collapsed array
				array('fid_2', 'fid_3'),
				array(
					'forum_row'	=> array(),
					'row'		=> array('forum_id' => 1),
				),
				array(
					'S_FORUM_HIDDEN' => false,
					'U_COLLAPSE_URL' => 'phpbb_collapsiblecategories_main_controller#a:2:{s:8:"forum_id";s:5:"fid_1";s:4:"hash";s:8:"e454b5ca";}',
				),
			),
			array( // Un-categorized forum 1 is in the collapsed array
				array('fid_1', 'fid_2', 'fid_3'),
				array(
					'forum_row'	=> array(),
					'row'		=> array('forum_id' => 1),
				),
				array(
					'S_FORUM_HIDDEN' => true,
					'U_COLLAPSE_URL' => 'phpbb_collapsiblecategories_main_controller#a:2:{s:8:"forum_id";s:5:"fid_1";s:4:"hash";s:8:"e454b5ca";}',
				),
			),
		);
	}

	/**
	 * Test test_show_collapsible_categories() is adding the expected
	 * show/hide states for collapsed categories to the template data
	 *
	 * @param $collapsed_forums
	 * @param $data_map
	 * @param $expected
	 *
	 * @dataProvider show_collapsible_categories_data
	 */
	public function test_show_collapsible_categories($collapsed_forums, $data_map, $expected)
	{
		$this->set_listener();

		// Define event data object
		$data = new \phpbb\event\data($data_map);

		$this->user->data['collapsible_categories'] = json_encode($collapsed_forums);

		// Call the method
		$this->listener->show_collapsible_categories($data);

		// Get the first array key name (cat_row or forum_row)
		$forum_row = key($data_map);

		// Assert the event data object is updated as expected
		$this->assertSame($expected, $data[$forum_row]);
	}
}
