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

function xarbb_user_subscribe()
{
    // No anons please
    if (!xarUserIsLoggedIn()) return;

    // And you better have rights.
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    // All we need is who and where.
    if (!xarVarFetch('tid', 'id', $tid)) return;

    // Do not allow specifying the uid via URL parameters !
    $uid = (int)xarUserGetVar('uid');

    if (!xarModAPIFunc('xarbb', 'admin', 'subscribe', array('tid'=>$tid, 'uid'=>$uid))) return;

    // And then go back to the topic.
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));

    return true;
}

?>