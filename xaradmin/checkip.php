<?php

/**
 * View posters for an IP
 *
 * @copyright (C) 2005 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */

function xarbb_admin_checkip()
{
    if(!xarSecurityCheck('AdminxarBB')) return;

    if(!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('ip', 'str:1:20', $ip)) return;

    $data['items'] = array();
    $data['message'] = xarML('Your topics');
    $topics = xarModAPIFunc('xarbb', 'user', 'get_topic_authors',
        array(
            'ip' => $ip,
            'startnum' => $startnum,
            'numitems' => xarModGetVar('xarbb', 'topicsperpage')
        )
    );

    $replies = xarModAPIFunc('xarbb', 'user', 'get_reply_authors',
        array(
            'ip' => $ip,
            'startnum' => $startnum,
            'numitems' => xarModGetVar('xarbb', 'topicsperpage')
        )
    );

    $data['topics'] = $topics;
    $data['replies'] = $replies;
    $data['ip'] = $ip;

    return $data;
}

?>