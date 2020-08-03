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
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 * @todo fid should not have to be mandatory, also what about IP, authors, etc?
 * @todo Move this count to the admin API, since it does a raw count with no privilege checks
 * @note Counting topics against a range of criteria, with privilege checks, is done in user api getalltopics()
 */

function xarbb_userapi_counttopics($args)
{
    extract($args);

    if (!isset($fid)) {
        $msg = xarML('Invalid parameter count');
        throw new BadParameterException(null,$msg);
    }

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $xbbtopicstable = $xartable['xbbtopics'];
    $query = "SELECT COUNT(1) FROM $xbbtopicstable WHERE xar_fid = ?";
    $result = $dbconn->Execute($query, array((int)$fid));

    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
}

?>