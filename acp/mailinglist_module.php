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
    /** @var string */
    public $tpl_name;

    /** @var string */
    public $page_title;

    /** @var string */
    public $u_action;

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

    /** @var \davidiq\mailinglist\managers\mailinglist_manager */
    protected $mailinglist_manager;

    function main($id, $mode)
    {
        global $user, $template, $phpbb_root_path, $phpEx, $phpbb_container, $request;

        $this->phpbb_container = $phpbb_container;
        $this->log = $this->phpbb_container->get('log');
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $phpEx;
        $this->mailinglist_manager = $phpbb_container->get('davidiq.mailinglist.manager');

        $this->user->add_lang_ext('davidiq/mailinglist', 'mailinglist_acp');

        $this->tpl_name = 'mailinglist';
        $this->page_title = 'ACP_MAILINGLIST_SETTINGS';

        $errors = array();
        $form_name = 'acp_mailinglist';
        $action = $this->request->is_set_post('cancel') ? '' : $this->request->variable('action', '');
        $mailinglist_id = $this->request->variable('mailinglist_id', 0);
        $submit = $this->request->is_set_post('submit');

        switch ($action)
        {
            case 'add':
            case 'edit':

                add_form_key($form_name);
                $mailinglist_all_forums = false;
                $mailinglist_data = [
                    'mailinglist_email' => '',
                    'mailinglist_post_type' => 1,
                    'mailinglist_include_contents' => false,
                    'mailinglist_unsubscribe' => '',
                    'forum_ids' => [],
                ];

                if ($submit)
                {
                    if (!check_form_key($form_name))
                    {
                        trigger_error($this->user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
                    }

                    $mailinglist_data['mailinglist_email'] = $this->request->variable('mailinglist_email', '');
                    $mailinglist_all_forums = $this->request->variable('all_forums', false);
                    if (!$mailinglist_all_forums)
                    {
                        $mailinglist_data['forum_ids'] = $this->request->variable('mailinglist_forum_ids', [0]);
                    }

                    if (empty($mailinglist_data['mailinglist_email']))
                    {
                        $errors[] = $this->user->lang('MAILINGLIST_EMAIL_REQUIRED');
                    }

                    if (!count($mailinglist_data['forum_ids']) && !$mailinglist_all_forums)
                    {
                        $errors[] = $this->user->lang('MAILINGLIST_FORUM_OPTION_REQUIRED');
                    }

                    if (empty($errors))
                    {
                        $mailinglist_data['mailinglist_post_type'] = $this->request->variable('mailinglist_post_type', 1);
                        $mailinglist_data['mailinglist_include_contents'] = $this->request->variable('mailinglist_include_contents', false);
                        $mailinglist_data['mailinglist_unsubscribe'] = $this->request->variable('mailinglist_unsubscribe', '');

                        // Save
                        if ($mailinglist_id)
                        {
                            $this->mailinglist_manager->update_mailinglist($mailinglist_data, $mailinglist_id);
                            $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MAILINGLIST_UPDATED', false, [$mailinglist_data['mailinglist_email']]);
                            trigger_error(sprintf($this->user->lang('MAILINGLIST_UPDATED'), $mailinglist_data['mailinglist_email']) . adm_back_link($this->u_action));
                        } else
                        {
                            $this->mailinglist_manager->insert_mailinglist($mailinglist_data);
                            $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MAILINGLIST_CREATED', false, [$mailinglist_data['mailinglist_email']]);
                            trigger_error(sprintf($this->user->lang('MAILINGLIST_CREATED'), $mailinglist_data['mailinglist_email']) . adm_back_link($this->u_action));
                        }

                    }
                } else if ($action === 'edit')
                {
                    $mailinglist_data = $this->mailinglist_manager->get_mailing_list($mailinglist_id);
                    $mailinglist_data['forum_ids'] = array_column($mailinglist_data['forums'], 'mailinglist_forum_id');
                    $mailinglist_all_forums = !count($mailinglist_data['forum_ids']);
                }

                $this->template->assign_vars([
                    'S_MAILINGLIST_EMAIL' => $mailinglist_data['mailinglist_email'],
                    'S_ALL_FORUMS' => $mailinglist_all_forums,
                    'S_MAILINGLIST_POST_TYPE' => $mailinglist_data['mailinglist_post_type'],
                    'S_MAILINGLIST_INCLUDE_CONTENTS' => $mailinglist_data['mailinglist_include_contents'],
                    'S_MAILINGLIST_UNSUBSCRIBE' => $mailinglist_data['mailinglist_unsubscribe'],

                    'S_ADD_MAILINGLIST' => $action === 'add',
                    'S_EDIT_MAILINGLIST' => $action === 'edit',
                    'S_FORUM_OPTIONS' => make_forum_select(count($mailinglist_data['forum_ids']) ? $mailinglist_data['forum_ids'] : false, false, false, true),
                    'S_ERROR' => count($errors),
                    'ERROR_MSG' => count($errors) ? implode('<br>', $errors) : '',
                ]);
                break;

            case 'delete':
                if (confirm_box(true))
                {
                    $mailinglist_data = $this->mailinglist_manager->get_mailing_list($mailinglist_id);
                    $this->mailinglist_manager->delete_mailinglist($mailinglist_id);
                    $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MAILINGLIST_DELETED', false, [$mailinglist_data['mailinglist_email']]);
                    trigger_error(sprintf($this->user->lang('MAILINGLIST_DELETED'), $mailinglist_data['mailinglist_email']) . adm_back_link($this->u_action));
                } else
                {
                    $s_hidden_fields = array(
                        'submit' => $submit,
                        'mailinglist_id' => $mailinglist_id,
                    );

                    $s_hidden_fields = build_hidden_fields($s_hidden_fields);
                    confirm_box(false, $this->user->lang('MAILINGLIST_DELETE_CONFIRM'), $s_hidden_fields);
                }
                break;

            default:
                $mailinglists_data = $this->mailinglist_manager->get_mailinglists_data();

                foreach ($mailinglists_data as $data)
                {
                    $forum_names = array_column($data['forums'], 'forum_name');
                    $this->template->assign_block_vars('mailinglists',
                        [
                            'EMAIL' => $data['mailinglist_email'],
                            'FORUMS' => count($data['forums']) ? implode(', ', $forum_names) : false,
                            'POST_TYPE' => $this->mailinglist_manager->post_types[$data['mailinglist_post_type']],
                            'INCLUDE_CONTENTS' => $data['mailinglist_include_contents'] ? 'YES' : 'NO',
                            'UNSUBSCRIBE' => !empty($data['mailinglist_unsubscribe']) ? 'YES' : 'NO',
                            'U_EDIT' => "{$this->u_action}&amp;action=edit&amp;mailinglist_id=" . $data['mailinglist_id'],
                            'U_DELETE' => "{$this->u_action}&amp;action=delete&amp;mailinglist_id=" . $data['mailinglist_id'],
                            'U_ADD' => "{$this->u_action}&amp;action=add"
                        ]
                    );
                }

        }

        $this->template->assign_var('U_ACTION', $this->u_action . (($mailinglist_id) ? "&amp;mailinglist_id={$mailinglist_id}" : ''));
    }
}
