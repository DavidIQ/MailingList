<?php
/**
* Mailing List extension for the phpBB Forum Software package.
*
* @copyright (c) 2020 DavidIQ.com
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace davidiq\mailinglist\migrations\v20x;

/**
* Migration stage 1: Initial data changes to the database
*/
class v200 extends \phpbb\db\migration\migration
{
   static public function depends_on()
	{
		return [
			'\davidiq\mailinglist\migrations\v20x\add_mailinglist_table',
		];
	}

	public function update_data()
	{
		return [
		   ['custom', [[$this, 'export_config_to_table']]],
			['config.remove', ['mailinglist_email']],
         ['config.remove', ['mailinglist_post_type']],
         ['config.remove', ['mailinglist_include_contents']],
         ['config.remove', ['mailinglist_unsubscribe']],
      ];
	}

	public function export_config_to_table()
   {
      if (empty($this->config['mailinglist_email']))
      {
         return;
      }

      $sql_ary = [
         'mailinglist_email'	            => $this->config['mailinglist_email'],
         'mailinglist_post_type'			   => (int) $this->config['mailinglist_post_type'],
         'mailinglist_include_contents'	=> (bool) $this->config['mailinglist_include_contents'],
         'mailinglist_unsubscribe'	      => $this->config['mailinglist_unsubscribe']
      ];

      $this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'mailinglists ' . $this->db->sql_build_array('INSERT', $sql_ary));
   }
}
