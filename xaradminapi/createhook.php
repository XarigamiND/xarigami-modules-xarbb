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
 * create an entry for a module item - hook for ('item','create','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarbb_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
         // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
          // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
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
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    // Do we need to process further?
    if (!xarVarFetch('xarbb_forum', 'id', $fid, NULL, XARVAR_DONT_SET)) return;

     if (empty($fid) && isset($extrainfo['xarbb_forum'])) {
        $fid = $extrainfo['xarbb_forum'];
    }
    if (empty($fid)) {
        // no forum
        return $extrainfo;
    }

    if (isset($extrainfo['summary'])) {
        $tpost = $extrainfo['summary'];
    } else {
        if (!xarVarFetch('summary', 'str', $tpost, '', XARVAR_NOT_REQUIRED)) return;
    }
    if (empty($tpost)) {
        // No summary, no forum post.
        return $extrainfo;
    }

    if (isset($extrainfo['title'])) {
        $ttitle = $extrainfo['title'];
    } else {
        if (!xarVarFetch('ttitle', 'str', $ttitle, '', XARVAR_NOT_REQUIRED)) return;
    }
    if (empty($ttitle)) {
        // No title, no forum post.
        return $extrainfo;
    }

    $itemlinks = xarModAPIFunc($modname,'user','getitemlinks',
                               array('itemtype' => $itemtype,
                                     'itemids' => array($itemid)),
                               0);

    if (isset($itemlinks[$itemid]) && !empty($itemlinks[$itemid]['url'])) {
        // normally we should have url, title and label here
        foreach ($itemlinks[$itemid] as $field => $value) {
            $item[$field] = $value;
        }
    } else {
        $item['url'] = xarModURL($modname,'user','display',
                                 array('itemtype' => $itemtype,
                                       'itemid' => $itemid));
    }
    $settings = xarModGetVar('xarbb', 'settings.' . $fid);
    $settings = unserialize($settings);

    $showsourcelink = !isset($settings['showsourcelink']) ? false : $settings['showsourcelink'];
    $createtopiclink = !isset($settings['showitemlink']) ? false : $settings['showitemlink'];

    $allowhtml = !isset($settings['allowhtml']) ? false:$settings['allowhtml'];
    $allowbbcode = !isset($settings['allowbbcode']) ? false:$settings['allowbbcode'];
    $tpostnew = $tpost;
    if ($showsourcelink) {
        $tpostfull = $tpost;
        if ($allowhtml) {
            $tpostfull .= "<br />";
            $tpostfull .= xarML('Source');
            $tpostfull .= ': <a href="'.$item['url'].'" title="'.$ttitle.'">'.$ttitle.'</a>';
        } elseif ($allowbbcode) {
            $tpostfull .= "\n\n";
            $tpostfull .= xarML('Source');
            $tpostfull .= ': [url='.$item['url'].']'.$ttitle.'[/url]';
        } else {
            $tpostfull = $tpost;
        }
        $tpostnew = $tpostfull;
    }
    $tposter = xarUserGetVar('uid');
    $tstatus = 0;
    $tid = xarModAPIFunc('xarbb',
                         'user',
                         'createtopic',
                         array('fid'      => $fid,
                               'ttitle'   => $ttitle,
                               'tpost'    => $tpostnew,
                               'tposter'  => $tposter,
                               'tstatus'  => $tstatus));
    if (empty($tid)) {
        return $extrainfo;
    }

    if ($createtopiclink) {
        $usebbcode = xarModIsHooked('bbcode',$modname,$itemtype) ? true : false;
        //create link in article that was created
        $topiclink = xarModURL('xarbb','user','viewtopic',array('tid'=>$tid));
        $tpostarticle = $tpost;
        if ($usebbcode) {
                $tpostarticle .= "\n\n";
                $tpostarticle .= xarML('Discuss');
                $tpostarticle .= ': [url='.$topiclink.']'.$ttitle.'[/url]';
        } else {
                $tpostarticle .= "<br />";
                $tpostarticle .= xarML('Discuss');
                $tpostarticle .= ': <a href="'.$topiclink.'" title="'.$ttitle.'">'.$ttitle.'</a>';
        }

        $article = array('aid'=>(int)$itemid,'ptid'=>(int)$itemtype,'title'=>$ttitle,'summary'=>$tpostarticle);

        //update the article
        xarModAPIFunc('articles', 'admin', 'update', $article);
    }

    if (!xarModAPIFunc('xarbb',
                       'user',
                       'updateforumview',
                       array('fid'      => $fid,
                             'tid'      => $tid,
                             'ttitle'   => $ttitle,
                             'treplies' => 0,
                             'topics'   => 1,
                             'move'     => 'positive',
                             'replies'  => 1,
                             'fposter'  => $tposter))) {
        return $extrainfo;
    }

    return $extrainfo;
}
?>
