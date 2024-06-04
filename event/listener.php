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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var operator_interface */
	protected $operator;

	/**
	 * Constructor
	 *
	 * @param operator_interface $operator Collapsible categories operator object
	 */
	public function __construct(operator_interface $operator)
	{
		$this->operator = $operator;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'									=> 'load_language_on_setup',
			'core.display_forums_modify_category_template_vars'	=> 'show_collapsible_categories',
			'core.display_forums_modify_template_vars'			=> 'show_collapsible_categories',
		);
	}

	/**
	 * Load common language file during user setup
	 *
	 * @param \phpbb\event\data $event The event object
	 *
	 * @return void
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'phpbb/collapsiblecategories',
			'lang_set' => 'collapsiblecategories',
		);
		$event['lang_set_ext'] = $lang_set_ext;
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
