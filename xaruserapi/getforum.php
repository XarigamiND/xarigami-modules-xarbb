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
 * get a specific link
 * @poaram $args['fid'] id of forum to get
 * @returns array
 * @return forum info array, or false on failure
 */
function xarbb_userapi_getforum($args)
{
    // Get all matching forums, limited to 2 for speed
    $forums = xarModAPIfunc(
        'xarbb', 'user', 'getallforums',
        array_merge($args, array('numitems' => 2))
    );

/*
// FIXME: forums may be assigned to several categories in the future, so xarbb should accept
          the fact that catid may contain several category ids (someday)
    if (count($forums) > 1) {
        $msg = xarML('Too many forums matched criteria');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException($msg));
        return;
    }
*/

    if (count($forums) == 0) {
        $msg = xarML('Selected forum does not exist');
        throw new IDNotFoundException(null,$msg);
    }

    $forum = reset($forums);

    return $forum;
}

?>
