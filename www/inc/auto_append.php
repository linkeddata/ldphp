<?php
if (substr(REQUEST_URL, 0, 5) === '/json') {
    if (isset($g_callback)) {
        echo ');';
    }
}
