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
function xarbb_user_preferences($args)
{
    // No anonymouses
    if (!xarUserIsLoggedIn()) return;

    // Must be able to view forums
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    extract($args);

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    // Initilise array to store all the user prefs
    $data = array();

    // Array of default values set in the module config
    // Has matching keys to the elements in $data
    $data['default'] = array();

    switch(strtolower($phase)) {
        case 'modify':
        default:
            // Auto Subscribe
            // Fetch the default value
            $default_autosubscribe = xarModGetVar('xarbb', 'autosubscribe');
            if (!empty($default_autosubscribe)) {
                $data['default']['autosubscribe'] = $default_autosubscribe;
            } else {
                $data['errors'][] = xarML('Module setting: autosubscribe undefined');
                return $data;
            }
            // Fetch the user's autosubscribe setting
            $data['autosubscribe'] = xarModGetUserVar('xarbb', 'autosubscribe');

        break;

        case 'update':
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // Set the user's preference to autosubscribe
            if (!xarVarFetch('autosubscribe', 'enum:default:none:topics:replies', $autosubscribe, 'default', XARVAR_NOT_REQUIRED)) return;
            xarModSetUserVar('xarbb', 'autosubscribe', $autosubscribe);

            // Refresh the page
            xarResponseRedirect(xarModURL('xarbb', 'user', 'preferences'));
            return true;
        break;
    }

    return $data;
}

?>