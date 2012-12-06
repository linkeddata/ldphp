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
    
    if (file_exists($_filename.'/'.$profile) === true)
        die('You must pick a different identifier. <strong>'.
            $path.'</strong> already exists in the current directory!');
} else {
    die('You need to provide a preferred identifier.');
}
$email = $_POST['email'];
$spkac = $_POST['SPKAC'];

// the full WebID URI
$webid = $_base.$path;

/* --- Certificate --- */

// Remove any whitespace in teh supplied SPKAC and prepare the cert request
$req = "SPKAC=".str_replace(str_split(" \t\n\r\0\x0B"), '', $spkac);
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
header('Last-Modified: ' . date('r+b'));
header('Accept-Ranges: bytes');
header('Content-Length: ' . $length);
header('Content-Type: application/x-x509-user-cert');
readfile($tmpCERTfname);

// delete the temporary CRT file
unlink($tmpCERTfname);
// clear certificate history from CA

/* --- Profile --- */

// Write the new profile to disk
$profile = new Graph('', $_filename.'/'.$profile, '', $_base.'/'.$profile);
if (!$profile) { return false; }

// add a PrimaryTopic
$profile->append_objects($_base.'/'.$profile,
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
         array(array('type'=>'uri', 'value'=>'http://xmlns.com/foaf/0.1/PersonalProfileDocument')));
$profile->append_objects($_base.'/'.$profile,
        'http://xmlns.com/foaf/0.1/primaryTopic',
         array(array('type'=>'uri', 'value'=>$_base.'/'.$path)));
         
// add a foaf:Person
$profile->append_objects($_base.'/'.$path,
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',                    
        array(array('type'=>'uri', 'value'=>'http://xmlns.com/foaf/0.1/Person')));
// add name
$profile->append_objects($_base.'/'.$path,
        'http://xmlns.com/foaf/0.1/name',
        array(array('type'=>'literal', 'value'=>$name)));
// add mbox if we have one
if (strlen($email) > 0) {
    $profile->append_objects($_base.'/'.$path,
            'http://xmlns.com/foaf/0.1/mbox',
            array(array('type'=>'uri', 'value'=>'mailto:'.$email)));
}
// add modulus and exponent as bnode
$profile->append_objects($_base.'/'.$path,
        'http://www.w3.org/ns/auth/cert#key',
        array(array('type'=>'bnode', 'value'=>'_:bnode1')));
$profile->append_objects('_:bnode1',
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
        array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/cert#RSAPublicKey')));         
$profile->append_objects('_:bnode1',
        'http://www.w3.org/ns/auth/cert#modulus',
        array(array('type'=>'literal', 'value'=>$modulus, 'datatype'=>'http://www.w3.org/2001/XMLSchema#hexBinary')));
$profile->append_objects('_:bnode1',
        'http://www.w3.org/ns/auth/cert#exponent',
        array(array('type'=>'literal', 'value'=>'65537', 'datatype'=>'http://www.w3.org/2001/XMLSchema#int')));
// Write graph to disk
$profile->save();

/* --- WAC (.meta) --- */

$meta = new Graph('', $_aclbase.'/.meta', '', $_base.'.meta');
if (!$meta) { return false; }

// Read the .meta file if we already had one
$meta->load($_base.'/.meta');
/*
<#Default>
    <http://www.w3.org/ns/auth/acl#accessTo> <http://presbrey.data.fm>, <>, <Public> ;
    <http://www.w3.org/ns/auth/acl#agentClass> <http://xmlns.com/foaf/0.1/Agent> ;
    <http://www.w3.org/ns/auth/acl#defaultForNew> <Public> ;
    <http://www.w3.org/ns/auth/acl#mode> <http://www.w3.org/ns/auth/acl#Read> .
*/    
if (substr($_base, -1, 1) == '/')
    $_base = substr($_base, 0, -1);
// Add the default entry
$meta->append_objects('#Default',
    'http://www.w3.org/ns/auth/acl#accessTo',                    
    array(array('type'=>'uri', 'value'=>$_base)));
$meta->append_objects('#Default',
    'http://www.w3.org/ns/auth/acl#accessTo', 
    array(array('type'=>'uri', 'value'=>$profile)));
$meta->append_objects('#Default',
    'http://www.w3.org/ns/auth/acl#accessTo', 
    array(array('type'=>'uri', 'value'=>'')));
    
$meta->append_objects('#Default',
    'http://www.w3.org/ns/auth/acl#agentClass',                    
    array(array('type'=>'uri', 'value'=>'http://xmlns.com/foaf/0.1/Agent')));

$meta->append_objects('#Default',
    'http://www.w3.org/ns/auth/acl#defaultForNew',                    
    array(array('type'=>'uri', 'value'=>$profile)));

$meta->append_objects('#Default',
    'http://www.w3.org/ns/auth/acl#mode',                    
    array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/acl#Read')));

// Add the profile rules
$meta->append_objects('#'.$profile,
    'http://www.w3.org/ns/auth/acl#accessTo',                    
    array(array('type'=>'uri', 'value'=>$profile)));
    
$meta->append_objects('#'.$profile,
    'http://www.w3.org/ns/auth/acl#agent',                    
    array(array('type'=>'uri', 'value'=>$webid)));

$meta->append_objects('#'.$profile,
    'http://www.w3.org/ns/auth/acl#mode',    
    array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/acl#Write')));

//echo $meta->to_string('turtle');
// Write graph to disk
$meta->save();    


