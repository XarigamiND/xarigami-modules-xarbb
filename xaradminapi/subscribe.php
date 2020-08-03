<?php
/**
 * Subscribe to a topic
 *
 * @copyright (C) 2005-2008 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */

function xarbb_adminapi_subscribe($args)
{
    extract($args);

    // Topic ID - mandatory
    if (!isset($tid)) return;
    if (!is_numeric($tid)) return;

    // User ID - default to logged in user
    if (!isset($uid)) {
        if (xarUserIsLoggedIn()) $uid = xarUserGetVar('uid'); else return;
    }
    if (!is_numeric($uid)) return;

    // Get the topic data
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
    // If there are subscribers already, we need to update that array
    // else we start a new array.
    if (!empty($data['toptions'])){
        $topicoptions = unserialize($data['toptions']);
        if (!isset($topicoptions['subscribers'])) {
            $topicoptions['subscribers'] = array();
        } elseif (in_array($uid, $topicoptions['subscribers'])) {
            // We're already subscribed
            xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            return true;
        }
        array_push($topicoptions['subscribers'], $uid);
        $topicoptions['subscribers'][] = $uid;
        $mergedarray = serialize($topicoptions);
    } else {
        $topicoptions['subscribers'] = array($uid);
        $mergedarray = serialize($topicoptions);
    }
    // Then we just need to push the update through.
    if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
        array(
            'tid'      => $tid,
            'fid'      => $data['fid'],
            'ttime'    => $data['ttime'],
            'toptions' => $mergedarray
        )
    )) return;

    return true;
}
?>