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
function xarbb_userapi_replynotify($args)
{
    extract($args);
    if (empty($tid)) {
        $msg = xarML('Invalid parameter count');
         throw new BadParameterException(null,$msg);
    }

    // Gots ta gets the topic info
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // Let's see if there are subscribers, else move on, nothing to see.
    if (!empty($data['toptions'])) {
        $topicoptions = unserialize($data['toptions']);
    } else {
        return true;
    }

    if (isset($topicoptions['subscribers'])) {
        $subscribers = $topicoptions['subscribers'];
    } else {
        return true; /* No subscribers */
    }

    // Our subscribers are in the array $topicoption['subscribers']
    // We just need to run through a loop and send an email.
    // Is there any real reason to template this email?
    // I don't see any right now, but lets see the feature request rack up.
    // Close one, and get 15 more...
    $sitename       = xarModGetVar('themes', 'SiteName');
    // Don't URL encode the link if it is to end up in a text e-mail
    $link           = xarModUrl('xarbb', 'user', 'viewtopic', array('tid' => $tid), false);
    $subject        = xarML('Topic Subscription Notice at #(1)', $sitename);
    $message        = xarML('A topic that you subscribed to at #(1) has a reply.', $sitename);
    $message        .= "\n\n";
    $message        .= xarML('Topic Title: #(1)', $data['ttitle']);
    $message        .= "\n\n";
    $message        .= xarML('Topic Link: #(1)', $link);
    $htmlmessage    = xarML('A topic that you subscribed to at #(1) has a reply.', $sitename);
    $htmlmessage    .= '<p><a href="'. xarVarPrepForDisplay($link) .'">'. $data['ttitle'] .'</a>';

    foreach($subscribers as $subscriber){
        // Send Mail
        // Gots ta gets the topic info
        $user = xarModAPIFunc('roles', 'user', 'get', array('uid' => $subscriber));

        if (!xarModAPIFunc('mail', 'admin', 'sendmail',
            array(
                'info'         => $user['email'],
                'name'         => $user['name'],
                'subject'      => $subject,
                'message'      => $message,
                'htmlmessage'  => $htmlmessage)
            )
        ) return;
    }

    return true;
}

?>