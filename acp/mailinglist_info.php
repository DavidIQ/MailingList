<?php
/**
* Mailing List extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 DavidIQ.com
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace davidiq\mailinglist\acp;

class mailinglist_info
{
	function module()
	{
		return array(
			'filename'	=> '\davidiq\mailinglist\acp\mailinglist_module',
			'title'		=> 'ACP_MAILINGLIST_SETTINGS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'main'		=> array(
						'title' => 'ACP_MAILINGLIST_SETTINGS',
						'auth' => 'ext_davidiq/mailinglist && acl_a_board',
						'cat' 	=> array('ACP_CAT_MAILINGLIST'),
				),
			),
		);
	}
}
