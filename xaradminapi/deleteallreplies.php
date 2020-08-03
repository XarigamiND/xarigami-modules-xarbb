<?php

/**
 * Delete topic replies for a given topic
 * @copyright (C) 2005-2008 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */

/**
 * delete replies
 * @param $args['tid'] Topic id
 * @returns bool
 * @return true on success, false on failure
 */

function xarbb_adminapi_deleteallreplies($args)
{
    extract($args);

    // Argument check
    if (!isset($tid))  {
        $msg = xarML('Invalid Parameter Count');
         throw new BadParameterException(null,$msg);
    }

    // Get topic id
    $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    if (!$topic) {
        $msg = xarML('Could not get topic');
         throw new BadParameterException(null,$msg);
    }

    if (!xarSecurityCheck('ModxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;

    xarModAPIFunc('comments', 'admin', 'delete_object_nodes',
        array(
            'modid' => xarModGetIdFromName('xarbb'),
            'itemtype' => $topic['fid'],
            'objectid' => $tid
        )
    );

    return true;
}

?>