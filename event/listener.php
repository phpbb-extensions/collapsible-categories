<?php
/**
*
* Collapsible Categories extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\collapsiblecategories\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var array Array of collapsed forum category identifiers */
	protected $categories;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\collapsiblecategories\operator\operator_interface */
	protected $operator;

	/** @var \phpbb\template\template */
	protected $template;

	public function __construct(\phpbb\controller\helper $helper, \phpbb\collapsiblecategories\operator\operator_interface $operator, \phpbb\template\template $template)
	{
		$this->helper = $helper;
		$this->operator = $operator;
		$this->template = $template;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.display_forums_after'							=> 'setup_collapsible_categories',
			'core.display_forums_modify_category_template_vars'	=> 'load_collapsible_categories',
		);
	}

	public function setup_collapsible_categories()
	{
		$this->template->assign_vars(array(
			'UA_COLLAPSIBLE_CATEGORIES_URL' => $this->helper->route('phpbb_collapsiblecategories_main_controller'),
		));
	}

	public function load_collapsible_categories($event)
	{
		if (!isset($this->categories))
		{
			$this->categories = $this->operator->get_user_categories();
		}

		$cat_row = $event['cat_row'];
		$cat_row += array('S_FORUM_HIDDEN' => in_array('fid_' . $event['row']['forum_id'], $this->categories));
		$event['cat_row'] = $cat_row;
	}
}
