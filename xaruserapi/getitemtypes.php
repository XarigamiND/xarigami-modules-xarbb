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
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function xarbb_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');

    foreach($forums as $forum) {
        $itemtypevalue = $forum['fid'];
        $itemtypes[$itemtypevalue] = array(
            'label' => xarML('#(1) Forum', $forum['fname']),
            'title' => xarML('Individual Forum Configuration'),
            'url' => xarModURL('xarbb', 'user', 'viewforum', array('fid' => $forum['fid']))
        );
    }

    return $itemtypes;
}

?>