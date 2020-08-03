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
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with xarbb Menu information
 */
function xarbb_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditxarBB',1,'Forum')) return;

       xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));

    // success
    return true;
}

?>
