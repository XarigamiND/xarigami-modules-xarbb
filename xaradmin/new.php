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
 * add new forum
 */
function xarbb_admin_new()
{
    // Security Check
    if (!xarSecurityCheck('AddxarBB', 1, 'Forum')) return;

    // Get parameters
    // TODO: define these defaults in ONE place only.
    // linoj: now also in xaradminapi/new.php
    if (!xarVarFetch('fstatus','int', $data['fstatus'], 0)) return;
    if (!xarVarFetch('phase', 'str:1:', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cids', 'array', $cids, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('new_cids', 'array', $cids, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('postsperpage','int:1:',$postsperpage, 20 ,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('postsortorder', 'str:1:', $postsortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('topicsortby', 'str:1:', $topicsortby, 'time', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('topicsortorder', 'str:1:', $topicsortorder, 'DESC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hottopic','int:1:',$hottopic, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowhtml','checkbox', $allowhtml, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowbbcode','checkbox', $allowbbcode, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowsmilies','checkbox', $allowsmilies, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('editstamp','int:0:2',$editstamp, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showcats','checkbox', $showcats, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nntp', 'str:1:', $nntp, '', XARVAR_NOT_REQUIRED)) return;

    $data['menulinks'] = xarModAPIFunc('xarbb','admin','getmenulinks');
    switch(strtolower($phase)) {

        case 'form':
        default:
            if (!xarVarFetch('fname', 'str:1:', $data['fname'], '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fdesc', 'str:1:', $data['fdesc'], '', XARVAR_NOT_REQUIRED)) return;

            // Default forum settings
            $xarsettings= xarModGetVar('xarbb', 'settings');
            if (!empty($xarsettings)) {
                $settings = unserialize($xarsettings);
            }


            $data['postsperpage']    = !isset($settings['postsperpage']) ? 20 :$settings['postsperpage'];
            $data['postsortorder']   = !isset($settings['postsortorder']) ? 'ASC' :$settings['postsortorder'];
            $data['topicsperpage']   = !isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage'];
            $data['topicsortby']     = !isset($settings['topicsortby']) ? 'time' :$settings['topicsortby'];
            $data['topicsortorder']  = !isset($settings['topicsortorder']) ? 'DESC' :$settings['topicsortorder'];
            $data['hottopic']        = !isset($settings['hottopic']) ? 20 :$settings['hottopic'];
            $data['editstamp']       = 0;
            $data['allowhtml']       = !isset($settings['allowhtml']) ? false :$settings['allowhtml'];
            $data['allowbbcode']     = !isset($settings['allowbbcode']) ? false :$settings['allowbbcode'];
            $data['allowsmilies']     = !isset($settings['allowsmilies']) ? false :$settings['allowsmilies'];
            $data['showcats']        = !isset($settings['showcats']) ? false :$settings['showcats'];
            $data['usenntp']         = !isset($settings['usenntp']) ? false :$settings['usenntp'];

            $item = array();
            $item['module'] = 'xarbb';
            $item['itemtype'] = 0; // forum
            $hooks = xarModCallHooks('item', 'new','', $item); // forum
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('', $hooks);
            } else {
                $data['hooks'] = $hooks;
            }

            $masternntpsetting = xarModGetVar('xarbb', 'masternntpsetting');
            $masternntpsetting = (!isset($masternntpsetting) ? false :$masternntpsetting);

            //jojodee- let's only do this if we allow nntp in the master setting else this is loading each time now
            //even if the nntp settings are not available. Review when nntp is available
            if (xarModIsAvailable('newsgroups') && $masternntpsetting){
                // get the current list of newsgroups
                $data['items'] = xarModAPIFunc('newsgroups', 'user', 'getgroups', array('nocache' => true));
                $grouplist = xarModGetVar('newsgroups', 'grouplist');

                if (!empty($grouplist)) {
                    $selected = unserialize($grouplist);
                    // get list of selected newsgroups
                    $data['selected'] = array_keys($selected);
                    // update description of selected newsgroups
                    foreach ($selected as $group => $info) {
                        if (isset($data['items'][$group]) && isset($info['desc'])) {
                            $data['items'][$group]['desc'] = $info['desc'];
                        }
                    }
                } else {
                    $data['selected'] = '';
                }
            }

            //editstamp options
            $data['editstampoptions'] = array
                               ('0'=>xarML('None'),
                                '1'=>xarML('Yes'),
                                '2'=>xarML('Yes - exclude for Admin')
                                );
            $data['fstatusoptions'] = array('0'=>xarML('Ready'),'1'=>xarML('Locked'));
            $data['topicsortoptions']= array(
                        'tid'=>xarML('Topic'),
                        'time'=>xarML('Last Post')
                        );
            $data['topicorderoptions'] = array('ASC'=>xarML('Ascending'),'DESC'=>xarML('Descending'));

            $data['createlabel'] = xarML('Submit');
            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            if (!xarVarFetch('fname', 'str:1:', $data['fname'])) return;
            if (!xarVarFetch('fdesc', 'str:1:', $data['fdesc'])) return;

            if (!empty($cids) && count($cids) > 0) {
                $data['cids'] = array_values(preg_grep('/\d+/',$cids));
            } else {
                $data['cids'] = array();
            }

            $tposter = xarUserGetVar('uid');

            // API does create plus other setup
            $newfid = xarModApiFunc('xarbb', 'admin', 'create',
                array(
                    'fname'    => $data['fname'],
                    'fdesc'    => $data['fdesc'],
                    'cids'     => $data['cids'],
                    'fposter'  => $tposter,
                    'ftopics'  => 0,
                    'fposts'   => 0,
                    'fstatus'  => $data['fstatus'],
                    'allowbbcode' => $allowbbcode,
                    'allowhtml'     => $allowhtml,
                    'allowsmilies'  => $allowsmilies,
                    'usenntp'       => $nntp,
                    'postsperpage'  => $postsperpage,
                    'postsortorder' => $postsortorder,
                    'topicsperpage' => $topicsperpage,
                    'topicsortorder'  => $topicsortorder,
                    'topicsortby'  => $topicsortby,
                    'hottopic'      => $hottopic,
                    'editstamp'     => $editstamp,
                    'showcats'      => $showcats
                )
            );
            $msg = xarML('Forum successfully created.');
            xarTplSetMessage($msg,'status');
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
            break;
    }

    // Return the output
    return $data;
}

?>