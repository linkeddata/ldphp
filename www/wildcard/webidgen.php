<?php
//TODO: replace all die() with proper error messages

/* OpenSSL conf */
// This is the CA password you supplied when you created the CA 
$pass = '12345';
// Path to the openssl.cnf file used by the CA
$conf = $_SERVER["DOCUMENT_ROOT"].'/CA/openssl.cnf';

// Prepare the request
$name = (isset($_POST['name']))?$_POST['name']:'Anonymous';
if (isset($_POST['path'])) {
    $path = $_POST['path'];
    // Exit if we don't have a #
    if (strpos($path, '#') === false) // missing # 
        die("You must at least provide a # fragment. For example: #me or #public.");

    // remove the # fragment so we get the profile document path
    $path_frag = explode('#', $path);
    $profile = $path_frag[0];
    $hash = $path_frag[1];

    if (file_exists($_filename.$profile) === true)
        die('You must pick a different identifier. <strong>'.
            $path.'</strong> already exists in <strong>'.$_base.'</strong>.');
} else {
    die('You need to provide a preferred identifier.');
}
$email = $_POST['email'];
$spkac = str_replace(str_split("\n\r"), '', $_POST['SPKAC']);
//$spkac = $_POST['SPKAC'];
// the full WebID URI
$webid = $_base.$path;

/* --- Certificate --- */
/*
// Remove any whitespace in teh supplied SPKAC and prepare the cert request
$req = "SPKAC=".$spkac;
$req .= "\nCN=".$name;

// Export the subjectAltName to be picked up by the openssl.cnf file
$SAN = "URI:".$webid;
putenv("SAN=$SAN");
// Export the base dir for CA 
putenv("CA_BASE=".$_SERVER["DOCUMENT_ROOT"].'/CA');

// Create temporary files to hold the input and output to the openssl call.
$tmpSPKACfname = "/tmp/SPK" . md5(time().rand());
$tmpCERTfname  = "/tmp/CRT" . md5(time().rand());

// Write the SPKAC and DN into the temporary file
$f = fopen($tmpSPKACfname, "w");
fwrite($f, $req);
fclose($f);

// TODO - This should be more easily configured
$command = "openssl ca -config $conf -verbose -batch -notext -spkac $tmpSPKACfname -out $tmpCERTfname -passin pass:'".$pass."' 2>&1";

// Run the command;
$output = shell_exec($command);
//echo $output;

// TODO - Check for failures on the command
if (preg_match("/Data Base Updated/", $output)==0)
{
    //echo $req."<br/>\n";
	//echo "<pre>";
	//echo $output;
	//echo "</pre>";
	// Remove unneeded files    
    unlink($tmpSPKACfname);
    unlink($tmpCERTfname);
	die('Failed to create X.509 Certificate!');
}
// Delete the temporary SPKAC file
unlink($tmpSPKACfname);

// get the modulus for this certificate
$command = "openssl x509 -inform der -in $tmpCERTfname -modulus -noout";
$output = explode('=', shell_exec($command));
$modulus = preg_replace('/\s+/', '', strtolower($output[1]));

// TODO: make sure the user got the cert somehow
// Everything is done, now send the p12 encoded SSL certificate back to the user
// notice: it is IMPERATIVE that no html data gets transmitted to the user before the header is sent!
$length = filesize($tmpCERTfname);
*/

$cert_cmd = 'python ../../py/pki.py '.
                " -s '$spkac'" .
                " -n '$name'" .
                " -w '$webid'";
/*
$cert = shell_exec($cert_cmd);
$tmpCERTfname  = "/tmp/CRT";
// Write the SPKAC and DN into the temporary file
$f = fopen($tmpCERTfname, "w");
fwrite($f, $cert);
fclose($f);
*/
// Send the certificate back to the user
header('Content-Type: application/x-x509-user-cert');
passthru($cert_cmd);

//readfile($tmpCERTfname);
// delete the temporary CRT file
//unlink($tmpCERTfname);

exit();

/* --- Profile --- */

// Write the new profile to disk
$document = new Graph('', $_filename.$profile, '', $_base.$profile);
if (!$document) { return false; }

// add a PrimaryTopic
$document->append_objects($_base.'/'.$profile,
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
         array(array('type'=>'uri', 'value'=>'http://xmlns.com/foaf/0.1/PersonalProfileDocument')));
$document->append_objects($_base.'/'.$profile,
        'http://xmlns.com/foaf/0.1/primaryTopic',
         array(array('type'=>'uri', 'value'=>$_base.'/'.$path)));
         
// add a foaf:Person
$document->append_objects($_base.'/'.$path,
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',                    
        array(array('type'=>'uri', 'value'=>'http://xmlns.com/foaf/0.1/Person')));
// add name
$document->append_objects($_base.'/'.$path,
        'http://xmlns.com/foaf/0.1/name',
        array(array('type'=>'literal', 'value'=>$name)));
// add mbox if we have one
if (strlen($email) > 0) {
    $document->append_objects($_base.'/'.$path,
            'http://xmlns.com/foaf/0.1/mbox',
            array(array('type'=>'uri', 'value'=>'mailto:'.$email)));
}
// add modulus and exponent as bnode
$document->append_objects($_base.'/'.$path,
        'http://www.w3.org/ns/auth/cert#key',
        array(array('type'=>'bnode', 'value'=>'_:bnode1')));
$document->append_objects('_:bnode1',
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
        array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/cert#RSAPublicKey')));         
$document->append_objects('_:bnode1',
        'http://www.w3.org/ns/auth/cert#modulus',
        array(array('type'=>'literal', 'value'=>$modulus, 'datatype'=>'http://www.w3.org/2001/XMLSchema#hexBinary')));
$document->append_objects('_:bnode1',
        'http://www.w3.org/ns/auth/cert#exponent',
        array(array('type'=>'literal', 'value'=>'65537', 'datatype'=>'http://www.w3.org/2001/XMLSchema#int')));
// Write graph to disk
//exit('Exit');
$document->save();

/* --- WAC (.meta) --- */
$meta = new Graph('', $_aclbase.'/.meta', '', $_base.'.meta');
if (!$meta) { return false; }
//echo "<pre>G=".htmlentities($meta->to_string('turtle'))."</pre>";

// look for an existing default rule and add it if necessary
$default_query = 'PREFIX acl: <http://www.w3.org/ns/auth/acl#>
      SELECT ?s WHERE {?s ?p ?o .
        FILTER (regex(?s, "#Default", "i"))
    }';
$default_res = $meta->SELECT($default_query);
if (isset($default_res['results']['bindings']) && count($default_res['results']['bindings']) == 0) {
    // Add the default entry if we don't have one
    // TODO what do we do with existing files/dirs in the root dir? (add rules for them?)
    $meta->append_objects($_base.'.meta#Default',
        'http://www.w3.org/ns/auth/acl#accessTo',                    
        array(array('type'=>'uri', 'value'=>'http://'.$_domain)));
    $meta->append_objects($_base.'.meta#Default',
        'http://www.w3.org/ns/auth/acl#accessTo',                    
        array(array('type'=>'uri', 'value'=>'https://'.$_domain)));
    $meta->append_objects($_base.'.meta#Default',
        'http://www.w3.org/ns/auth/acl#accessTo',
        array(array('type'=>'uri', 'value'=>$_request_path)));
    $meta->append_objects($_base.'.meta#Default',
        'http://www.w3.org/ns/auth/acl#accessTo', 
        array(array('type'=>'uri', 'value'=>'')));
    $meta->append_objects($_base.'.meta#Default',
        'http://www.w3.org/ns/auth/acl#agentClass',                    
        array(array('type'=>'uri', 'value'=>'http://xmlns.com/foaf/0.1/Agent')));
    $meta->append_objects($_base.'.meta#Default',
        'http://www.w3.org/ns/auth/acl#defaultForNew',                    
        array(array('type'=>'uri', 'value'=>$_request_path)));
    $meta->append_objects($_base.'.meta#Default',
        'http://www.w3.org/ns/auth/acl#mode',          
        array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/acl#Read')));   
}

// Add the Read/Write for user over whole domain if it doesn't exist
$frag_query = 'PREFIX acl: <http://www.w3.org/ns/auth/acl#>
      SELECT ?s WHERE {?s ?p ?o .
        FILTER (regex(?s, "#'.$hash.'", "i"))
    }';
$frag_res = $meta->SELECT($frag_query);
if (isset($frag_res['results']['bindings']) && count($frag_res['results']['bindings']) == 0) {
    $meta->append_objects($_base.'.meta#'.$hash,
        'http://www.w3.org/ns/auth/acl#accessTo',                    
        array(array('type'=>'uri', 'value'=>'http://'.$_domain)));  
    $meta->append_objects($_base.'.meta#'.$hash,
        'http://www.w3.org/ns/auth/acl#accessTo',                    
        array(array('type'=>'uri', 'value'=>'https://'.$_domain)));  
    $meta->append_objects($_base.'.meta#'.$hash,
        'http://www.w3.org/ns/auth/acl#agent',                    
        array(array('type'=>'uri', 'value'=>$webid)));
    $meta->append_objects($_base.'.meta#'.$hash,
        'http://www.w3.org/ns/auth/acl#defaultForNew',                    
        array(array('type'=>'uri', 'value'=>'http://'.$_domain)));
    $meta->append_objects($_base.'.meta#'.$hash,
        'http://www.w3.org/ns/auth/acl#defaultForNew',                    
        array(array('type'=>'uri', 'value'=>'https://'.$_domain)));
    $meta->append_objects($_base.'.meta#'.$hash,
        'http://www.w3.org/ns/auth/acl#mode',    
        array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/acl#Read')));
    $meta->append_objects($_base.'.meta#'.$hash,
        'http://www.w3.org/ns/auth/acl#mode',    
        array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/acl#Write')));
}

// remove the prefixing / from rule name
if (substr($_request_path, 0, 1) == '/')
    $_request_path = substr($_request_path, 1, strlen($_request_path));

// Add the profile rules
$meta->append_objects($_base.'.meta#'.$_request_path,
    'http://www.w3.org/ns/auth/acl#accessTo',                    
    array(array('type'=>'uri', 'value'=>$_request_path)));  
$meta->append_objects($_base.'.meta#'.$_request_path,
    'http://www.w3.org/ns/auth/acl#agent',                    
    array(array('type'=>'uri', 'value'=>$webid)));
$meta->append_objects($_base.'.meta#'.$_request_path,
    'http://www.w3.org/ns/auth/acl#defaultForNew',                    
    array(array('type'=>'uri', 'value'=>$_request_path)));
$meta->append_objects($_base.'.meta#'.$_request_path,
    'http://www.w3.org/ns/auth/acl#mode',    
    array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/acl#Write')));

// Write graph to disk
$meta->save();    


