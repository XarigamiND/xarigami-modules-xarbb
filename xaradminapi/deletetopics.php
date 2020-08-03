<?php

/**
 * Delete a forum
 *
 * @copyright (C) 2005-2008 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */

/**
 * delete a forum
 * @param $args['tids'] Array( IDs ) of the forum   or $args['tid'] ID
 * @returns bool
 * @return true on success, false on failure
 */

function xarbb_adminapi_deletetopics($args)
{
    extract($args);

    // Argument check
    if ((!isset($tids) || !is_array($tids) || count($tids) == 0) && (!isset($tid) || !($tid > 0))) {
        $msg = xarML('Invalid Parameter count');
          throw new BadParameterException(null,$msg);
    }

    if (!isset($tids)) $tids = array($tid);

    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $xbbtopicstable = $xartable['xbbtopics'];
    foreach($tids as $tid) {
        // get forum id
        $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
        if (empty($topic)) return;

        // Item Specific Security Check
        if (!xarSecurityCheck('ModxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) continue;

        // Delete comments
        if (!xarModAPIFunc('xarbb', 'admin', 'deleteallreplies', array('tid' => $tid))) return;

        // Delete the item
        $query = "DELETE FROM $xbbtopicstable WHERE xar_tid = ?";
        $result = $dbconn->Execute($query, array($tid));
        if (!$result) return;

        // Let any hooks know that we have deleted a topic
        $args['module'] = 'xarbb';
        $args['itemtype'] = $topic['fid'];
        $args['itemid']= $tid;
        xarModCallHooks('item', 'delete', $tid, $args);
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>