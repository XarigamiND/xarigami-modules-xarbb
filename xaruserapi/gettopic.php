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
 * get a specific link
 * @params same as getalltopics()
 * @returns array
 * @return single topic array, or NULL on failure
 */

function xarbb_userapi_gettopic($args)
{
    // Get all matching topics, limited to 2 for speed
    $topics = xarModAPIfunc(
        'xarbb', 'user', 'getalltopics',
        array_merge($args, array('numitems' => 2))
    );

    if (count($topics) > 1) {
        $msg = xarML('Too many topics matched criteria');
        throw new IDNotFoundException(null,$msg);
    }

    if (count($topics) == 0) {
        // Handle any errors, pertaining to missing topics, at the receiving end.
        return;
    }

    $topic = reset($topics);

    return $topic;
}

?>