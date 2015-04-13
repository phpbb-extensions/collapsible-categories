<?php
/**
 *
 * Collapsible Categories extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\collapsiblecategories\operator;

/**
 * Interface operator_interface
 * This describes all of the methods for processing collapsed category data
 *
 * @package phpbb\collapsiblecategories\operator
 */
interface operator_interface
{
	/**
	 * Get the user's collapsed category data from the user object
	 *
	 * @return array An array of collapsed forum identifiers
	 *               or an empty array if nothing was found.
	 * @access public
	 */
	public function get_user_categories();

	/**
	 * Set the user's collapsed category data in the database
	 *
	 * @param string $forum_id A forum identifier
	 *
	 * @return bool True if user data was stored, false otherwise
	 * @access public
	 */
	public function set_user_categories($forum_id);

	/**
	 * Get the user's collapsed category data from a cookie
	 *
	 * @return array An array of collapsed forum identifiers
	 *               or an empty array if nothing was found.
	 * @access public
	 */
	public function get_cookie_categories();

	/**
	 * Set the user's collapsed category data in a cookie
	 *
	 * @param string $forum_id A forum identifier
	 *
	 * @return bool True
	 * @access public
	 */
	public function set_cookie_categories($forum_id);
}
