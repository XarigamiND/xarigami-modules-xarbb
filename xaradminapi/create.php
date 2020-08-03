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
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @returns int
 * @return autolink ID on success, false on failure
 */
function xarbb_adminapi_create($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return

    $invalid = array();
    if (!isset($fname) || !is_string($fname)) {
        $invalid[] = 'fname';
    }
    if (!isset($fposter) || !is_numeric($fposter)) {
        $invalid[] = 'fposter';
    }
    if (!isset($fdesc) || !is_string($fdesc)) {
        $invalid[] = 'fdesc';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1)', join(', ', $invalid));
         throw new BadParameterException(null,$msg);
    }

    //Let's set defaults so we can pass in values if necessary
    if ((!isset($ftopics)) || (empty($ftopics))){
        $ftopics=0;
    }
    if ((!isset($fposts))|| (empty($fposts))){
        $fposts=0;
    }

    // Security Check
    if(!xarSecurityCheck('AddxarBB',1,'Forum')) return;

    // Default categories is none
    if (empty($cids) || !is_array($cids) ||
        // catch common mistake of using array('') instead of array()
        (count($cids) > 0 && empty($cids[0]))) {
        $cids = array();
        // for security check below
        $args['cids'] = $cids;
    } else {
        $args['cids'] = array_values(preg_grep('/\d+/', $cids));
    }

    // Get datbase setup
    $dbconn =  xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // Get next ID in table
    $nextId = $dbconn->GenId($xbbforumstable);
    $forder = $nextId;

    // Get Time
    if ((!isset($fpostid)) || (empty($fpostid))){
        $fpostid= time();
    }

    // Add item
    $query = "INSERT INTO $xbbforumstable ("
        . " xar_fid, xar_fname, xar_fdesc, xar_ftopics,"
        . " xar_fposts, xar_fposter, xar_fpostid, xar_fstatus)"
        . " VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $result = $dbconn->Execute($query, array($nextId, $fname, $fdesc, $ftopics, $fposts, $fposter, $fpostid, $fstatus));
    if (!$result) return;

    // Get the ID of the item that we inserted
    $fid = $dbconn->PO_Insert_ID($xbbforumstable, 'xar_fid');

    $forder = $fid;

    // Create some module variables for the forum.
    // Users will override these variables to track which forums and topics
    // they have read.
    //
    // Last visited time.
    xarModSetVar('xarbb', 'f_' . $fid, '0');
    // Last read time.
    xarModSetVar('xarbb', 'fr_' . $fid, '0');
    // Topic tracking array.
    $topic_tracking = array();
    xarModSetVar('xarbb', 'topics_' . $fid, serialize($topic_tracking));

    // Now update the forder field .. can't do this in the create ...
    $query = 'UPDATE ' . $xbbforumstable
        . ' SET xar_forder = ?'
        . ' WHERE xar_fid = ?';

    $result = $dbconn->execute($query, array($forder, $fid));
    if (!$result) return;
    $result->close();

    if (empty($cids)) {
        // Set them to the master categories
        $cids = explode(';', xarModGetVar('xarbb', 'mastercids'));
    }

    $settings = array();
    $settings['postsperpage']       = $postsperpage;
    $settings['postsortorder']      = $postsortorder;
    $settings['topicsperpage']      = $topicsperpage;
    $settings['topicsortby']        = $topicsortby;
    $settings['topicsortorder']     = $topicsortorder;
    $settings['hottopic']           = $hottopic;
    $settings['allowhtml']          = $allowhtml;
    $settings['allowbbcode']        = $allowbbcode;
    $settings['allowsmilies']       = $allowsmilies;
    $settings['editstamp']          = $editstamp;
    $settings['showcats']           = $showcats;
    $settings['usenntp']               = $usenntp;

    xarModSetVar('xarbb', 'settings.' . $fid, serialize($settings));
    // Enable bbcode hooks for modified forum
    if (xarModIsAvailable('bbcode')) {
        // Make sure the overall module hook is disabled so we can do each forum
        xarModAPIFunc(
            'modules', 'admin', 'disablehooks',
            array(
                'callerModName'    => 'xarbb',
                'callerItemType'   => 0,
                'hookModName'      => 'bbcode'
            )
        );

        if ($settings['allowbbcode']) {
            xarModAPIFunc(
                'modules', 'admin', 'enablehooks',
                array(
                    'callerModName'    => 'xarbb',
                    'callerItemType'   => $fid,
                    'hookModName'      => 'bbcode'
                )
            );
        } else {
            xarModAPIFunc(
                'modules', 'admin', 'disablehooks',
                array(
                    'callerModName'    => 'xarbb',
                    'callerItemType'   => $fid,
                    'hookModName'      => 'bbcode'
                )
            );
        }
    }
    // Enable smilies hooks for modified forum
    if (xarModIsAvailable('smilies')) {
        // Make sure the overall module hook is disabled so we can do each forum
        xarModAPIFunc(
            'modules', 'admin', 'disablehooks',
            array(
                'callerModName'    => 'xarbb',
                'callerItemType'   => 0,
                'hookModName'      => 'smilies'
            )
        );

        if ($settings['allowsmilies']) {
            xarModAPIFunc(
                'modules', 'admin', 'enablehooks',
                array(
                    'callerModName'    => 'xarbb',
                    'callerItemType'   => $fid,
                    'hookModName'      => 'smilies'
                )
            );
        } else {
            xarModAPIFunc(
                'modules', 'admin', 'disablehooks',
                array(
                    'callerModName'    => 'xarbb',
                    'callerItemType'   => $fid,
                    'hookModName'      => 'smilies'
                )
            );
        }
    }

    // Let any hooks know that we have created a new forum
    $args['module'] = 'xarbb';
    $args['itemtype'] = 0; // forum
    $args['itemid'] =$fid; // forum
    $args['cids'] = $cids;
    xarModCallHooks('item', 'create', $fid, $args);// forum

    // Return the id of the newly created link to the calling process
    return $fid;
}

?>