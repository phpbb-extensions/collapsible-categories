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

class controller implements main_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\config\config                $config             Config object
	* @param \phpbb\controller\helper            $helper             Controller helper object
	* @param \phpbb\boardrules\operators\rule    $rule_operator      Rule operator object
	* @param \phpbb\template\template            $template           Template object
	* @param \phpbb\user                         $user               User object
	* @param string                              $root_path          phpBB root path
	* @param string                              $php_ext            phpEx
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * This method processes AJAX requests for collapsible categories
	 * when a user collapses/expands a category. Collapsed categories
	 * will be stored to the user's db and cookie. Expanded categories
	 * will be removed from the user's db and cookie.
	 *
	 * @param int $forum_id A forum identifier
	 *
	 * @throws \phpbb\exception\http_exception An http exception
	 * @return \Symfony\Component\HttpFoundation\JsonResponse A Symfony JSON Response object
	 * @access public
	 */
	public function handle($forum_id)
	{
		// If this is no ajax request, it is not supported, so we throw an exception
		if (!$this->request->is_ajax())
		{
			throw new \phpbb\exception\http_exception(403, 'NO_AUTH_OPERATION');
		}

		// When the route is called without a parameter, 0 is used. This is no valid request, so we throw an error here
		if ($forum_id == 0)
		{
			throw new \phpbb\exception\http_exception(500, 'GENERAL_ERROR');
		}

		// Set a cookie
		$response = $this->operator->set_cookie_categories($forum_id);

		// Close the announcement for registered users
		if ($this->user->data['is_registered'])
		{
			$response = $this->operator->set_user_categories($forum_id);
		}

		// Send a JSON response
		return new \Symfony\Component\HttpFoundation\JsonResponse(array(
			'success' => $response,
		));
	}
}
