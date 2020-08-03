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
 * Get a list of authors who have replied to topics.
 *
 * @author Jason Judge
 * @access public
 * @param ip string The IP address the reply/comment was made from
 * @todo: we may want to know authors having replied to specific forums, and perhaps order by rank.
 */

function xarbb_userapi_get_topic_authors($args)
{
    extract($args);

    // Optional argument for Pager -
    // for those modules that use comments and require this
    if (!isset($startnum)) $startnum = 1;
    if (!isset($numitems)) $numitems = -1;

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();

    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT t_tab.xar_thostname, t_tab.xar_tposter, COUNT(t_tab.xar_tposter) AS t_tab_uid_count"
        . " FROM $xartable[xbbtopics] AS t_tab";
    $bindvars = array();

    if (isset($ip) && $ip > 0) {
        $sql .= " WHERE t_tab.xar_thostname = ? ";
        $bindvars[] = (string)$ip;
    }

    $sql .= " GROUP BY t_tab.xar_thostname, t_tab.xar_tposter ORDER BY t_tab_uid_count";

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems) && is_numeric($numitems)) {
        $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    } else {
        $result = $dbconn->Execute($sql, $bindvars);
    }

    //$result = $dbconn->Execute($sql);
    if (!$result) return;

    // initialize the commentlist array
    $authorlist = array();

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        list($hostname, $author, $author_count) = $result->fields;
        $authorlist[] = array(
            'ip' => $hostname,
            'uid' => $author,
            'uid_count' => $author_count,
        );
        $result->MoveNext();
    }
    $result->Close();

    return $authorlist;
}

?>