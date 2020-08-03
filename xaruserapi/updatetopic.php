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
 * create or update a forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @param $args['tid'] topic id to update
 * @returns int
 * @return true on success, NULL on failure
 * @todo support an 'options' array that merges into an existing 'toptions' array.
 */

function xarbb_userapi_updatetopic($args)
{
    // Get arguments from argument array
    extract($args);

    if (!isset($tid)) $invalid[] = "tid";

    // params in arg
    $params = array(
        "fid"       => "xar_fid",
        "ttitle"    => "xar_ttitle",
        "tpost"     => "xar_tpost",
        "tposter"   => "xar_tposter",
        "time"      => "xar_ttime",
        "tposter"   => "xar_tposter",
        "treplies"  => "xar_treplies",
        "treplier"  => "xar_treplier",
        "tftime"    => "xar_tftime",
        "tstatus"   => "xar_tstatus",
        "toptions"  => "xar_toptions"
    );

    foreach($params as $vvar => $dummy) {
        if (isset($args[$vvar])) {
            $set = true;
            break;
        }
    }

    if (!isset($set)) {
        $invalid[] = xarML('At least one of these parameters has to be set: #(1)', join(',', array_keys($fields)));
    }

    // Argument check - make sure that at least on paramter is present
    // if not then set an appropriate error message and return
    if (isset($invalid)) {
        $msg = xarML('Invalid parameter count');
         throw new BadParameterException(null,$msg);
    }

    // For sec check.
    // Note: this now returns an 'options' pre-expanded array; merge $options into this, if supplied.
    // TOOD: work out how to merge in the subscribers sub-array, since there are several ways in which
    // the merge could be intended (perhaps subscribers_replace, subscribers_add and subscribers_remove arrays?)
    $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
    if (empty($topic)) return;

    // Security Check
    // It would have to be ModxarBB, but because posting results in an update, it has to be post permission
    // FIXME: this prevents users without posting rights from subscribing to a topic,
    // but does *not* prevent users with posting rights from changing the status of a topic
    // or from updating a topic that is not theirs.
    // This privilege check needs a finer level of granularity, with different privilege levels
    // allowing updates of different parts of the topic.
    if (!xarSecurityCheck('PostxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;

    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $xbbtopicstable = $xartable['xbbtopics'];

    if ((empty($time)) && (empty($toptions))) {
        $time = time();
    }

    $update = array();
    $bindvars = array();

    // Update item
    $query = "UPDATE $xbbtopicstable SET ";
    if (isset($fid)){
        $update[] = "xar_fid = ? ";
        $bindvars[] = $fid;
    }
    if (isset($ttitle)){
        $update[] = "xar_ttitle = ? ";
        $bindvars[] = $ttitle;
    }
    if (isset($tpost)){
        $update[] = "xar_tpost = ? ";
        $bindvars[] = $tpost;
    }
    if (isset($tposter)){
        $update[] = "xar_tposter = ? ";
        $bindvars[] = $tposter;
    }
    if (isset($time)){
        $update[] = "xar_ttime = ? ";
        $bindvars[] = $time;
    }
    if (isset($treplies)){
        $update[] = "xar_treplies = ? ";
        $bindvars[] = $treplies;
    }
    if (isset($treplier)){
        $update[] = "xar_treplier = ? ";
        $bindvars[] = $treplier;
    }
    if (isset($tftime)){
        $update[] = "xar_tftime = ? ";
        $bindvars[] = $tftime;
    }
    if (isset($tstatus)){
        $update[] = "xar_tstatus = ? ";
        $bindvars[] = $tstatus;
    }
    if (isset($toptions)){
        $update[] = "xar_toptions = ? ";
        $bindvars[] = $toptions;
    }

    $query .= join(",",$update);
    $query .= "WHERE xar_tid = ? ";
    $bindvars[] = $tid;

    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // check if the topic moved to another forum and if there are replies
    if (!empty($fid) && $fid != $topic['fid'] && !empty($topic['treplies'])) {
        // if so, adapt the itemtype for the comments too
        $commentstable = $xartable['comments'];
        $ctable = $xartable['comments_column'];
        $query = "UPDATE $commentstable SET $ctable[itemtype] = ?
                  WHERE $ctable[modid] = ? AND $ctable[itemtype] = ? AND $ctable[objectid] = ?";
        $modid = xarModGetIDFromName('xarbb');
        $bindvars = array((int) $fid, (int) $modid, (int) $topic['fid'], (int) $tid);

        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) return;
    }

    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    if (!isset($nohooks)){
        // Let any hooks know that we have created a new topic
        $args['module'] = 'xarbb';
        $args['itemtype'] = $topic['fid']; // forum item type
        $args['itemid'] = $tid;
        xarModCallHooks('item', 'update', $tid, $args);
    }

    // Return the id of the newly created link to the calling process
    return true;
}

?>