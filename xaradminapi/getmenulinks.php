<?php
/**
 * Utility function to pass individual menu items to the main menu
 *
 * @copyright (C) 2005-2008 by the Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xarigami xarbb
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_xarbb
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xarbb_adminapi_getmenulinks()
{

    if (xarSecurityCheck('ModxarBB', 0, 'Forum')) {
        $menulinks[] = array(
            'url'   => xarModURL('xarbb', 'admin', 'view'),
            'title' => xarML('View and Edit Forums'),
            'label' => xarML('View forums'),
             'active' =>array('view')
        );
    }
   if (xarSecurityCheck('AddxarBB', 0, 'Forum')) {
        $menulinks[] = array(
            'url'   => xarModURL('xarbb', 'admin', 'new'),
            'title' => xarML('Add a New forum'),
            'label' => xarML('Add forum'),
            'active' =>array('new')
        );
    }

    if (xarSecurityCheck('AdminxarBB', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('xarbb', 'admin', 'modifyconfig'),
            'title' => xarML('Modify the configuration for the XarBB'),
            'label' => xarML('Modify Config'),
             'active' =>array('modifyconfig')
        );
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>