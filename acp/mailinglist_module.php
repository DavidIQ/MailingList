<?php
/**
* Mailing List extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 DavidIQ.com
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace davidiq\mailinglist\acp;

class mailinglist_module
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var ContainerInterface */
	protected $phpbb_container;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	public $u_action;

	function main($id, $mode)
	{
		global $user, $template, $cache, $config, $phpbb_root_path, $phpEx, $phpbb_container, $request;

		$this->config = $config;
		$this->phpbb_container = $phpbb_container;
		$this->config_text = $this->phpbb_container->get('config_text');
		$this->log = $this->phpbb_container->get('log');
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;

		$this->user->add_lang_ext('davidiq/mailinglist', 'mailinglist_acp');

		$this->tpl_name = 'mailinglist';
		$this->page_title = 'ACP_MAILINGLIST_SETTINGS';

		$form_name = 'acp_mailinglist';
		add_form_key($form_name);

		$mailinglist_email = $this->request->variable('mailinglist_email', $this->config['mailinglist_email']);
		$mailinglist_post_type = $this->request->variable('mailinglist_post_type', (int)$this->config['mailinglist_post_type']);
		$mailinglist_include_contents = $this->request->variable('mailinglist_include_contents', (bool)$this->config['mailinglist_include_contents']);
		$mailinglist_unsubscribe = $this->request->variable('mailinglist_unsubscribe', $this->config['mailinglist_unsubscribe']);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key($form_name))
			{
				trigger_error($this->user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			//Update configuration now
			//$this->config->set('mailinglist_email', $mailinglist_email);
			//$this->config->set('mailinglist_post_type', $mailinglist_post_type);
			//$this->config->set('mailinglist_include_contents', $mailinglist_include_contents);
			//$this->config->set('mailinglist_unsubscribe', $mailinglist_unsubscribe);

			//$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MAILINGLIST_UPDATED');
			trigger_error($user->lang['MAILINGLIST_UPDATED'] . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'S_MAILINGLIST_EMAIL'				=> $mailinglist_email,
			'S_MAILINGLIST_POST_TYPE'			=> $mailinglist_post_type,
			'S_MAILINGLIST_INCLUDE_CONTENTS'	=> $mailinglist_include_contents,
			'S_MAILINGLIST_UNSUBSCRIBE'			=> $mailinglist_unsubscribe,

			'U_ACTION'							=> $this->u_action,
		));
	}
}
