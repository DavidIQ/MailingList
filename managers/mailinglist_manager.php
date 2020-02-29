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
    const NEW_POSTS = 1;
    const NEW_TOPICS = 2;
    const NEW_POSTS_NEW_TOPICS = 3;

    /** @var array */
    public $post_types = [self::NEW_POSTS => 'NEW_POSTS', self::NEW_TOPICS => 'NEW_TOPICS', self::NEW_POSTS_NEW_TOPICS => 'NEW_POSTS_NEW_TOPICS'];

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
     * @param \phpbb\cache\driver\driver_interface $cache Cache driver interface
     * @param \phpbb\db\driver\driver_interface $db Database connection
     * @param string $root_path Root path
     * @param string $php_ext PHP extension
     * @param string $mailinglists_table Table name
     * @param string $mailinglists_forums_table Table name
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
                $mailinglists_data[$row['mailinglist_id']] = $row;
            }

            $this->db->sql_freeresult($result);
            $this->cache->put(self::DATA_CACHE_KEY, $mailinglists_data);
        }

        return $mailinglists_data;
    }

    /**
     * Get data for a specific mailing list
     *
     * @param $mailinglist_id integer  The mailinglist ID
     * @return array | null
     */
    public function get_mailing_list($mailinglist_id)
    {
        $mailinglist_data = $this->get_mailinglists_data();
        if (isset($mailinglist_data[$mailinglist_id]))
        {
            return $mailinglist_data[$mailinglist_id];
        }
        return null;
    }

    /**
     * Get the mailinglists that apply to the forum
     *
     * @param $forum_id integer  The forum ID
     * @param $new_topic bool    Indicates if it is a new topic
     * @return array
     */
    public function get_forum_mailinglists($forum_id, $new_topic)
    {
        $mailinglist_data = $this->get_mailinglists_data();
        $forum_mailinglists = array();

        foreach ($mailinglist_data as $mailinglist)
        {
            if (($new_topic && $mailinglist['mailinglist_post_type'] == self::NEW_POSTS) ||
                (!$new_topic && $mailinglist['mailinglist_post_type'] == self::NEW_TOPICS))
            {
                continue;
            }
            $forum_ids = array_column($mailinglist['forums'], 'mailinglist_forum_id');
            if (!count($forum_ids) || in_array($forum_id, $forum_ids))
            {
                $forum_mailinglists[] = $mailinglist;
            }
        }

        return $forum_mailinglists;
    }

    /**
     * Inserts a mailinglist
     *
     * @param $data array  The data to insert
     */
    public function insert_mailinglist($data)
    {
        $forum_ids = $data['forum_ids'];
        unset($data['forum_ids']);

        $sql = "INSERT INTO {$this->mailinglists_table} " . $this->db->sql_build_array('INSERT', $data);
        $this->db->sql_query($sql);
        $mailinglist_id = (int)$this->db->sql_nextid();

        $this->insert_mailinglist_forums($mailinglist_id, $forum_ids);

        $this->cache->destroy(self::DATA_CACHE_KEY);
    }

    /**
     * Update an existing mailinglist
     *
     * @param $data array  The data to update
     * @param $mailinglist_id integer  The mailinglist ID to update
     */
    public function update_mailinglist($data, $mailinglist_id)
    {
        $forum_ids = $data['forum_ids'];
        unset($data['forum_ids']);

        $this->remove_mailinglist_forums($mailinglist_id);
        $sql = "UPDATE {$this->mailinglists_table} SET " . $this->db->sql_build_array('UPDATE', $data) .
            ' WHERE mailinglist_id = ' . (int)$mailinglist_id;
        $this->db->sql_query($sql);

        $this->insert_mailinglist_forums($mailinglist_id, $forum_ids);

        $this->cache->destroy(self::DATA_CACHE_KEY);
    }

    /**
     * Delete a mailinglist
     *
     * @param $mailinglist_id integer  The mailinglist ID
     */
    public function delete_mailinglist($mailinglist_id)
    {
        $this->remove_mailinglist_forums($mailinglist_id);
        $sql = "DELETE FROM {$this->mailinglists_table} WHERE mailinglist_id = " . (int)$mailinglist_id;
        $this->db->sql_query($sql);
        $this->cache->destroy(self::DATA_CACHE_KEY);
    }

    private function remove_mailinglist_forums($mailinglist_id)
    {

        $sql = "DELETE FROM {$this->mailinglists_forums_table}
               WHERE mailinglist_id = " . (int)$mailinglist_id;
        $this->db->sql_query($sql);
    }

    private function insert_mailinglist_forums($mailinglist_id, $forum_ids)
    {
        foreach ($forum_ids as $forum_id)
        {
            $sql = "INSERT INTO {$this->mailinglists_forums_table} " . $this->db->sql_build_array('INSERT',
                    [
                        'mailinglist_id' => $mailinglist_id,
                        'mailinglist_forum_id' => $forum_id
                    ]);
            $this->db->sql_query($sql);
        }
    }
}
