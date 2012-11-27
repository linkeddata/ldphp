<?php

// RDF libs
require 'client.php';

$uri = (isset($_REQUEST['uri'])) ? $_REQUEST['uri'] : "";
$form = '<form name="test" method="GET">';
$form .= 'LDP URI: ';
$form .= '<input type="text" name="uri" value="'.$uri.'">';
$form .= '<input type="submit" name="submit" value="Test!">';
$form .= '';
$form .= '</form>';
echo $form;

// Let the tests begin!
if (isset($_REQUEST['uri'])) {

    $container = $_REQUEST['uri'];
    // ugly hack to avoid extra / at the end
    $slash = (substr($_REQUEST['uri'], -1) == '/') ? '':'/';
//    $container .= $slash.'testcontainer';

/* ------------- CONTAINERS --------------- */
    
/* --- POST container --- */
    $postData = "@prefix dcterms: <http://purl.org/dc/terms/>.\n".
                "@prefix ldp: <http://www.w3.org/ns/ldp#>.\n".
                "<>\n".
                "   a ldp:Container ;\n".
                "   dcterms:title \"A new empty container\" .";

    echo "<strong>POST data:</strong>";
    echo "<pre>".htmlentities($postData)."</pre>";

    $test = new TestRequest();
    $test->setUri($container);
    $test->setMethod('POST');
    $test->setPostData($postData);
    $test->setAccept('text/turtle');
    $test->setTitle('POST container on '.$container);
    $ret = $test->testHTML();
    $newURI = $test->getContent();
    
    echo $ret;

/* --- POST resource --- */
    $postData = "@prefix dcterms: <http://purl.org/dc/terms/>.\n".
                "@prefix ldp: <http://www.w3.org/ns/ldp#>.\n".
                "<>\n".
                "   a ldp:Resource ;\n".
                "   dcterms:title \"A new empty resource\" .";

    echo "<strong>POST data:</strong>";
    echo "<pre>".htmlentities($postData)."</pre>";

    $test = new TestRequest();
    $test->setUri($container.'/res1');
    $test->setMethod('POST');
    $test->setPostData($postData);
    $test->setAccept('text/turtle');
    $test->setTitle('POST resource on '.$container);
    $ret = $test->testHTML();
    $newURI = $test->getContent();

    echo $ret;

/* --- DELETE container --- */
    // Add logic to remove the test container

/* ------------- Resources --------------- */
    
}


