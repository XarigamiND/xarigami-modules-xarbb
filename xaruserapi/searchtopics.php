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
 * Searches all active comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 * @deprec 2006-05-01 - now support moved to getalltopics() - please use that instead
 */

function xarbb_userapi_searchtopics($args)
{
    if (empty($args)) return;

    extract($args);

    // Look at the title and text paramaters, for legacy support.
    if (!empty($title) || !empty($text) || !empty($q)) {
        if (empty($q)) {
            $q = (!empty($title) ? $title : $text);
        }

        // Determine which columns to search on.
        $columns = array();
        if (!empty($title)) $columns[] = 'xar_ttitle';
        if (!empty($text)) $columns[] = 'xar_tpost';

        $args['q'] = $q;
        $args['columns'] = $columns;
    }

    if (!empty($author) && is_numeric($author)) {
        $args['uid'] = $author;
    }

    $topics = xarModAPIfunc('xarbb', 'user', 'getalltopics', $args);

    return $topics;
}

?>