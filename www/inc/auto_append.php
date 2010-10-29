<?php
/* auto_append.php
 * application-wide enclosure
 *
 * $Id$
 */
if (substr(REQUEST_URL, 0, 5) === '/json') {
    if (isset($g_callback)) {
        echo ');';
    }
}
TAG(__FILE__, __LINE__, '$Id$');
