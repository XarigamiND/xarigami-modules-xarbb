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
 * Update a topic with a new reply
 *
 * @param int tid topic id
 * @param int modify
 * @return array
 */

function xarbb_user_updatetopic()
{
    // We need to update the statistics about the forum and the topics here.
    // We do this by updating both tables at once and then giving the poster a chance to reply to the
    // topic or go back to the forum of which he came.

    if (!xarVarFetch('tid', 'id', $tid)) return;
    if (!xarVarFetch('modify', 'int:0:1', $modify, 0, XARVAR_NOT_REQUIRED)) return;

    // Need to handle locked topics
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // TODO: the locked status is now a topic option, not a main status
    if ($data['tstatus'] == 3) {
        $msg = xarML('Topic -- #(1) -- has been locked by administrator', $data['ttitle']);
        return xarResponseForbidden($msg);
    }

    // get the number of comments
    // Need to move this outside the modify condition so we can return to the topic
    // Bug 3517
    // Start by updating the topic stats.
    $modid = xarModGetIDFromName('xarbb');
    $count = xarModAPIFunc('comments', 'user', 'get_count',
        array(
            'modid'       => $modid,
            'itemtype'    => $data['fid'],
            'objectid'    => $tid
        )
    );
    // get the last comment
    $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
            array(
                'modid'       => $modid,
                'itemtype'    => $data['fid'],
                'objectid'    => $tid,
                'startnum' => $count,
                'numitems' => 1
            )
        );
    $lastpost = current($comments);
    // Don't count up if the topic is being edited? Need to add modify test here.
    if ($modify != 1) {

        $totalcomments = count($comments);
        $isanon= 0;
        if (($totalcomments)>0) {
            $isanon = $comments[$totalcomments-1]['xar_postanon'];
        }
        $anonuid = xarConfigGetVar('Site.User.AnonymousUID');

        if ($isanon == 1) {
            $poster = $anonuid;
        } else {
            $poster = xarUserGetVar('uid');
        }

        if (!xarModAPIFunc('xarbb', 'user', 'updatetopicsview',
            array('tid' => $tid, 'treplies' => $count, 'treplier' => $poster)
        )) return;

        if (!xarModAPIFunc('xarbb', 'user', 'updateforumview',
            array(
                'fid'      => $data['fid'],
                'tid'      => $tid,
                'ttitle'   => $data['ttitle'],
                'treplies' => $count,
                'replies'  => 1,
                'move'     => 'positive',
                'fposter'  => $poster
            )
        )) return;

        // Check the auto subscription
        $autosubscribe_setting = xarModGetUserVar('xarbb', 'autosubscribe');
        $autosubscribe_default = xarModGetVar('xarbb', 'autosubscribe');
        if ($autosubscribe_setting == 'replies' || ($autosubscribe_setting == 'default' && $autosubscribe_default == 'replies')) {
            // Subscribe this user to the topic
            xarModAPIFunc('xarbb', 'admin', 'subscribe', array('tid'=>$tid));
        }

        // While we are here, let's send any subscribers notifications.
        // TODO: provide an option to queue the notificiations, because if there are lot of
        // subscribers, we don't want to delay the posting of a reply while the e-mails are sent.
        if (!xarModAPIFunc('xarbb', 'user', 'replynotify', array('tid' => $tid))) return;
    }

    $forumreturn = xarModURL('xarbb', 'user', 'viewforum', array('fid' => $data['fid']));
    $forumdata= xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $data['fid']));
    $settings = $forumdata['settings'];

    $pageinfo= xarTplPagerInfo($lastpost['xar_cid'], $count,$settings['postsperpage']);

    if ($settings['postsortorder'] == 'ASC') {
        $startnum = $pageinfo['lastpage'];
    } else {
         $startnum = $pageinfo['firstpage'];
    }
    $args =   array('tid'       =>$tid,
                    'startnum'  =>$startnum
                    );

    $replyreturn= xarModURL('xarbb','user','viewtopic',$args,NULL,$lastpost['xar_cid']);

    $topicreturn = xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid));
    $xarbbtitle = xarModGetVar('xarbb', 'xarbbtitle', 0);
    $xarbbtitle = (isset($xarbbtitle) ? $xarbbtitle : '');

    $data = xarTplModule('xarbb', 'user', 'return',
        array(
            'forumreturn'   => $forumreturn,
            'topicreturn'   => $topicreturn,
            'replyreturn'   => $replyreturn,
            'xarbbtitle'    => $xarbbtitle
        )
    );

    return $data;
}

?>