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
 * @author John Cox
 * @function to delete a forum and related topics
 */
function xarbb_admin_delete($args)
{
    extract($args);

    // Get parameters
    if (!xarVarFetch('fid', 'int:1:', $fid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation', 'str:1:', $confirmation, '', XARVAR_NOT_REQUIRED)) return;


    // The user API function is called.
    $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));

    if (empty($data)) return;

    // Security Check
    if (!xarSecurityCheck('DeletexarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        // for forums that lost their category
        if (!isset($data['fid'])) {
            $data['fid'] = $fid;
        }

        $data['authid'] = xarSecGenAuthKey();

        // For Tabs:
        // The user API function is called
        $links = xarModAPIFunc('xarbb', 'user', 'getallforums');
        $totlinks = count($links);

        // Check individual permissions for Edit / Delete
        for ($i = 0; $i < $totlinks; $i++) {
            $link = $links[$i];

            if (xarSecurityCheck('EditxarBB', 0)) {
                $links[$i]['editurl'] = xarModURL('xarbb', 'admin', 'modify', array('fid' => $link['fid']));
            } else {
                $links[$i]['editurl'] = '';
            }

        }
        // Add the array of items to the template variables
        $data['tabs'] = $links;
        $data['action'] = '2';
        $data['forumname'] = $data['fname'];
        $data['menulinks'] = xarModAPIFunc('xarbb','admin','getmenulinks');


        //Load Template
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;


    $topics =  xarModAPIFunc('xarbb', 'user', 'getalltopics', array('fid' => $fid));

    if (count($topics) > 0) { //check to make sure there are topics to delete
        // need to delete the topics first then the forum.
        if (!xarModAPIFunc('xarbb', 'admin', 'deletealltopics', array('fid' => $fid))) return;
    }

    $delforum = xarModAPIFunc('xarbb', 'admin', 'delete', array('fid' => $fid));
    if (empty( $delforum))
    {
        $msg = xarML('There was a problem deleting the forum. It was not deleted.');
        xarTplSetMessage($msg,'error');
    } else {
        $msg = xarML('Forum successfully deleted.');
        xarTplSetMessage($msg,'status');
    }
    // Redirect
    xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));

    // Return
    return true;
}

?>
