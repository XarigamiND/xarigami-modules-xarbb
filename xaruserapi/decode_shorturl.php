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
 * Extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * Supported URLs:
 *
 * [/<xarbb-alias>]/index
 * [/<xarbb-alias>]/forum/<forum-id>
 * [/<xarbb-alias>]/topic/<topic-id>
 * [/<xarbb-alias>]/category/<category-id>
 *
 * @author the xarBB module development team
 * @param  array $params array containing the different elements of the virtual path
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 * Notes:
 * - TODO: support category and forum names.
 * - Missing IDs or an unrecognised path will result in a redirect to '<xarbb-alias>/index'
 * - Additional path arguments will be ignored.
 * - The IDs are extracted as the left-most digits only (e.g. 3.html => 3).
 */

function xarbb_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();
    $aliasisset = xarModGetVar('xarbb', 'useModuleAlias');
    $aliasname = xarModGetVar('xarbb', 'aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias = true;
    } else{
        $usealias = false;
    }

    $module = 'xarbb';
    if ($params[0] != $module) { //it's possibly some type of alias
        $aliasname = xarModGetVar('xarbb', 'aliasname');
    }

    // Shift the alias out if it is equal to the module name.
    // This allows us to use, say, 'topics' or 'forum' as the module alias.
    if ((strtolower($params[0]) == 'xarbb') || (strtolower($params[0] == $aliasname))) {
        array_shift($params);
    }

    // If no path components then return.
    if (empty($params)) {
        return;
    }

    // The default function if we don't match any others.
    $func = 'main';

    // Decode the ID, if present and CID.
    if (!empty($params[1]) && preg_match('/^(\d+)/', $params[1], $matches)) {
        $id = $matches[1];
    }
    if (!empty($params[3])&& preg_match('/^(\d+)/', $params[3], $matches)) {
        $cid=$matches[1];
    }
    // forum
    if (preg_match('/^forum|^viewforum/i', $params[0]) && !empty($id)) {
       $args['fid'] = $id;
       $func = 'viewforum';
    }
    // newtopic
    if (preg_match('/^newtopic/i', $params[0]) && !empty($id)) {
       if (!empty($params[2]) && preg_match('/^edit/i', $params[2])) {
           $args['redirect']= 'topic'; //edit the topic
           $args['tid'] = $id;
       } elseif (empty($params[2])) {     //else just create a new topic
           $args['fid'] = $id;
       }
       $func = 'newtopic';
    }
   // newreply

    if (preg_match('/^newreply/i', $params[0]) && !empty($id)) {
        if (!empty($params[2]) && preg_match('/^edit/i',$params[2]) && isset($cid)) {
            $args['phase'] = 'edit';
            $args['cid'] = $cid;
        }elseif (!empty($params[2]) && preg_match('/^quote/i',$params[2])&& isset($cid)) {
             $args['phase'] = 'quote';
             $args['cid'] = $cid;
        }elseif (!empty($params[2]) && preg_match('/^quote/i',$params[2]) ) {
             $args['phase'] = 'quote';
        }

            $args['tid'] = $id;
            $func = 'newreply';
    }

    // updatetopic
    if (preg_match('/^updatetopic/i', $params[0]) && !empty($id)) {
       $args['tid'] = $id;
       $func = 'updatetopic';
    }
    // viewtopic
    if (preg_match('/^topic|^viewtopic/i', $params[0]) && !empty($id)) {
       $args['tid'] = $id;
       $func = 'viewtopic';
    }
    // subscribe
    if (preg_match('/^subscribe/i', $params[0]) && !empty($id)) {
       $args['tid'] = $id;
       $func = 'subscribe';
    }
    // unsubscribe
    if (preg_match('/^unsubscribe/i', $params[0]) && !empty($id)) {
       $args['tid'] = $id;
       $func = 'unsubscribe';
    }
    // topic
    if (preg_match('/^printtopic/i', $params[0]) && !empty($id)) {
       $args['tid'] = $id;
       $func = 'printtopic';
    }
    // category
    if (preg_match('/^category/i', $params[0]) && !empty($id)) {
       $args['catid'] = $id;
    }

    return array($func, $args);
}

?>