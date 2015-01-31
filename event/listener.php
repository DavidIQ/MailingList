<?php
/**
* Mailing List extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 DavidIQ.com
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace davidiq\mailinglist\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\config\config        	$config             Config object
	* @param string							$php_ext			phpEx
	* @return \davidiq\mailinglist\event\listener
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, $php_ext)
	{
		$this->config = $config;
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
			'core.notification_manager_add_notifications'	=> 'send_to_mailinglist',
		);
	}

	/**
	* Sends to mailing list when the post event applies
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function send_to_mailinglist($event)
	{

	}

}
