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

class main_controller implements main_interface
{
	/** @var \phpbb\collapsiblecategories\operator\operator */
	protected $operator;

	/** @var \phpbb\request\request */
	protected $request;

	/**
	 * Constructor
	 *
	 * @param \phpbb\collapsiblecategories\operator\operator $operator Operator object
	 * @param \phpbb\request\request                         $request  Request object
	 * @access public
	 */
	public function __construct(\phpbb\collapsiblecategories\operator\operator $operator, \phpbb\request\request $request)
	{
		$this->operator = $operator;
		$this->request = $request;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle($forum_id)
	{
		// Throw an exception for non-AJAX requests or if the forum_id is missing
		if (!$this->request->is_ajax() || !$forum_id)
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
}
