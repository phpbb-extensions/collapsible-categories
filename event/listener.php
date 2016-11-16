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

use phpbb\collapsiblecategories\operator\operator_interface;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\collapsiblecategories\operator\operator_interface */
	protected $operator;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	 * Constructor
	 *
	 * @param \phpbb\collapsiblecategories\operator\operator_interface $operator Collapsible categories operator object
	 * @param \phpbb\template\template                                 $template Template object
	 */
	public function __construct(operator_interface $operator, template $template)
	{
		$this->operator = $operator;
		$this->template = $template;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.display_forums_modify_category_template_vars'	=> 'show_collapsible_categories',
			'core.display_forums_modify_template_vars'			=> 'show_collapsible_categories',
		);
	}

	/**
	 * Set category display states
	 *
	 * @param \phpbb\event\data $event The event object
	 *
	 * @return void
	 */
	public function show_collapsible_categories($event)
	{
		$fid = 'fid_' . $event['row']['forum_id'];
		$row = isset($event['cat_row']) ? 'cat_row' : 'forum_row';
		$event_row = $event[$row];
		$event_row = array_merge($event_row, array(
			'S_FORUM_HIDDEN'	=> $this->operator->is_collapsed($fid),
			'U_COLLAPSE_URL'	=> $this->operator->get_collapsible_link($fid),
		));
		$event[$row] = $event_row;
	}
}
