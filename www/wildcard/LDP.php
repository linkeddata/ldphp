<?php
/* LDP.php
 * handle LDP management
 *
 * $Id$
 */
 
class LDP {
    private $_g;        // graph with request data
    private $_base;     // the root URI for the LDPR/LDPC
    private $_filename; // local path
    private $_type;     // LDPC or LDPR
    private $_input;
    private $_data;
 
    public function __construct ($filename, $base, $input, $data) {
        if ($filename)
            $this->_filename = $filename;
        else
            return false;
        
        if ($base)
            $this->_base = $base;
        else
            return false; // replace later with something more useful
        
        if (!$input)
            $input = 'turtle';

        if (!$data)
            return false;

        // debug
        //echo "\nFile=".$filename."\nBase=".$base."\nInput=".$input."\nData=".$data;
    
        // Check to see if we have an LDP element in the request    
        $g = new \RDF\Graph('', $this->_filename, '', $this->_base);  
        $g->append($input, $data);
        
        $q = "SELECT ?o WHERE {?s ?p ?o}";
        $r = $g->SELECT($q);

        $ok = true;
        if ((isset($r['results']['bindings'])) && (count($r['results']['bindings']) > 0)) {
            foreach ($r['results']['bindings'] as $val) {
                if ($val['o']['value'] == 'http://www.w3.org/ns/ldp#Container') {
                    $ok = $this->addContainer($input, $data);
                } else if ($val['o']['value'] == 'http://www.w3.org/ns/ldp#Resource') {
                    $ok = $this->addResource();
                }
            }
        }
        return $ok;
    }
    
    // create the requested container (dir)
    function addContainer($input, $data) {
        if (!file_exists($this->_filename))
            mkdir($this->_filename, 0777, true);
        
        $filename = (substr($this->_filename, -1) != '/')?$this->_filename.'/':$this->_filename;
        $base = (substr($this->_base, -1) != '/')?$this->_base.'/':$this->_base;
        
        // add the .meta file with LDP data
        $c = new \RDF\Graph('', $filename.'.meta', '', $base.'.meta');
        if (!$c) { return false; }

        // add container data to the .meta file
        $c->append($input, $data, $base);
        $c->save();
        
        //TODO: add a memberOf relation to the parent dir
        
        return true;
    }
    
    // add the new resource (also add a relation to the parent container)
    function addResource() {
        // load the container .meta information
        $r = new \RDF\Graph('', dirname($this->_filename).'/.meta', '', dirname($this->_base).'/.meta');
        if (!$r) { return false; }
        
        $r->load(dirname($this->_base).'/.meta');
        // add member relation to the parent container
        $r->append_objects(dirname($this->_base).'/', 
                            'http://www.w3.org/2000/01/rdf-schema#member', 
                            array(array('type'=>'uri', 'value'=>$this->_base)));
        $r->save();
        
        // TODO: make sure to recursively update the .meta files in parent dirs!

        return true;
    }

}
