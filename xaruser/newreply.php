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
 *
 * @param int tid Topic ID
 * @param int cid Category ID
 * @param string phase Either 'quote' or 'edit'
 * @return array
 */
function xarbb_user_newreply()
{
    if (!xarVarFetch('tid', 'id', $tid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cid', 'id', $cid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:quote:edit', $phase, '', XARVAR_NOT_REQUIRED)) return;

    //jojo - require the topic ID at all times
    $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
    if (empty($topic)) return;

    // TODO: check the locked status, which is independent of the topic status.
    // TODO: display a nicer error message, rather than a system error.
    // FIXME: having this check here implies no error is raised if a user replies to a reply of a locked thread...?
    if ($topic['tstatus'] == 3) {
        $msg = xarML('Topic -- #(1) -- has been locked by administrator', $topic['ttitle']);
        throw new BadParameterException(null,$msg);
    }

    // Fetch the comment if we are replying to one..
    if (!empty($cid)) {
        $comment = xarModAPIFunc('comments', 'user', 'get_one', array('cid' => $cid));
        // Just want the first element from the array-of-one.
        $comment = reset($comment);
    }

    $forum = xarModAPIfunc('xarbb', 'user', 'getforum', array('fid' => $topic['fid']));
    $settings = $forum['settings'];

    $settings = $forum['settings'];
    $allowhtml = isset($settings['allowhtml']) && xarModIsAvailable('html') ? $settings['allowhtml']:false;
    $allowbbcode = isset($settings['allowbbcode']) && xarModIsAvailable('bbcode')?$settings['allowbbcode']:false;
    $allowsmilies = isset($settings['allowsmilies']) && xarModIsAvailable('smilies')?$settings['allowsmilies']:false;


    if (empty($cid)) {
        $package['title'] = $topic['ttitle'];

        if (($phase == 'quote') && ($allowbbcode == true)) {
            // CHECKME: if the original topic poster is anonymous, ensure we don't expose their identity.
            $package['text'] = '[quote=' . xarUsergetVar('name', $topic['tposter']) . ']'
                . $topic['tpost'] . '[/quote]';
        } elseif (($phase == 'quote') && ($allowhtml == true)) {
            $package['text'] = '<blockquote>' . $topic['tpost'] . '</blockquote>';
        } elseif ($phase == 'edit') {
            $package['text'] = $topic['tpost'];
        }
    } elseif (!empty($cid)) {
        $package['title'] = $comment['xar_title'];

        // CHECKME: this assignment makes replies to an anonymous reply, also anonymouse. Is this desired?
        $package['postanon'] = $comment['xar_postanon'];

        if (($phase == 'quote') && ($allowbbcode == true)){
            $package['text'] = '[quote=' . (!empty($comment['xar_postanon']) ? xarML('Anonymous') : $comment['xar_author']) . ']'
                . $comment['xar_text'] . '[/quote]';
        } elseif (($phase == 'quote') && ($allowhtml == true)){
            $package['text'] = '<blockquote>' . $comment['xar_text'] . '</blockquote>';
        } elseif ($phase == 'edit') {
            $package['text'] = $comment['xar_text'];
        }
    }

    // Security Check
    if ($phase == 'edit'){
        // FIXME: can we make this assumption?
        if (!xarUserIsLoggedIn()) {
            $msg = xarML('You do not have access to modify this topic.');
            return xarTplModule('authsystem','user','errors',array('errortype' => 'locked_out', 'var1' => $lockouttime));
        }

        $uid = xarUserGetVar('uid');
        if (!xarSecurityCheck('ModxarBB', 0, 'Forum', $topic['catid'] . ':' . $topic['fid'])) {
            // No Privs, Hows about this is my comment?
            // FIXME: we have not checked whether this is a comment! Is 'edit' phase really supported?
            if ($uid != $comment['xar_uid']) {
                // Nope?  Lets return
                // TODO: needs to be templated.
                $message = xarML('You do not have access to modify this reply');
               return xarResponseForbidden($message);
            }
        }
    } else {
        if (!xarSecurityCheck('PostxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;
    }

    // Initialise the data array.
    $data = $topic;
    $data['allowhtml'] = $allowhtml;
    $data['allowbbcode'] = $allowbbcode;
    $data['allowsmilies'] = $allowsmilies;
    // Some configuration variables for the comments module.
    $header['input-title']  = xarML('Post a Reply');
    $header['modid']        = xarModGetIDFromName('xarbb');
    $header['objectid']     = $tid;
    $header['itemtype']     = $data['fid'];
    $header['cid']          = $cid;

    // TODO: can we return the user back closer to where they came from - the comment they just added perhaps?
    if ($phase == 'edit') {
        $action = 'modify';
        $receipt['returnurl']['decoded'] = xarModURL('xarbb', 'user', 'updatetopic', array('tid' => $tid, 'modify' => 1));
    } else {
        $action = 'reply';
        $receipt['returnurl']['decoded'] = xarModURL('xarbb', 'user', 'updatetopic', array('tid' => $tid));
    }

    $receipt['post_url']    = xarModURL('comments', 'user', $action, array('tid' => $tid));
    $receipt['action']      = $action;

    $package['name']        = xarUserGetVar('name');
    $package['uid']         = xarUserGetVar('uid');

    // Add images
    // FIXME: images to go in templates.
    $data['profile']    = '<img src="' . xarTplGetImage('infoicon.gif') . '" alt="' . xarML('Profile') . '" />';

    // Form Hooks
    $itemtype = $data['fid'];
    $antibotinvalid = isset($hookinfo['antibotinvalid']) ? $hookinfo['antibotinvalid'] : 0;
    $formhooks = xarModAPIFunc('xarbb', 'user', 'formhooks', array('modname'=>'xarbb','itemtype' => $itemtype));

    $item['itemid'] ='';
    $item['itemtype'] = 0;
    $item['modname'] = 'comments';
    $item['modid'] = xarModGetIDFromName('comments');
    // Call hooks here - but need to null out the ones that will cause trouble
    $hookmodules= xarModCallHooks('item', 'new', '', $item);
    $data['hooks']['categories']= NULL;
    $data['hooks']['formantibot'] = NULL; //handled separately with formhooks
    $data['hooks']      = $formhooks;
    $data['hookmodules'] = $hookmodules;
    $data['receipt']    = $receipt;
    $data['package']    = $package;
    $data['header']     = $header;
    $data['authid']     = xarSecGenAuthkey('comments');

    xarTplSetPageTitle(xarML('Reply to #(1)', $data['ttitle']));

    $xarbbtitle         = xarModGetVar('xarbb', 'xarbbtitle', 0);
    $data['xarbbtitle'] = (isset($xarbbtitle) ? $xarbbtitle : '');

    return $data;
}

?>