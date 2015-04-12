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
	/** @var \phpbb\collapsiblecategories\event\listener */
	protected $listener;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $controller_helper;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $operator;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $template;

	/**
	 * Setup test environment
	 */
	public function setUp()
	{
		parent::setUp();

		// Stub of the operator class
		$this->operator = $this->getMockBuilder('\phpbb\collapsiblecategories\operator\operator_interface')
			->getMock();

		// Stub of the controller helper class
		$this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		$this->controller_helper->expects($this->any())
			->method('route')
			->willReturnCallback(function ($route, array $params = array()) {
				return $route . '#' . serialize($params);
			});

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
			$this->controller_helper,
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
			'core.display_forums_after',
			'core.display_forums_modify_category_template_vars',
		), array_keys(\phpbb\collapsiblecategories\event\listener::getSubscribedEvents()));
	}

	/**
	 * Test setup_collapsible_categories() is assigning the expected template vars
	 */
	public function test_init_collapsible_categories()
	{
		$this->set_listener();

		// Set up expectations
		$this->template->expects($this->once())
			->method('assign_vars')
			->with(array(
				'UA_COLLAPSIBLE_CATEGORIES_URL' => 'phpbb_collapsiblecategories_main_controller#' . serialize(array()),
			));

		// Call the method
		$this->listener->init_collapsible_categories();
	}

	/**
	 * Data set for test_show_collapsible_categories
	 *
	 * @return array
	 */
	public function load_collapsible_categories_data()
	{
		return array(
			array( // Forum 1 is not collapsed
				array(
					'cat_row'	=> array(),
					'row'		=> array('forum_id' => 1),
				),
				array(),
				array('S_FORUM_HIDDEN' => false),
			),
			array( // Forum 1 is collapsed
				array(
					'cat_row'	=> array(),
					'row'		=> array('forum_id' => 1),
				),
				array('fid_1', 'fid_2', 'fid_3'),
				array('S_FORUM_HIDDEN' => true),
			),
			array( // Forum 9 is not collapsed (with additional template data mixed in)
				array(
					'cat_row'	=> array('FOO1' => 'BAR1'),
					'row'		=> array('forum_id' => 9),
				),
				array('fid_1', 'fid_2', 'fid_3'),
				array('FOO1' => 'BAR1', 'S_FORUM_HIDDEN' => false),
			),
			array( // Forum 9 is not collapsed (with additional template data miced in)
				array(
					'cat_row'	=> array('FOO2' => 'BAR2'),
					'row'		=> array('forum_id' => 9),
				),
				array('fid_1', 'fid_2', 'fid_3'),
				array('FOO2' => 'BAR2', 'S_FORUM_HIDDEN' => false),
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
	 * @dataProvider load_collapsible_categories_data
	 */
	public function test_show_collapsible_categories($data_map, $collapsed_forums, $expected)
	{
		$this->set_listener();

		// Define event data object
		$data = new \phpbb\event\data($data_map);

		// Make the operator return $collapsed_forums test data
		$this->operator->expects($this->any())
			->method('get_user_categories')
			->will($this->returnValue($collapsed_forums));

		// Call the method
		$this->listener->show_collapsible_categories($data);

		// Assert the event data object is updated as expected
		$this->assertSame($data['cat_row'], $expected);
	}
}
