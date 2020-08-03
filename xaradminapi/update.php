<?php
/**
 * Update a forum
 *
 * @copyright (C) 2005-2008 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */
/**
 * update a forum
 * @param $args['fid'] the ID of the link
 * @param $args['fname'] the new keyword of the link
 * @param $args['fdesc'] the new title of the link
 */

function xarbb_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($fid) || !isset($fname) || !isset($fdesc)) {
        $msg = xarML('Invalid parameter count');
        throw new BadParameterException(null,$msg);
    }

    // The user API function is called.
    $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
    if (empty($data)) return;

    // Security Check
    if(!xarSecurityCheck('EditxarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;

    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $xbbforumstable = $xartable['xbbforums'];

    // Update the forum
    $query = "UPDATE $xbbforumstable"
        . " SET xar_fname = ?, xar_fdesc = ?, xar_fstatus = ?"
        . " WHERE xar_fid = ?";
    $result = $dbconn->Execute($query, array((string)$fname, (string)$fdesc, (int)$fstatus, (int)$fid));
    if (!$result) return;

    // Default categories is the master categories here
    // catch common mistake of using array('') instead of array()
    if (empty($cids) || !is_array($cids) || (count($cids) > 0 && empty($cids[0]))) {
        // Set them to the master categories
        $cids = explode(';', xarModGetVar('xarbb', 'mastercids'));
    }

    // Let any hooks know that we have modified a forum itemtype
    $args['module'] = 'xarbb';
    $args['itemtype'] = 0; // forum
    $args['itemid'] = $fid;
    $args['cids'] = $cids;
    xarModCallHooks('item', 'update', $fid, $args);

    // Let the calling process know that we have finished successfully
    return true;
}

?>