<?php
/**
 * Set a 'cookie' value in the user area, falling back
 * to a session variable if user is not logged on.
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
 * @todo Build this all into xarModSetUserVar()
*/

function xarbb_adminapi_set_cookie($args)
{
    extract($args);

    // We need a name/value pair.
    if (!isset($name) || !is_string($name) || !isset($value) || (!is_string($value) && !is_numeric($value))) return;

    if (xarUserIsLoggedIn()) {
        xarModSetUserVar('xarbb', $name, $value);
    } else {
        // Store it in the session
        xarSessionSetVar('xarbb_' . $name, $value);
    }

    return;
}

?>