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
 * Re-synchronise forums and topics
 *
 * @param $args['fid'] int forum id (optional)
 * @param $args['withtopics'] bool update topics too (optional, default false)
 * @return void
*/
function xarbb_admin_sync($args)
{
    // Security Check
    if (!xarSecurityCheck('EditxarBB', 1, 'Forum')) return;

    extract($args);

    // Get parameters
    if (!xarVarFetch('fid','id', $fid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('withtopics','bool', $withtopics, false, XARVAR_NOT_REQUIRED)) return;
    xarSessionSetVar('statusmsg', '');

    // Pass arguments to the API function
    $syncresult = xarModAPIFunc('xarbb', 'admin', 'sync', array('fid' => $fid, 'withtopics' => $withtopics));
    if (empty($syncresult)) {
        if ($withtopics) {
            xarSessionSetVar('statusmsg', xarML('Problem syncing forum topics'));
        } else {
            xarSessionSetVar('statusmsg', xarML('Problem syncing forum'));
        }

        return;
    }
    if ($withtopics) {
        xarSessionSetVar('statusmsg', xarML('Forum topics successfully synced'));
    } else {
        xarSessionSetVar('statusmsg', xarML('Forum successfully synced'));
    }

    // redirect
    //xarResponseRedirect(xarModURL('xarbb', 'admin', 'modify', array('fid'=> $fid)));
    xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
    return true;
}
?>