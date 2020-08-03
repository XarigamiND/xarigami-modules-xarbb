<?php
/**
 * Delete forum topics and replies
 *
 * @copyright (C) 2005-2008 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */
/**
 * delete a forum
 * @param $args['fid'] ID of the forum
 * @returns bool
 * @return true on success, false on failure
 */
function xarbb_adminapi_deletealltopics($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count', '', 'admin', 'delete', 'xarbb');
           throw new BadParameterException(null,$msg);
    }

    // The user API function is called.
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $fid));

    if (empty($data)) return;

    // Security Check
    if(!xarSecurityCheck('ModxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

    $topics =  xarModAPIFunc('xarbb','user','getalltopics',array('fid' => $fid));

    if ((!$topics) || (count($topics) ==0)) {
         return;
    }
    //Delete all the reply posts (comments table)
    foreach($topics as $topic)       {
        if(!xarModAPIFunc('xarbb','admin','deleteallreplies',array(
                    'tid' => $topic['tid']
                ))) return;
    }

    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // Delete the topic items themselves
    $query = "DELETE FROM $xbbtopicstable
              WHERE xar_fid = ?";
    $result = $dbconn->Execute($query, array($fid));
    if (!$result) return;

    // Let any hooks know that we have deleted topics
    foreach($topics as $topic)    {
        $args['module'] = 'xarbb';
        $args['itemtype'] = $fid; // topic
        $args['itemid'] = $topic['tid'];
        xarModCallHooks('item', 'delete', $topic['tid'], $args);
    }

    // Let the calling process know that we have finished successfully
    return true;
}
?>