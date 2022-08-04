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

/**
 * Interface main_interface
 * This describes all the methods for the main controller
 *
 * @package phpbb\collapsiblecategories\controller
 */
interface main_interface
{
	/**
	 * This method processes AJAX requests for collapsible categories
	 * when a user collapses/expands a category. Collapsed categories
	 * will be stored to the user's db and cookie. Expanded categories
	 * will be removed from the user's db and cookie.
	 *
	 * @param string $forum_id A forum identifier
	 *
	 * @throws \phpbb\exception\http_exception A http exception
	 * @return \Symfony\Component\HttpFoundation\JsonResponse A Symfony JSON Response object
	 */
	public function handle($forum_id);
}
