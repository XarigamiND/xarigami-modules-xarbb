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
 * Redirect to the main function with same args
 */
function xarbb_user_view($args)
{
    // No security check or redirect needed - just call up the main function.
    return xarModFunc('xarbb', 'user', 'main', $args);
}

?>