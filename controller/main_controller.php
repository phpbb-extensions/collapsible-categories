<?php
/**
 *
 * Collapsible Categories extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\collapsiblecategories\controller;

use phpbb\collapsiblecategories\operator\operator;
use phpbb\request\request;

class main_controller implements main_interface
{
	/** @var operator */
	protected $operator;

	/** @var request */
	protected $request;

	/**
	 * Constructor
	 *
	 * @param operator $operator Operator object
	 * @param request  $request  Request object
	 */
	public function __construct(operator $operator, request $request)
	{
		$this->operator = $operator;
		$this->request = $request;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle($forum_id)
	{
		// Throw an exception for non-AJAX requests or invalid link requests
		if (!$this->request->is_ajax() || !$this->is_valid($forum_id) || !check_link_hash($this->request->variable('hash', ''), 'collapsible_' . $forum_id))
		{
			throw new \phpbb\exception\http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Update the user's collapsed category data for the given forum
		$response = $this->operator->set_user_categories($forum_id);

		// Return a JSON response
		return new \Symfony\Component\HttpFoundation\JsonResponse(array(
			'success' => $response,
		));
	}

	/**
	 * Validate values containing only letters, numbers and underscores
	 *
	 * @param string $value Value to test
	 *
	 * @return bool true if valid, false if invalid
	 */
	protected function is_valid($value)
	{
		return !empty($value) && preg_match('/^\w+$/', $value);
	}
}
