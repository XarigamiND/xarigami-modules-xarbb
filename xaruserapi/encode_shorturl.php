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

/*
 * Support for short URLs (user functions)
 *
 * The following two functions encode module parameters into some
 * virtual path that will be added to index.php, and decode a virtual
 * path back to the original module parameters.
 *
 * The result is that people (and search engines) can use URLs like :
 *
 * - http://mysite.com/index.php/xarbb/ (main function)
 * - http://mysite.com/index.php/xarbb/list.html (view function)
 * - http://mysite.com/index.php/xarbb/123.html (display function)
 *
 * in addition to the 'normal' Xaraya URLs that look like :
 *
 * - http://mysite.com/index.php?module=xarbb&func=display&exid=123
 *
 * You can also combine the two, e.g. for less frequently-used parameters :
 *
 * - http://mysite.com/index.php/xarbb/list.html?startnum=21
 *
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the xarBB module development team
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function xarbb_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // Coming from categories etc.
    if (!empty($objectid)) {
        $fid = $objectid;
    }

    // The components of the path.
    $path = array();
    $get = $args;

    // This module
    $module = 'xarbb';

    // Alias for the module, set in the config screen.
    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module, 'aliasname');

    if (!empty($aliasisset) && isset($aliasname)) {
        $module_for_alias = xarModGetAlias($aliasname);

        // If the alias is for this module, then
        // use it instead of the module name.
        if ($module_for_alias == $module) {
            $module = $aliasname;
        }
    }

    // Set the first part of the path, which will always be the
    // module name or alias.
    $path[] = $module;

    if ($func == 'main') {
        unset($get['func']);
        if (isset($catid) && is_numeric($catid)) {
            $path[] = 'category';

            unset($get['catid']);
            $path[] = $catid;
        }
    } elseif ($func == 'viewforum') {
        if (isset($fid) && is_numeric($fid)) {
            unset($get['func']);
            $path[] = 'forum';

            unset($get['fid']);
            $path[] = $fid;
        }
    } elseif ($func == 'viewtopic') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['func']);
            $path[] = 'topic';

            unset($get['tid']);
            $path[] = $tid;
        }
    } elseif ($func == 'newtopic') {
        // check for required parameters
        if (isset($fid) && is_numeric($fid)) {
            unset($get['func']);
            if (isset($phase) && $phase == 'quote' && isset($tid) && is_numeric($tid)){
                $path[] = 'newreply';

                unset($get['tid']);
                $path[] = $tid;

                unset($get['phase']);
                $path[] = $phase;
            } else {
                $path[] = $func;

                unset($get['fid']);
                $path[] = $fid;
            }
        } elseif (isset($tid) && is_numeric($tid)) {
            unset($get['func']);
            $path[] = $func;

            unset($get['tid']);
            $path[] = $tid;

            $path[] = 'edit';
        }
    } elseif ($func == 'newreply') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['func']);
            $path[] = $func;

            unset($get['tid']);
            $path[] = $tid;

            if (isset($phase) && ($phase == 'edit' || $phase == 'quote')) {
                if ((isset($cid) && is_numeric($cid)) || $phase == 'quote') {
                    unset($get['phase']);
                    $path[] = $phase;
                }

                if (isset($cid) && is_numeric($cid)) {
                    unset($get['cid']);
                    $path[] = $cid;
                }
            }
        }
    } elseif ($func == 'updatetopic' || $func == 'subscribe' || $func == 'unsubscribe' || $func == 'printtopic') {
        // check for required parameters
        if (isset($tid) && is_numeric($tid)) {
            unset($get['func']);
            $path[] = $func;

            unset($get['tid']);
            $path[] = $tid;

            if ($func == 'printtopic') {
                // Ensure the 'theme' GET parameter is set.
                $get['theme'] = 'print';
            }
        }
    }

    return array('path' => $path, 'get' => $get);
}

?>