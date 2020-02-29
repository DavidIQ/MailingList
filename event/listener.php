<?php
/**
 * Mailing List extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 DavidIQ.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace davidiq\mailinglist\event;

use davidiq\mailinglist\managers\mailinglist_manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
    /** @var mailinglist_manager */
    protected $mailinglist_manager;

    /** @var string phpEx */
    protected $php_ext;

    /** @var string phpbb_root_path */
    protected $phpbb_root_path;

    /**
     * Constructor
     *
     * @param mailinglist_manager $mailinglist_manager Mailinglist manager class
     * @param string $phpbb_root_path Current phpBB root path
     * @param string $php_ext phpEx
     *
     * @access public
     */
    public function __construct(mailinglist_manager $mailinglist_manager, $phpbb_root_path, $php_ext)
    {
        $this->mailinglist_manager = $mailinglist_manager;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
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
            'core.notification_manager_add_notifications' => 'send_to_mailinglist',
        );
    }

    /**
     * Sends to mailing list when the post event applies
     *
     * @param object $event The event object
     * @access public
     */
    public function send_to_mailinglist($event)
    {
        $notification_type_name = $event['notification_type_name'];
        if (!in_array($notification_type_name, ['notification.type.topic', 'notification.type.post']))
        {
            return;
        }

        $new_topic = ($notification_type_name == 'notification.type.topic');
        $post_data = $event['data'];
        $mailinglists = $this->mailinglist_manager->get_forum_mailinglists($post_data['forum_id'], $new_topic);

        if (count($mailinglists))
        {
            foreach ($mailinglists as $mailinglist)
            {
                $email_data = array(
                    'AUTHOR_NAME' => htmlspecialchars_decode($post_data['post_username']),
                    'TOPIC_TITLE' => htmlspecialchars_decode($post_data['topic_title']),
                    'FORUM_NAME' => htmlspecialchars_decode($post_data['forum_name']),

                    'POST_CONTENTS' => $this->get_post_contents($post_data, $mailinglist),

                    'U_TOPIC' => generate_board_url() . "/viewtopic.{$this->php_ext}?f={$post_data['forum_id']}&t={$post_data['topic_id']}",
                    'U_POST' => generate_board_url() . "/viewtopic.{$this->php_ext}?p={$post_data['post_id']}#p{$post_data['post_id']}",
                    'U_FORUM' => generate_board_url() . "/viewforum.{$this->php_ext}?f={$post_data['forum_id']}",

                    'U_MAILINGLIST_UNSUBSCRIBE' => $mailinglist['mailinglist_unsubscribe'],
                );
                $template_name = 'mailinglist_new_' . ($new_topic ? 'topic' : 'post');

                $this->send_message($email_data, $template_name, $mailinglist);
            }
        }
    }

    /**
     * Gets the post contents in a format that can be read in an email, if configured to display
     *
     * @param $post_data array    The post data that needs to be processed to obtain the post contents
     * @param $mailinglist array  The mailinglist data to use
     * @return string
     */
    protected function get_post_contents($post_data, $mailinglist)
    {
        $processed_contents = '';
        if ($mailinglist['mailinglist_include_contents'])
        {
            if (!function_exists('generate_text_for_display'))
            {
                include($this->phpbb_root_path . 'includes/functions_content.' . $this->php_ext);
            }

            $flags = ($post_data['bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
            $processed_contents = generate_text_for_display($post_data['post_text'], $post_data['bbcode_uid'], $post_data['bbcode_bitfield'], $flags);
            $processed_contents = strip_tags(str_replace('<br />', "\n", $processed_contents));
        }

        return $processed_contents;
    }

    /**
     * Send email messages to defined mailing list
     *
     * @param array $message_data Array of message data
     * @param string $template The email template file to use
     * @param array $mailinglist Mailinglist data
     * @access protected
     */
    protected function send_message($message_data, $template, $mailinglist)
    {
        if (!class_exists('messenger'))
        {
            include($this->phpbb_root_path . 'includes/functions_messenger.' . $this->php_ext);
        }

        $messenger = new \messenger(false);
        $messenger->template('@davidiq_mailinglist/' . $template);
        $messenger->to($mailinglist['mailinglist_email']);
        $messenger->assign_vars($message_data);
        $messenger->send();
    }
}
