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
function xarbb_user_viewforumrss()
{
    if (!xarVarFetch('fid', 'id', $fid)) return;

    // The user API function is called.
    $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));

    if (empty($data)) return;

    // Security Check
    // - although 'getforum' already has security checks and will not return
    // forums for which the user does not have access.
    // TODO: need to handle it a bit better than this (until the RSS theme has its own
    // custom method of handling system errors). Just need to ensure the RSS feed is valid.
    if (!xarSecurityCheck('ReadxarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;

    // The user API function is called
    // TODO: hard-coded 20 items for now, but make it configurable.
    $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics', array('fid' => $fid, 'numitems' => 20));

    $totaltopics = count($topics);

    for ($i = 0; $i < $totaltopics; $i++) {
        $topic = $topics[$i];
        $topics[$i]['tpostrss'] = xarVarPrepForDisplay($topic['tpost']);
        $topics[$i]['tpost'] = xarVarPrepHTMLDisplay($topic['tpost']);
    }

    // Add the array of items to the template variables
    $data['fid'] = $fid;
    $data['items'] = $topics;

    xarTplSetPageTitle(xarVarPrepForDisplay($data['fname']));

    // Return the template variables defined in this function
    return $data;
}

?>