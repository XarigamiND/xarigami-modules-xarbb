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
 * Jump to a forum
 *
 * @param int f ForumID
 * @return bool true if correctly jumped to forum
 */
function xarbb_user_jump()
{
    // Security Check
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;
    if (!xarVarFetch('f', 'isset', $f, NULL, XARVAR_DONT_SET)) return;
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $f)));
    return true;
}
?>