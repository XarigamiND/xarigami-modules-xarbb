<?php
/**
 * XarBB - A lightweight BB for Xarigami
 *
 * @copyright (C) 2005-2008 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */
/**
 * @returns integer
 * @todo extend this count function so can count posts in forums, topics, for users etc.
 * @todo Doh! Why are there so many different API functions for counting the same things!
 */

function xarbb_userapi_countposts($args)
{
    static $countcache = array();
    extract($args);

    if (!isset($uid)) {
        $msg = xarML('Invalid Parameter Count');
        throw new BadParameterException(null,$msg);
    }

    if (isset($countcache[$uid])) return $countcache[$uid];

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $xbbtopicstable = $xartable['xbbtopics'];

    $query = "SELECT COUNT(1) FROM $xbbtopicstable WHERE xar_tposter = ?";
    $result = $dbconn->Execute($query,array((int)$uid));
    if (!$result) return;

    list($topics) = $result->fields;
    $result->Close();

    // While we are here, how many replies have been made as well?
    $replies = xarModAPIFunc(
        'comments', 'user', 'get_author_count',
        array('modid' => xarModGetIdFromName('xarbb'), 'author' => $uid)
    );

    $total = $topics + $replies;
    $countcache[$uid] = $total;

    return $total;
}

?>