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
function xarbb_user_unsubscribe()
{
    // No anons please
    if (!xarUserIsLoggedIn()) return;

    // And you better have rights.
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    // All we need is who and where.
    if (!xarVarFetch('tid', 'id', $tid)) return;

    // Do not allow specifying the uid via URL parameters !
    // FIXME: Why not? Can the administrator not be allowed to subscribe
    // or unsubscribe other users?
    $uid = (int)xarUserGetVar('uid');

    // Get the topic data
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // If there are subscribers already, we need to update that array
    if (!empty($data['toptions'])){
        $topicoptions = unserialize($data['toptions']);
        if (empty($topicoptions['subscribers']) ||
            !in_array($uid, $topicoptions['subscribers'])) {

            // We're already unsubscribed
            xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            return true;
        }

        // Remove $uid from the list
        $flipped = array_flip($topicoptions['subscribers']);
        unset($flipped[$uid]);
        $topicoptions['subscribers'] = array_keys($flipped);
        $mergedarray = serialize($topicoptions);
    } else {
        // We're already unsubscribed
        xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
        return true;
    }
    // Then we just need to push the update through.
    if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
        array(
            'tid'      => $tid,
            'fid'      => $data['fid'],
            'ttime'    => $data['ttime'],
            'toptions' => $mergedarray
        )
    )) return;

    // And then go back to the topic.
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));

    return true;
}

?>