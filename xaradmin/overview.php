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
 * Overview displays standard Overview page
 *
 * Only used if you actually supply an overview link in your adminapi menulink function
 * and used to call the template that provides display of the overview
 *
 * @author John Cox <niceguyeddie@xaraya.com>
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @return array xarTplModule with $data containing template data
 * @since 3 Sept 2005
 */
function xarbb_admin_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminxarBB', 0)) return;

    $data=array();

    // if there is a separate overview function return data to it
    // else just call the main function that usually displays the overview
    $data['menulinks'] = xarModAPIFunc('xarbb','admin','getmenulinks');


    return xarTplModule('xarbb', 'admin', 'main', $data, 'main');
}

?>