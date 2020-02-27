<?php
/**
* Mailing List extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 DavidIQ.com
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace davidiq\mailinglist\managers;

class mailinglist_manager
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $mailinglists_table;

	/** @var string */
	protected $mailinglists_forums_table;

	const DATA_CACHE_KEY = '_mailinglist_data';

   /**
	* Constructor
	*
	* @param \phpbb\cache\driver\driver_interface $cache                     Cache driver interface
	* @param \phpbb\db\driver\driver_interface    $db                        Database connection
	* @param string                               $root_path                 Root path
	* @param string                               $php_ext                   PHP extension
   * @param string                               $mailinglists_table        Table name
	* @param string                               $mailinglists_forums_table Table name
	* @access public
	*/
	public function __construct(\phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, $root_path, $php_ext, $mailinglists_table, $mailinglists_forums_table)
	{
      $this->cache = $cache;
      $this->db = $db;
      $this->mailinglists_table = $mailinglists_table;
      $this->mailinglists_forums_table = $mailinglists_forums_table;
   }

   /**
    * Get all mailinglist data
    *
    * @return array|mixed
    */
   public function get_mailinglists_data()
   {
      if (($mailinglists_data = $this->cache->get(self::DATA_CACHE_KEY)) === false)
      {
         $mailinglists_data = array();
         $sql = "SELECT
                     mailinglist_id, 
                     mailinglist_email,
                     mailinglist_post_type,
                     mailinglist_include_contents,
                     mailinglist_unsubscribe
           FROM {$this->mailinglists_table}";
         $result = $this->db->sql_query($sql);

         while ($row = $this->db->sql_fetchrow($result))
         {
            $sql2 = "SELECT m.mailinglist_forum_id, f.forum_name
               FROM {$this->mailinglists_forums_table} m
               JOIN " . FORUMS_TABLE . " f ON f.forum_id = m.mailinglist_forum_id
               WHERE m.mailinglist_id = {$row['mailinglist_id']}";
            $result2 = $this->db->sql_query($sql2);
            $row['forums'] = $this->db->sql_fetchrowset($result2);
            $this->db->sql_freeresult($result2);
            $mailinglists_data[] = $row;
         }

         $this->db->sql_freeresult($result);
         //$this->cache->put(self::DATA_CACHE_KEY, $mailinglists_data);
      }

      return $mailinglists_data;
   }

   /**
    * Get data for a specific mailing list
    *
    * @param $mailinglist_id integer  The mailinglist ID
    * @return mixed
    */
   public function get_mailing_list($mailinglist_id)
   {
         $sql = "SELECT
                     mailinglist_id, 
                     mailinglist_email,
                     mailinglist_post_type,
                     mailinglist_include_contents,
                     mailinglist_unsubscribe
           FROM {$this->mailinglists_table}
           WHERE mailinglist_id = " . (int) $mailinglist_id;
         $result = $this->db->sql_query_limit($sql, 1);
         $mailinglist_data = $this->db->sql_fetchrow($result);
         $this->db->sql_freeresult($result);

         $sql = "SELECT
                  mailinglist_id,
                  mailinglist_forum_id
               FROM {$this->mailinglists_forums_table}
               WHERE mailinglist_id = " . (int) $mailinglist_id;
         $result = $this->db->sql_query($sql);
         $mailinglist_data['forums'] = $this->db->sql_fetchrowset($result);
         $this->db->sql_freeresult($result);

         return $mailinglist_data;
   }
}
