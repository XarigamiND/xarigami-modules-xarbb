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
 * get the previous topic id in a forum
 * @param $args['tid'] id of next topic, or
 * @param $args['fid'] id of the forum +
 * @param $args['ttime'] time of next topic
 * @param $args['sort'] sort criteria in the forum (TODO - default ttime)
 * @returns array
 * @return link array, or false on failure
 */
function xarbb_userapi_getprevioustopicid($args)
{
    extract($args);

    if (empty($tid) && (empty($fid) || empty($ttime))) {
        $msg = xarML('Invalid parameter count');
         throw new BadParameterException(null,$msg);
    }

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    if (!empty($fid) && !empty($ttime)) {
        $query = "SELECT xar_tid
                  FROM $xbbtopicstable
                  WHERE xar_fid = ? AND xar_ttime < ?
                  ORDER BY xar_ttime DESC";
        $result = $dbconn->SelectLimit($query, 1, 0, array((int)$fid,(int)$ttime));
    } else {
        $query = "SELECT t1.xar_tid
                  FROM $xbbtopicstable t1, $xbbtopicstable t2
                  WHERE t2.xar_tid = ? AND t1.xar_fid = t2.xar_fid AND t1.xar_ttime < t2.xar_ttime
                  ORDER BY t1.xar_ttime DESC";
        $result = $dbconn->SelectLimit($query, 1, 0, array((int)$tid));
    }
    if (!$result) return;

    if ($result->EOF) {
        // no next topic
        return 0;
    }

    $topicid = $result->fields[0];

    $result->Close();

    // let viewtopic worry about security check - we're only getting a topicid here
    //    if (!xarSecurityCheck('ReadxarBB',1,'Forum',"$catid:$fid")) return;

    return $topicid;
}

?>