<?php
/**
 * Create a new forum
 * 
 * @copyright (C) 2005 by the Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami XarBB Module
 * @copyright (C) 2007,2008,2009 2skies.com 
 * @link http://xarigami.com/project/xarigami_xarbb
 * @author John Cox, Mikespub, Jo Dalle Nogare
*/
/**
 * @ View existing forums
*/
function xarbb_admin_view()
{  
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('moveaction', 'str:1:', $moveaction, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cfid', 'int:0:', $cfid, NULL, XARVAR_NOT_REQUIRED)) return;
        
    $data['items'] = array();
    // Security Check
    if(!xarSecurityCheck('EditxarBB',1,'Forum')) return;
    $data['isforums']=true;
    
    $forumsperpage=xarModGetVar('xarbb','forumsperpage');
    
    $data['addforum']=xarModURL('xarbb','admin','new');
    
    // The user API function is called
    $items = xarModAPIFunc('xarbb', 'user', 'getallforums',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('xarbb',
                                                            'forumsperpage')));
    if (empty($items)) {
        $data['isforums']=false;
        return $data;
    }
    
    /*
    $totlinks=count($forums);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $totlinks; $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditxarBB', 0)) {
            $links[$i]['editurl'] = xarModURL('xarbb', 'admin', 'modify',
                                              array('fid' => $link['fid']));
        } else {
            $links[$i]['editurl'] = '';
        }
    }
    */
    //Get individual links for reordering, editing, delete, resync topic, resync posts
    $mastercat= xarModGetVar('xarbb', 'mastercids');
    $totalitems=count($items);
    for ($i = 0; $i < $totalitems; $i++) {
        $item = $items[$i];
        $items[$i]['editurl'] = '';
        $items[$i]['deleteurl'] = '';
        //create edit links 
        //in case category is missing
        $catid = isset($item['catid']) ?$item['catid'] :$mastercat;
        if (xarSecurityCheck('ModxarBB', 0, 'Forum', "{$catid}:{$item['fid']}")) {
             $items[$i]['editurl'] = xarModURL('xarbb', 'admin', 'modify',
                                              array('fid' => $item['fid']));
            //do the reorder links
            if ($i <> 0 ) {
                $items[$i]['upurl'] = xarModURL('xarbb', 'admin', 'view',
                    array('cfid' => $item['fid'],'moveaction' => 'up')
                );
            } elseif ($i ==0) {
                $items[$i]['upurl'] = '';
                $items[$i]['uptitle'] = '';
            } else {
                $items[$i]['upurl'] = '';
            }
            $items[$i]['uptitle'] = xarML('Move Up');      
            if ($i <>$totalitems-1) {
                $items[$i]['downurl'] = xarModURL('xarbb', 'admin', 'view',
                    array('cfid' => $item['fid'], 'moveaction' => 'down')
                );
                $items[$i]['downtitle'] = xarML('Move Down');
            } elseif ($i == $totalitems-1) {
                $items[$i]['downurl'] = '';
                $items[$i]['downtitle'] = '';
            } else {
                $items[$i]['downurl'] = '';
                $items[$i]['downtitle'] = xarML('Move Down');
            }
            $items[$i]['calcorder'] = $i+1; 
            $items[$i]['syncforum'] = xarModURL('xarbb','admin','sync',array('fid'=>$item['fid']));
            $items[$i]['synctopic'] = xarModURL('xarbb','admin','sync',array('fid'=>$item['fid'],'withtopics'=>1));
        } 
        
        if (xarSecurityCheck('DeletexarBB', 0, 'Forum', "{$catid}:{$item['fid']}")) {
            $items[$i]['deleteurl'] = xarModURL('xarbb', 'admin', 'delete',
                                              array('fid' => $item['fid']));
        } 
        
   }    
    if (!empty($cfid) && !empty($moveaction)) {
       $domove=xarModAPIFunc('xarbb', 'admin', 'moveforum', array('cfid' => $cfid, 'moveaction' => $moveaction));

       if (!$domove) {
            return;
       } else{
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
       }
    }    
    // Add the array of items to the template variables
    $data['items'] = $items;
    // For the tabs to never be the active tab.
    $data['fid'] = '';

    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarbb', 'user', 'countforums'),
        xarModURL('xarbb', 'admin', 'reorder', array('startnum' => '%%')),
        xarModGetVar('xarbb', 'itemsperpage')
    );
    $data['menulinks'] = xarModAPIFunc('xarbb','admin','getmenulinks');

    // Return the template variables defined in this function
    return $data;
}
?>