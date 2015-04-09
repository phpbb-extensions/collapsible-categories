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
 * Class operator
 *
 * @package phpbb\collapsiblecategories\operator
 */
class operator implements operator_interface
{
	/** @var string An array of collapsed category forum identifiers */
	protected $collapsed_categories;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config                $config         Config object
	 * @param \phpbb\db\driver\driver_interface   $db             Database object
	 * @param \phpbb\request\request              $request        Request object
	 * @param \phpbb\user                         $user           User object
	 *
	 * @return \phpbb\collapsiblecategories\operator\operator
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\user $user)
	{
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
	}

	/**
	 * {@inheritdoc}}
	 */
	public function get_user_categories()
	{
		// TODO
		// 1.gets the categories by unserializing the user object data 'collapsible_categories'
		// 2.if no categories found, call get_cookie_categories()
		// 3.return the categories or an empty array
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_user_categories($forum_id)
	{
		// Update the collapsed category data
		$this->set_collapsed_categories($forum_id);

		// TODO
		// 1.if user is registered, update the db with serialized array of collapsed category data
		// 2.set their cookie too by calling set_cookie_categories()
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cookie_categories()
	{
		// TODO
		// 1.gets categories from the cookie (will need to be unencoded by json_decode and htmlspecialchars_decode)
		// 2.return categories or an empty array
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_cookie_categories($forum_id)
	{
		// Update the collapsed category data
		$this->set_collapsed_categories($forum_id);

		// TODO
		// 1.update the cookie with json_encoded array of collapsed category data
	}

	/**
	 * Set the collapsed_categories property
	 *
	 * @param int $forum_id A forum identifier
	 *
	 * @return operator_interface $this object
	 */
	protected function set_collapsed_categories($forum_id)
	{
		if (!isset($this->collapsed_categories))
		{
			$this->collapsed_categories = $this->toggle_array_value($forum_id, $this->get_user_categories());
		}

		return $this;
	}

	/**
	 * Add a value to an array if it is new or
	 * remove the value if it exists in the array.
	 *
	 * @param mixed $value A string or int value
	 * @param array $array An array
	 *
	 * @return array The updated array
	 * @access protected
	 */
	protected function toggle_array_value($value, $array)
	{
		if (in_array($value, $array))
		{
			// Remove all matching values from the array using array_diff
			$array = array_diff($array, array($value));
		}
		else
		{
			// Add the new value to the array
			$array[] = $value;
		}

		// Enforce unique array values
		return array_unique($array);
	}
}
