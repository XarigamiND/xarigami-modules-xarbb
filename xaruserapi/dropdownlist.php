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
 * get an array of forums (id => field) for use in dropdown lists

 * @returns array

 */
function xarbb_userapi_dropdownlist($args)
{
 // Add default arguments
    if (!isset($args['field'])) {
        $args['field'] = 'fname';
    }
    // Get the forums
    $forums = xarModAPIFunc('xarbb','user','getallforums',$args);
    if (!$forums) return;

    // Fill in the dropdown list
    $list = array();
    $list[0] = '';
    $field = $args['field'];
    foreach ($forums as $forum) {
        if (!isset($forum[$field])) continue;
    // TODO: support other formatting options here depending on the field type ?
        $list[$forum['fid']] = xarVarPrepForDisplay($forum[$field]);
    }

    return $list;
}

?>