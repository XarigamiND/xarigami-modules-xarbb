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

function xarbb_user_showerror($args)
{
    extract($args);

    xarVarFetch('errortype', 'str:1', $errortype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('tid', 'id', $tid, 0, XARVAR_NOT_REQUIRED);

    $data = compact('errortype', 'tid');

    return $data;
}

?>