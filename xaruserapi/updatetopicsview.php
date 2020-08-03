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
/*
 * Update a topic view
 * @author John Cox
 * @author Jo dalle Nogare
 */
function xarbb_userapi_updatetopicsview($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($tid)) {
        $msg = xarML('Invalid parameter count');
         throw new BadParameterException(null,$msg);
    }

    if (!isset($treplies)) {
        $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

        if (empty($topic)) {
            $msg = xarML('No Such Topic Present - #(1)', $topic);
            throw new IDNotFoundException(null,$msg);
        }

        // Security Check: needed? only called through this module and data inconsistency if fails (wrong number of reply posts,..)
        // if(!xarSecurityCheck('ReadxarBB')) return;

        //---------------------------------------------------------------
        // DO Update Stuff
        $modid = xarModGetIDFromName('xarbb');
        $anonuid = xarConfigGetVar('Site.User.AnonymousUID');

        $treplies = xarModAPIFunc('comments', 'user', 'get_count',
            array('modid' => $modid, 'itemtype' => $topic['fid'], 'objectid' => $tid)
        );

        // get the last comment
        $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
            array(
                'modid'    => $modid,
                'itemtype' => $topic['fid'],
                'objectid' => $tid,
                'startnum' => $treplies,
                'numitems' => 1
            )
        );

        $totalcomments = count($comments);
        $isanon = 0;
        if ($totalcomments > 0) {
            $isanon=$comments[$totalcomments-1]['xar_postanon'];
            $time = $comments[$totalcomments-1]['xar_datetime'];
        }

        if ($isanon == 1) {
            $treplier = $anonuid;
        } elseif ($totalcomments > 0) {
            $treplier = $comments[$totalcomments-1]['xar_uid'];
        }

    }

    $param = array("tid" => $tid, "treplies" => $treplies);

    if (isset($treplier)) {
        $param["treplier"] = $treplier;
        if (isset($time)) {
            $param["time"] = $time;
        } else {
            $param["time"] = time();
        }
    }

    // We also want to call the hooks seperately so that the reply information
    // is supplied rather than the small amout of topic information that is known

    $param['nohooks'] = true;

    // Update the topic: call api func
    if (!xarModAPIFunc('xarbb', 'user', 'updatetopic', $param)) return;

    // Let the calling process know that we have finished successfully
    return true;
}
?>