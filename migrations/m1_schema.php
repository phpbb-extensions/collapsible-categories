<?php
/**
*
* Collapsible Categories extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\collapsiblecategories\migrations;

/**
 * Migration stage 1: Schema changes
 */
class m1_schema extends \phpbb\db\migration\migration
{
	/**
	 * Check if this migration is effectively installed
	 *
	 * @return bool True if this migration is installed, False if this migration is not installed
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'collapsible_categories');
	}

	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 */
	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v313');
	}

	/**
	 * Add the collapsible_categories column to the users table
	 *
	 * @return array Array of table schema
	 */
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'collapsible_categories'	=> array('TEXT', null),
				),
			),
		);
	}

	/**
	 * Drop the collapsible_categories column from the users table
	 *
	 * @return array Array of table schema
	 */
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'collapsible_categories',
				),
			),
		);
	}
}
