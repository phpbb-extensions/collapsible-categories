<?php
/**
*
* Collapsible Categories extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\collapsiblecategories\tests\controller;

class main_controller_test extends \phpbb_test_case
{
	public function get_controller($forum_id, $is_ajax = false, $result = false, $invoked = false)
	{
		global $user;

		$user = $this->getMockBuilder('\phpbb\user')
			->disableOriginalConstructor()
			->getMock();
		$user->data['user_form_salt'] = '';

		/** @var $operator \PHPUnit_Framework_MockObject_MockObject|\phpbb\collapsiblecategories\operator\operator */
		$operator = $this->getMockBuilder('\phpbb\collapsiblecategories\operator\operator')
			->setMethods(['set_user_categories'])
			->disableOriginalConstructor()
			->getMock();

		// Override set_user_categories() to expect $forum_id and return value of $result
		$operator->expects($invoked ? self::once() : self::never())
			->method('set_user_categories')
			->with($forum_id)
			->willReturn($result);

		/** @var $request \PHPUnit_Framework_MockObject_MockObject|\phpbb\request\request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();

		// Override is_ajax() to return value of $is_ajax
		$request->expects(self::atMost(1))
			->method('is_ajax')
			->willReturn($is_ajax);

		// Override variable() to return value of link hash
		$request->expects($invoked ? self::once() : self::never())
			->method('variable')
			->with(self::anything())
			->willReturnMap(array(
				array('hash', '', false, \phpbb\request\request_interface::REQUEST, generate_link_hash('collapsible_' . $forum_id))
			));

		// Return an instance of the controller
		return new \phpbb\collapsiblecategories\controller\main_controller($operator, $request);
	}

	/**
	 * Data set for test_main
	 *
	 * @return array
	 */
	public function main_test_data()
	{
		return array(
			array('fid_1', true, true, 200, '{"success":true}'), // AJAX response with true result
			array('fid_1', true, false, 200, '{"success":false}'), // AJAX response with false result
		);
	}

	/**
	 * Test the controller is returning a JSON response for
	 * AJAX requests with the expected success content.
	 *
	 * @param $forum_id
	 * @param $is_ajax
	 * @param $result
	 * @param $status_code
	 * @param $content

	 * @dataProvider main_test_data
	 */
	public function test_main($forum_id, $is_ajax, $result, $status_code, $content)
	{
		$controller = $this->get_controller($forum_id, $is_ajax, $result, true);
		self::assertInstanceOf('\phpbb\collapsiblecategories\controller\main_controller', $controller);

		$response = $controller->handle($forum_id);
		self::assertInstanceOf('\Symfony\Component\HttpFoundation\JsonResponse', $response);
		self::assertEquals($status_code, $response->getStatusCode());
		self::assertEquals($content, $response->getContent());
	}

	/**
	 * Data set for test_main_fails
	 *
	 * @return array
	 */
	public function main_test_fails_data()
	{
		return array(
			array(0, true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array('', true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array('0', true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array(null, true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array('foo 1', true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array('\'foo 1\'', true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array('foo%201', true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array('?foo=\'+escape\(document.cookie\)\;\'', true, 403, 'NO_AUTH_OPERATION'), // bad forum_id
			array('foo_1', false, 403, 'NO_AUTH_OPERATION'), // not AJAX
		);
	}

	/**
	 * Test an http_exception is immediately thrown for
	 * non-AJAX requests and empty forum_id values.
	 *
	 * @param $forum_id
	 * @param $is_ajax
	 * @param $status_code
	 * @param $content
	 *
	 * @dataProvider main_test_fails_data
	 */
	public function test_main_fails($forum_id, $is_ajax, $status_code, $content)
	{
		$controller = $this->get_controller($forum_id, $is_ajax);
		self::assertInstanceOf('\phpbb\collapsiblecategories\controller\main_controller', $controller);

		try
		{
			$controller->handle($forum_id);
			self::fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			self::assertEquals($status_code, $exception->getStatusCode());
			self::assertEquals($content, $exception->getMessage());
		}
	}
}
