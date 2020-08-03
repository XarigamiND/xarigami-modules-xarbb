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
 * modify an entry for a module item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return string hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarbb_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1)', 'extrainfo');
         throw new BadParameterException(null,$msg);
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1)', 'object ID');
        throw new BadParameterException(null,$msg);
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1)', 'module name');
         throw new BadParameterException(null,$msg);
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $itemid = 0;
    }

    $data['items'] = xarModAPIFunc('xarbb', 'user', 'getallforums');

    if (isset($extrainfo['xarbb_forum'])) {
        $xarbb_forum = $extrainfo['xarbb_forum'];
    } else {
        if (!xarVarFetch('xarbb_forum', 'id', $xarbb_forum, NULL, XARVAR_DONT_SET)) return;
    }

    if (empty($xarbb_forum)) {
        $xarbb_forum = '';
    }

    $default=$xarbb_forum;

    return xarTplModule('xarbb','admin','newhook',
        array(
            'xarbb_forum' => $xarbb_forum,
            'default' => $xarbb_forum,
            'items' =>$data['items']
        )
    );
}

?>