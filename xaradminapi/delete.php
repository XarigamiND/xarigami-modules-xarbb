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
 * @param $args['fid'] ID of the forum
 * @returns bool
 * @return true on success, false on failure
 */

function xarbb_adminapi_delete($args)
{
    extract($args);
    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count');
        throw new BadParameterException(null,$msg);
    }

    // The user API function is called.
    $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
    if (empty($data)) return;

    if (!xarSecurityCheck('DeletexarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;

    // topics and comments are deleted in delete gui func so do not care
    // shouldn't this call be here?

    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // Delete the item
    $query = "DELETE FROM $xbbforumstable WHERE xar_fid = ?";
    $result = $dbconn->Execute($query, array($fid));
    if (!$result) return;

    // Remove module variables in use.
    xarModDelVar('xarbb', 'f_' . $fid);
    xarModDelVar('xarbb', 'topics_' . $fid);

    // Let any hooks know that we have deleted a forum
    $args['module'] = 'xarbb';
    $args['itemtype'] = 0;
    $args['itemid'] = $fid;
    xarModCallHooks('item', 'delete', $fid, $args);

    // Let the calling process know that we have finished successfully
    return true;
}

?>