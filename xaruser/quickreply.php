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
function xarbb_user_quickreply()
{
    if (!xarVarFetch('fid', 'int:1:', $data['fid'])) return;
    if (!xarVarFetch('title', 'str', $data['ttitle'])) return;
    if (!xarVarFetch('text', 'str', $data['text'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tid', 'int:1:', $data['tid'])) return;
    $data['authid']     = xarSecGenAuthkey('comments');
    return $data;
}

?>