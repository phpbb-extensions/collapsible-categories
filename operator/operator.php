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
 */
class operator implements operator_interface
{
	/** @var array An array of collapsed category forum identifiers */
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
	 * @param \phpbb\config\config              $config  Config object
	 * @param \phpbb\db\driver\driver_interface $db      Database object
	 * @param \phpbb\request\request            $request Request object
	 * @param \phpbb\user                       $user    User object
	 *
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
	 * {@inheritdoc}
	 */
	public function get_user_categories()
	{
		// Get categories from the user object
		$collapsible_categories = (array) json_decode($this->user->data['collapsible_categories'], true);

		if (empty($collapsible_categories))
		{
			// The user object had no categories, check for a cookie
			$collapsible_categories = $this->get_cookie_categories();
		}

		return $collapsible_categories;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_user_categories($forum_id)
	{
		// Set the collapsed category data array
		$this->set_collapsed_categories($forum_id);

		// Update the db with json encoded array of collapsed category data
		if ($this->user->data['is_registered'])
		{
			$sql = 'UPDATE ' . USERS_TABLE . "
				SET collapsible_categories = '" . $this->db->sql_escape(json_encode($this->collapsed_categories)) . "'
				WHERE user_id = " . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);

			// There was an error updating the user's data
			if (!$this->db->sql_affectedrows())
			{
				return false;
			}
		}

		// Set a cookie with the collapsed category data and return true
		return $this->set_cookie_categories($forum_id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cookie_categories()
	{
		// Get categories from the cookie and htmlspecialchars decode it
		$cookie_data = htmlspecialchars_decode($this->request->variable($this->config['cookie_name'] . '_ccat', '', true, \phpbb\request\request_interface::COOKIE));

		// json decode the cookie data and return an array
		return (array) json_decode($cookie_data, true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_cookie_categories($forum_id)
	{
		// Set the collapsed category data array
		$this->set_collapsed_categories($forum_id);

		// Update the cookie with json encoded array of collapsed category data
		$this->user->set_cookie('ccat', json_encode($this->collapsed_categories), strtotime('+1 year'));

		// As we are unable to check immediately if the cookie was set, return true anyway
		return true;
	}

	/**
	 * Set the collapsed_categories property
	 *
	 * @param string $forum_id A forum identifier
	 *
	 * @return operator_interface $this object
	 * @access protected
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
		return array_values(array_unique($array));
	}
}
