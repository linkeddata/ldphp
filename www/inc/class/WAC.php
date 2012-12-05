<?php

/**
 * Web Access Control class
 * http://www.w3.org/wiki/WebAccessControl
 */
class WAC {
    private $_req_user;
    private $_base_meta;
    private $_base_uri;
    private $_graph;
    private $_options;

    private $_reason;

    /**
     * Constructor for WAC
     * @param string $req_user  the authenticated user 
     * @param string $base_meta the .meta file we're reading from
     * @param string $base_uri  the base URI of the HTTP request
     * @param array $options    local configuration options
     *
     * @return boolean (always true for now)
     */
    function __construct($req_user, $base_meta, $base_uri, $options) {
        // methods: Read/Write/Control
        $this->_req_user = $req_user;
        $this->_base_meta = $base_meta;
        $this->_base_uri = $base_uri;
        $this->_options = $options;
        
        $this->_graph = new Graph('', $base_meta, '', REQUEST_BASE.'/.meta');
        if ($options->linkmeta || $this->_graph->exists())
            header('Link: <'.$options->base_url.'/.meta>; rel=meta');

        return true;
    }

    function getReason() {
        return $this->_reason;
    }

    /**
     * Check if the user has access to a specific URI
     * @param string $method Read/Write/Append/Control
     * @param array $options local configuration options
     * @param string $uri the URI of the resource
     *
     * @return boolean (true if user has access)
     */
    function can($method, $uri=null) {
        // there is no .meta file present
        if ($this->_options->open && !$this->_graph->size()) {
            $this->_reason .= 'No .meta file found. in '.REQUEST_BASE;
            return true;
        }
        $this->_reason .= 'BaseURI='.$this->_base_uri.'';
        $uri = is_null($uri) ? $this->_base_uri : $uri;
        // strip trailing slash
        if (substr($uri, -1, 1) == '/')
            $uri = substr($uri, 0, -1);
        $p = $uri;
        // walk path
        while (true) {
            if (!strpos($p, '/')) break;
            $verb = ($p == $uri) ? 'accessTo' : 'defaultForNew';
            // specific authorization
            $q = "PREFIX acl: <http://www.w3.org/ns/auth/acl#>
                  SELECT * WHERE { 
                    ?z acl:agent <".$this->_req_user.">; 
                    acl:mode acl:$method; 
                    acl:$verb <$p> . 
                    }";
            $r = $this->_graph->SELECT($q);
            if (isset($r['results']['bindings']) && count($r['results']['bindings']) > 0) {
                $this->_reason .= 'User '.$this->_req_user.' is allowed '.$method.' access to '.$p;
                return true;
            }
            // public authorization
            $q = "PREFIX acl: <http://www.w3.org/ns/auth/acl#>
                  SELECT * WHERE { 
                    ?z acl:agentClass <http://xmlns.com/foaf/0.1/Agent>; 
                    acl:mode acl:$method; 
                    acl:$verb <$p> . 
                    }";
            $r = $this->_graph->SELECT($q);
            if (isset($r['results']['bindings']) && count($r['results']['bindings']) > 0) {
                $this->_reason .= 'Everyone is allowed '.$method.' access to '.$p;
                return true;
            }
            $p = dirname($p);
        }
        $this->_reason .= 'User '.$this->_req_user.' is NOT allowed '.$method.' access to '.$p;
        return false;
    }
    /**
     * Give a user a specific WAC rule
     * @param string $method Read/Write/Append/Control
     * @param string $user  a specific user or anyone (*)
     * @param string $uri the URI of the resource
     *
     * @return boolean (true if everything is ok)
     */
    function give($method, $user, $uri=null) {
        // check if we are allowed to write to .meta      
        if ($this->can('Write', $this->_options, $uri) == false)
            return false;
        
        $uri = is_null($uri) ? $this->_base_uri : $uri;
        // strip trailing slash
        if (substr($uri, -1, 1) == '/')
            $uri = substr($uri, 0, -1);
        
        // load existing .meta contents
        $this->_graph->load($this->_base_uri.'/.meta');

        // TODO: do we need recursivity here as well?
        // proceed to setting the WAC rule
        // add the resource first
        $this->_graph->append_objects($this->_base_uri,
            'http://www.w3.org/ns/auth/acl#accessTo',
            array(array('type'=>'uri', 'value'=>$uri)));
        // check if we have a specific user or any agent (*)?
        if ($user == '*') {            
            $this->_graph->append_objects($this->_base_uri,
                'http://www.w3.org/ns/auth/acl#agentClass',
                array(array('type'=>'uri', 'value'=>'http://xmlns.com/foaf/0.1/Agent')));
        } else {
            $this->_graph->append_objects($this->_base_uri,
                'http://www.w3.org/ns/auth/acl#agent',
                array(array('type'=>'uri', 'value'=>$user)));
        }
        
        // add access method
        $this->_graph->append_objects($this->_base_uri,
            'http://www.w3.org/ns/auth/acl#mode',
            array(array('type'=>'uri', 'value'=>'http://www.w3.org/ns/auth/acl#'.$method)));
        
        // save the new .meta
        $this->_graph->save();
        
        return true;
    }

}
