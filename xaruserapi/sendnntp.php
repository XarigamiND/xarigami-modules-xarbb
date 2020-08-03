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
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @param $args['tid'] topic id to update
 * @returns int
 * @return autolink ID on success, false on failure
 */
function xarbb_userapi_sendnntp($args)
{
    // Get arguments from argument array
    extract($args);

    include_once 'modules/xarbb/xarclass/NNTP.php';

    //$tpost      = wordwrap($tpost, 72, "\n", 1);
    $email      = xarUserGetVar('email');
    $name       = xarUserGetVar('name');
    $from       = $email .'('. $name .')';

    $forum = xarModAPIfunc('xarbb', 'user', 'getforum', array('fid' => $fid));
    $settings   = $forum['settings'];

    $server     = $settings['nntpserver'];
    $port       = $settings['nntpport'];
    $group      = $settings['nntpgroup'];

    // We should allow adding a header in the nntp module
    // $addheader = "Content-Transfer-Encoding: quoted-printable\r\n".
    //             "Content-Type: text/plain; charset=ISO-8859-1;\r\n".
    //             "Mime-Version: 1.0\r\n".
    //             'X-HTTP-Posting-Host: '.gethostbyaddr(getenv("REMOTE_ADDR"))."\r\n";

    if (empty($reference)){
        $reference = '';
    }

    if (!xarModAPIfunc('nntp', 'user', 'postarticle',
        array(
            'server'     => $server,
            'port'       => $port,
            'newsgroups' => $group,
            'ref'        => $reference,
            'body'       => $tpost,
            'subject'    => $ttitle,
            'from'       => $from)
        )
    ) return;

    return true;
}

?>