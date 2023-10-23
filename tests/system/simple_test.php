<?php
/**
 *
 * Collapsible Categories extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\collapsiblecategories\tests\system;

class simple_test extends \phpbb_test_case
{
	/** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\DependencyInjection\ContainerInterface */
	protected $container;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\finder */
	protected $extension_finder;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\db\migrator */
	protected $migrator;

	protected function setUp(): void
	{
		parent::setUp();

		// Stub the container
		$this->container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
			->disableOriginalConstructor()
			->getMock();

		// Stub the ext finder and disable its constructor
		$this->extension_finder = $this->getMockBuilder('\phpbb\finder')
			->disableOriginalConstructor()
			->getMock();

		// Stub the migrator and disable its constructor
		$this->migrator = $this->getMockBuilder('\phpbb\db\migrator')
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * Data set for test_ext
	 *
	 * @return array
	 */
	public function ext_test_data()
	{
		// Use the required version constant defined in the ext class
		$req_version = \phpbb\collapsiblecategories\ext::PHPBB_MIN_VERSION;

		return array(
			// Versions less than the requirement
			array('3.1.0', false),
			array('3.1.1', false),
			array('3.1.1.1', false),
			array($req_version . '-A1', false),
			array($req_version . '-RC1', false),
			array($req_version . '-DEV', false),

			// Versions equal to or greater than the requirement
			array($req_version, true),
			array($req_version . '-PL1', true),
			array($req_version . '.1', true),
			array('3.3.11', true),
		);
	}

	/**
	 * Test the extension can only be enabled when the minimum
	 * phpBB version requirement is satisfied.
	 *
	 * @param $version
	 * @param $expected
	 *
	 * @dataProvider ext_test_data
	 */
	public function test_ext($version, $expected)
	{
		// Instantiate config object and set config version
		$config = new \phpbb\config\config(array(
			'version' => $version,
		));

		// Mocked container should return the config object
		// when encountering $this->container->get('config')
		$this->container->expects(self::once())
			->method('get')
			->with('config')
			->willReturn($config);

		$ext = new \phpbb\collapsiblecategories\ext($this->container, $this->extension_finder, $this->migrator, 'phpbb/collapsiblecategories', '');

		self::assertSame($expected, $ext->is_enableable());
	}
}
