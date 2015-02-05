<?php
/**
* Mailing List extension for the phpBB Forum Software package.
* Swedish translation by Holger (http://www.maskinisten.net)
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
	'ACP_MAILINGLIST_SETTINGS'			=> 'Inställningar för mailinglistan',
	'ACP_MAILINGLIST_SETTINGS_EXPLAIN'	=> 'Detta tillägg skickar ut meddelanden till en fördefinerad mailinglista när ett nytt inlägg eller svar skrivs.',

	'MAILINGLIST_OPTIONS'				=> 'Inställningar för mailinglistan',

	'MAILINGLIST_EMAIL'					=> 'Mailinglistans e-postadress',
	'MAILINGLIST_EMAIL_EXPLAIN'			=> 'E-postadressen som meddelandena skickas till',
	'MAILINGLIST_POST_TYPE'				=> 'Inläggstyp',
	'MAILINGLIST_POST_TYPE_EXPLAIN'		=> 'Välj för vilken typ av inlägg som meddelandena ska skickas till mailinglistan.',

	'NEW POSTS'							=> 'Nya inlägg',
	'NEW_TOPICS'						=> 'Nya trådar',
	'NEW_POSTS_NEW_TOPICS'				=> 'Nya inlägg och nya trådar',

	'MAILINGLIST_INCLUDE_CONTENTS'		=> 'Bifoga inläggets innehåll',
	'MAILINGLIST_INCLUDE_CONTENTS_EXPLAIN'	=> 'Om detta ställs in till ja så bifogas även inläggens innehåll till e-postmeddelandet.',
	'MAILINGLIST_UNSUBSCRIBE'			=> 'Mailingslistans avbeställningslänk',
	'MAILINGLIST_UNSUBSCRIBE_EXPLAIN'	=> 'Denna länk visas längst ner i e-postmeddelandena så att mottagarna kan avbeställa mailinglistan.',

	'MAILINGLIST_UPDATED'				=> 'Inställningarna för mailinglistan har uppdaterats.',

));
