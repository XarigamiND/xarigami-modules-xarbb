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
function xarbb_userapi_getmenulinks()
{
    $menulinks = array();

    $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');

    // Get the category assignments for these forums
    $fidlist = array();
    if (count($forums)>0) {
        foreach ($forums as $forum) {
            $fidlist[] = $forum['fid'];
        }
        $cids = xarModAPIFunc('categories', 'user', 'getlinks',
            array('iids' => $fidlist, 'reverse' => 1, 'modid' => xarModGetIDFromName('xarbb'))
        );

        foreach ($forums as $forum) {
            $pass = true;
            if (isset($cids[$forum['fid']]) && count($cids[$forum['fid']]) > 0) {
                // Note: if forums are assigned to more than 1 category (= future), then we need read access to all here
                foreach ($cids[$forum['fid']] as $cid) {
                    if (!xarSecurityCheck('ReadxarBB', 0, 'Forum', $cid . ':' . $forum['fid'])) {
                        $pass = false;
                        break;
                    }
                }
            } elseif (!xarSecurityCheck('ReadxarBB', 0, 'Forum', 'All:' . $forum['fid'])) {
                $pass = false;
            }

            if($pass) {
                $menulinks[] = array(
                    'url'   => xarModURL('xarbb', 'user', 'viewforum', array('fid' => $forum['fid'])),
                    'title' => $forum['fname'],
                    'label' => $forum['fname']
                );
            }
        }
    }
    // User preferences
    // User must be logged in and able to view forums
    if (xarUserIsLoggedIn() && xarSecurityCheck('ViewxarBB', 1, 'Forum')) {
        $menulinks[] = array(
            'url' => xarModURL('xarbb', 'user', 'preferences'),
            'title' => 'User Preferences',
            'label' => 'User Preferences',
        );
    }

    return $menulinks;
}

?>