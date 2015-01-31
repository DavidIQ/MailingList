<?php
/**
* Mailing List extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 DavidIQ.com
* @license GNU General Public License, version 2 (GPL-2.0)
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// NOTE TO TRANSLATORS:  Text in parenthesis refers to keys on the keyboard

$lang = array_merge($lang, array(
	'ACP_MAILINGLIST_SETTINGS'			=> 'Mailing list settings',
	'ACP_MAILINGLIST_SETTINGS_EXPLAIN'	=> 'This extension will send notification to a specified mailing list when a new post or reply is created.',

	'MAILINGLIST_OPTIONS'				=> 'Mailing list options',

	'MAILINGLIST_EMAIL'					=> 'Mailing list email',
	'MAILINGLIST_EMAIL_EXPLAIN'			=> 'This will be the mailing list to which notifications will be sent',
	'MAILINGLIST_POST_TYPE'				=> 'Post type',
	'MAILINGLIST_POST_TYPE_EXPLAIN'		=> 'Select the type of posts for which to send notifications to the mailing list.',

	'NEW POSTS'							=> 'New posts',
	'NEW_TOPICS'						=> 'New topics',
	'NEW_POSTS_NEW_TOPICS'				=> 'New posts & New topics',

	'MAILINGLIST_INCLUDE_CONTENTS'		=> 'Include post contents',
	'MAILINGLIST_INCLUDE_CONTENTS_EXPLAIN'	=> 'If set to yes then post contents will be sent in the email.',
	'MAILINGLIST_UNSUBSCRIBE'			=> 'Mailing list unsubscribe link',
	'MAILINGLIST_UNSUBSCRIBE_EXPLAIN'	=> 'This will be the link shown at the bottom of mailing list emails so that a user can unsubscribe through another page/service.',

	'MAILINGLIST_UPDATED'				=> 'Mailing List settings have been updated.',

));
