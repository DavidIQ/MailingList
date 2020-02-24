<?php
/**
* Mailing List extension for the phpBB Forum Software package.
*
* @copyright (c) 2020 DavidIQ.com
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace davidiq\mailinglist\migrations\v20x;

/**
* Migration for v2: Initial table creation
*/
class add_mailinglist_table extends \phpbb\db\migration\migration
{
   static public function depends_on()
	{
		return [
			'\davidiq\mailinglist\migrations\v10x\initial_data',
		];
	}

	/**
	* Add the mailing lists tables.
	*
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return [
			'add_tables'	=> [
				$this->table_prefix . 'mailinglists'	=> [
					'COLUMNS'	=> [
						'mailinglist_id'				      => ['UINT', null, 'auto_increment'],
						'mailinglist_email'			      => ['VCHAR:500', ''],
						'mailinglist_post_type'		      => ['UINT', 0],
						'mailinglist_include_contents'   => ['BOOL', 0],
						'mailinglist_unsubscribe'			=> ['VCHAR:500', ''],
               ],
					'PRIMARY_KEY'	=> 'mailinglist_id',
				],
            $this->table_prefix . 'mailinglists_forums'  => [
               'COLUMNS'   => [
                  'mailinglist_id'           => ['UINT', 0],
                  'mailinglist_forum_id'     => ['UINT', 0],
               ],
            ],
			],
		];
	}

   /**
	* Drop the mailing lists tables.
	*
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return [
			'drop_tables'	=> [
				$this->table_prefix . 'mailinglists',
            $this->table_prefix . 'mailinglists_forums',
			],
		];
	}
}
