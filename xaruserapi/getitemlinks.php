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
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (1 = forums, 2 = topics)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function xarbb_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();

    // forums
    if (empty($itemtype)) {
        $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');
        if (empty($forums)) {
            return $itemlinks;
        }
        foreach ($forums as $forum) {
            if (!empty($itemids) && !in_array($forum['fid'], $itemids)) continue;

            $itemlinks[$forum['fid']] = array(
                'url'   => xarModURL('xarbb', 'user', 'viewforum', array('fid' => $forum['fid'])),
                'title' => xarML('View Forum'),
                'label' => xarVarPrepForDisplay($forum['fname'])
            );
         }
    } else {
        // topics
        if (empty($itemids)) {
            $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics', array('fid' => $itemtype));
        } else {
            $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics', array('tids' => $itemids));
        }
        if (empty($topics)) {
            return $itemlinks;
        }
        foreach ($topics as $topic) {
            $itemlinks[$topic['tid']] = array(
                'url'   => xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $topic['tid'])),
                'title' => xarML('View Topic'),
                'label' => xarVarPrepForDisplay($topic['ttitle'])
            );
        }
    }

    return $itemlinks;
}

?>