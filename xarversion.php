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
 * @author - John Cox
 */

$modversion['name']         = 'xarbb';
$modversion['id']           = '300';
$modversion['version']      = '1.3.2';
$modversion['displayname']  = 'xarBB';
$modversion['description']  = 'A lightweight BB for Xarigami';
$modversion['credits']      = 'xardocs/credits.txt';
$modversion['help']         = 'xardocs/help.txt';
$modversion['changelog']    = 'xardocs/changelog.txt';
$modversion['license']      = 'xardocs/license.txt';
$modversion['coding']       = 'xardocs/coding.txt';
$modversion['official']     = 1;
$modversion['author']       = 'John Cox, Jo dalle nogare';
$modversion['contact']      = 'http://xarigami.com';
$modversion['homepage']      = 'http://xarigami.com/project/xarbb';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
//$modversion['dependency']   = array(147, 177, 14);
$modversion['dependencyinfo']   = array(
                                    0 => array(
                                            'name' => 'core',
                                            'version_ge' => '1.4.0'
                                         ),
                                    147 => array(
                                            'name' => 'categories',
                                            'version_ge' => '2.4.0'
                                        ),
                                    177 => array(
                                            'name' => 'hitcout',
                                            'version_ge' => '1.2.0'
                                        ),
                                    14 => array(
                                            'name' => 'comments',
                                            'version_ge' => '1.5.0'
                                        )
                                );
if (false) { //Load and translate once
    xarML('XarBB');
    xarML('A lightweight BB for Xarigami');
}
?>
