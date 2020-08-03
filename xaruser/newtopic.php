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
 * @author John Cox
 * @author jojodee
 * add new forum topic
 */

function xarbb_user_newtopic()
{
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('ttitle', 'str:1:120', $ttitle, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tpost', 'str', $tpost, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tstatus', 'int:0:2', $tstatus, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('tid', 'id', $tid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('redirect', 'str', $redirect, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('preview', 'str:1:50', $preview, '', XARVAR_DONT_SET)) return;
    $antibotinvalid =0;//initialize
    if (isset($tid)) {
        // The user API function is called.
        $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
        if (!empty($data)) $forum = xarModAPIfunc('xarbb', 'user', 'getforum', array('fid' => $data['fid']));
    } elseif(isset($fid)) {
        // The user API function is called.
        $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
        $forum = $data;
    } else {
        // Neither fid nor tid supplied.
        $msg = xarML('No topic or forum identified');
        throw new BadParameterException(null,$msg);
    }

    if (empty($data)) return;

    if (isset($fid)) $data['fid'] = $fid;

    $settings = $forum['settings'];
    $allowhtml = isset($settings['allowhtml']) && xarModIsAvailable('html') ? $settings['allowhtml']:false;
    $data['allowhtml'] = $allowhtml;

    $allowbbcode = isset($settings['allowbbcode']) && xarModIsAvailable('bbcode')?$settings['allowbbcode']:false;
    $data['allowbbcode'] = $allowbbcode;

    $allowsmilies = isset($settings['allowsmilies']) && xarModIsAvailable('smilies')?$settings['allowsmilies']:false;
    $data['allowsmilies'] = $allowsmilies;

    if (isset($settings['editstamp'])) {
        $data['editstamp']  = $settings['editstamp'];
    } else {
        $settings['editstamp'] = 1;
        $data['editstamp'] = $settings['editstamp'];
    }

    // Security Check
    if (isset($tid))    {
        $uid = xarUserGetVar('uid');
        if (!xarSecurityCheck('ModxarBB', 0, 'Forum', $data['catid'] . ':' . $data['fid'])) {
            // No privs, but this could be my comment.
            // FIXME: support proper 'Myself' privileges.
            if ($uid != $data['tposter']){
                // Nope?  Lets return
                $message = xarML('You do not have access to modify this topic.');
                return $message;
            }
        }
    } else {
        if (!xarSecurityCheck('PostxarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;
    }

    if (!empty($preview)) $phase = 'form';

    switch(strtolower($phase)) {
        case 'form':
        default:
            if (isset($tid))  {
                // $data is an existing topic we are editting.
                if (!empty($preview)) {
                    if (empty($tpost)){
                        $data['tpost'] = '';
                    } else {
                        $data['tpost'] = $tpost;
                    }

                    if (empty($ttitle)){
                        $data['ttitle'] = '';
                    } else {
                        $data['ttitle'] = $ttitle;
                    }

                    if (empty($tstatus)){
                        $data['tstatus'] = '';
                    } else {
                        $data['tstatus'] = $tstatus;
                    }
                }

                $item = $data;

                $item['module'] = 'xarbb';
                $item['itemtype'] = $data['fid'];
                $item['itemid'] = $tid;

                // Horrible hack so the File Upload property knows when to invoke its uploads hooks.
                if (xarModIsHooked('uploads', 'xarbb', $data['fid'])) {
                    xarVarSetCached('Hooks.uploads','ishooked',1);
                }

                //Call hooks here - but need to null out the ones that will cause trouble
                // CHECKME: should we not remove them, rather than setting to NULL?
                $data['hooks'] = xarModCallHooks('item', 'modify', $tid, $item);
                unset($data['hooks']['categories']);
                unset($data['hooks']['formantibot']); //handled separately with formhooks
            } else  {
                // $data is a new topic we are creating
                if (empty($tpost)){
                    $data['tpost'] = '';
                } else {
                    $data['tpost'] = $tpost;
                }

                if (empty($ttitle)){
                    $data['ttitle'] = '';
                } else {
                    $data['ttitle'] = $ttitle;
                }

                if (empty($tstatus)){
                    $data['tstatus'] = '';
                } else {
                    $data['tstatus'] = $tstatus;
                }

                $item = $data;

                $item['module'] = 'xarbb';
                $item['itemtype'] = $fid;
                $item['itemid'] = '';

                // Horrible hack so the File Upload property knows when to invoke its uploads hooks.
                if (xarModIsHooked('uploads', 'xarbb', $fid)) {
                    xarVarSetCached('Hooks.uploads','ishooked',1);
                }

                // Call hooks here - but need to null out the ones that will cause trouble
                $data['hooks'] = xarModCallHooks('item', 'new', '', $item);
                $data['hooks']['categories']= NULL;
                $data['hooks']['formantibot'] = NULL; //handled separately with formhooks

            }

            $data['authid'] = xarSecGenAuthKey();

            if (empty($warning)){
                $data['warning'] = '';
            } else {
                $data['warning'] = $warning;
            }

            if(empty($redirect)) {
                $data['redirect'] = 'forum';
            } else {
                $data['redirect'] = $redirect;
            }

            //<jojodee> Have to pass the item type now as we have different itemtypes
            //pass specific forum itemtype $fid
            $formhooks = xarModAPIFunc('xarbb', 'user', 'formhooks', array('itemtype' => $data['fid'],'antibotinvalid'=>$antibotinvalid));
            $data['formhooks'] = $formhooks;
            break;

        case 'update':
            if (isset($tid)) {
                $adminid = xarModGetVar('roles', 'admin');
                if (($data['editstamp'] == 1 ) || (($data['editstamp'] == 2 ) && (xarUserGetVar('uid') <> $adminid))) {
                    $tpost2 = xarTplModule('xarbb', 'user', 'modifiedby', array());
                    $tpost = rtrim($tpost) . "\n" . $tpost2;
                }

                if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
                    array(
                        'tid'      => $tid,
                        'fid'      => $data['fid'],
                        'ttitle'   => $ttitle,
                        'tpost'    => $tpost,
                        'tstatus'  => $tstatus)
                    )
                ) return;
            } else {
                // Only update the user if new topic, not edited
                $tposter = xarUserGetVar('uid');
                $args = array('modid'    => xarModGetIDFromName('xarbb'),
                             'itemtype' => $data['fid'],
                             'objectid' => $data['fid'],
                             'ttitle'  => $ttitle,
                             'tpost'    => $tpost
                             );

                $hookinfo = xarModCallHooks('item', 'submit', $data['fid'], $args);
                $antibotinvalid = isset($hookinfo['antibotinvalid']) ? $hookinfo['antibotinvalid'] : 0;
                $data['antibotinvalid'] =  $antibotinvalid ;

                if ($antibotinvalid != TRUE) {
                    $tid = xarModAPIFunc('xarbb', 'user', 'createtopic',
                        array(
                            'fid'      => $data['fid'],
                            'ttitle'   => $ttitle,
                            'tpost'    => $tpost,
                            'tposter'  => $tposter,
                            'tstatus'  => $tstatus
                        )
                    );

                    // Check the auto subscription
                    $autosubscribe_setting = xarModGetUserVar('xarbb', 'autosubscribe');
                    $autosubscribe_default = xarModGetVar('xarbb', 'autosubscribe');
                    if ($autosubscribe_setting == 'topics' || ($autosubscribe_setting == 'default' && $autosubscribe_default == 'topics')) {
                        // Subscribe this user to the topic
                        xarModAPIFunc('xarbb', 'admin', 'subscribe', array('tid'=>$tid));
                    }

                    $settings   = unserialize(xarModGetVar('xarbb', 'settings.' . $fid));

                    // We don't want to update the forum counter on an updated reply.
                    if (!xarModAPIFunc('xarbb', 'user', 'updateforumview',
                        array(
                            'fid'      => $data['fid'],
                            'topics'   => 1,
                            'move'     => 'positive',
                            'replies'  => 1,
                            'fposter'  => $tposter,
                            'tid'      => $tid,
                            'ttitle'   => $ttitle,
                            'treplies' => 0)
                        )
                    ) return;
                } else {
                   //prepare to return to the form
                   $formhooks = xarModAPIFunc('xarbb', 'user', 'formhooks', array('itemtype' => $data['fid'],'antibotinvalid'=>$antibotinvalid));

                   $data['formhooks'] = $formhooks;
                   $data['ttitle'] = $ttitle;
                   $data['tpost'] = $tpost;
                   $data['tposter'] = $tposter;
                   $data['tstatus'] = $tstatus;
                   $data['authid'] = xarSecGenAuthKey();

                    if (empty($warning)){
                        $data['warning'] = '';
                    } else {
                        $data['warning'] = $warning;
                    }

                    if(empty($redirect)) {
                        $data['redirect'] = 'forum';
                    } else {
                        $data['redirect'] = $redirect;
                    }

                }
            }
            if ($antibotinvalid != TRUE) {
                $forumreturn = xarModURL('xarbb', 'user', 'viewforum', array('fid' => $data['fid']));
                $topicreturn = xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid));

                $xarbbtitle = xarModGetVar('xarbb', 'xarbbtitle', 0);
                $xarbbtitle = (isset($xarbbtitle) ? $xarbbtitle : '');

                $markup = xarTplModule('xarbb','user', 'return',
                    array(
                        'forumreturn'     => $forumreturn,
                        'topicreturn'     => $topicreturn,
                        'xarbbtitle'      => $xarbbtitle
                    )
                );
                return $markup;
            }
    } // Switch $phase

    if (!empty($preview)) {
        // Suppress HTML tags if necessary, but only for the transformed text.
        if ($allowhtml == true) {
            $transformedtext = xarVarPrepHTMLDisplay($data['tpost']);
            $transformedtitle = xarVarPrepHTMLDisplay($data['ttitle']);
        } else {
            $transformedtext = xarVarPrepForDisplay($data['tpost']);
            $transformedtitle = xarVarPrepForDisplay($data['ttitle']);
        }

        // Now we have everything, transform for the preview
        list ($data['transformedtext'], $data['transformedtitle']) = xarModCallHooks(
            'item', 'transform', $tid,
            array($transformedtext, $transformedtitle),
            'xarbb', $data['fid']
        );
    }

    // Make sure we return the preview state
    $data['preview'] = $preview;

    if (isset($tid)) {
        xarTplSetPageTitle(xarML('Edit Topic'));
    } else {
        xarTplSetPageTitle(xarML('New Topic'));
    }
    if ($antibotinvalid == TRUE) {
       $markup = xarTplModule('xarbb','user', 'newtopic',$data);

       return $markup;
    } else {
        // Return the output
      return $data;
    }
}

?>