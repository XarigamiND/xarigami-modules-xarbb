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
/*
 * @param name string Name of the value to store
 * @param value string Value to store
*/

function xarbb_adminapi_get_cookie($args)
{
    extract($args);

    // We need a name/value pair.
    if (!isset($name) || !is_string($name)) return;

    if (xarUserIsLoggedIn()) {
        // Get from the user variable space
        $value = xarModGetUserVar('xarbb', $name);
    } else {
        // Store it in the session
        $value = xarSessionGetVar('xarbb_' . $name);
    }

    return $value;
}

?>