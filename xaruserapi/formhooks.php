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
 * @param $itemtype - pass itemtype for multiple itemtype transformation
 * @author John Cox, jojodee
 * @return mixed The hooks that are called
 */
function xarbb_userapi_formhooks($args)
{
    extract($args);
    if (!isset($itemtype) || empty($itemtype)) {
       $itemtype = '0';
    }

    $hooks = array();

    // call the right hooks, i.e. not the ones for the comments module :)
    //<jojodee> also add the correct itemtype - we can then call specific form transform

    $hooks['formaction'] =  xarModCallHooks('item', 'formaction', '', $args, 'xarbb', $itemtype);
    $hooks['formdisplay'] = xarModCallHooks('item', 'formdisplay','', $args, 'xarbb', $itemtype);
    $hooks['formnew']     = xarModCallHooks('item', 'new', '', $args, 'xarbb',$itemtype);
    //we do not want all the dynamic data properties here eg uploads- called separately in hooks
    if (isset($hooks['formnew']['dynamicdata'])) {
        unset($hooks['formnew']['dynamicdata']);
    }
    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('', $hooks['formaction']);
    }
    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('', $hooks['formdisplay']);
    }
    if (empty($hooks['formnew'])){
        $hooks['formnew'] = '';
    } elseif (is_array($hooks['formnew'])) {
        $hooks['formnew'] = join('',$hooks['formnew']);
    }

    return $hooks;
}

?>