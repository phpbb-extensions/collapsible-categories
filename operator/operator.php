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

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\request\request;
use phpbb\user;

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

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config              $config  Config object
	 * @param \phpbb\db\driver\driver_interface $db      Database object
	 * @param \phpbb\controller\helper          $helper  Controller helper object
	 * @param \phpbb\request\request            $request Request object
	 * @param \phpbb\user                       $user    User object
	 */
	public function __construct(config $config, driver_interface $db, helper $helper, request $request, user $user)
	{
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->request = $request;
		$this->user = $user;

		$this->user->add_lang_ext('phpbb/collapsiblecategories', 'collapsiblecategories');
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_collapsed($forum_id)
	{
		if (!isset($this->collapsed_categories))
		{
			$this->collapsed_categories = $this->get_user_categories();
		}

		return in_array($forum_id, $this->collapsed_categories);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_collapsible_link($forum_id)
	{
		return $this->helper->route('phpbb_collapsiblecategories_main_controller', array(
			'forum_id'	=> $forum_id,
			'hash'		=> generate_link_hash("collapsible_$forum_id")
		));
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
		return array_keys(array_count_values($array));
	}
}
