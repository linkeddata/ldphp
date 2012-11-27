<?php

class TestRequest
{
    private $uri;
    private $method = 'GET';
    private $accept = 'text/turtle';
    private $maxRedirects = 4;
    private $postData;
    private $putFile;
    private $putSize;
    private $title;
    private $results;

    private $cert_path = '/var/www/auth/public/tests/agentWebID.pem';
    private $cert_pass = '1234';
    
    function __construct ($uri=null) {
        if ($uri)
            $this->uri = $uri;
    }

    public function testHTML() {
        $this->connect();
        $result = $this->results;
        $ret = "<p><table>\n";
        $ret .= "<tr><td colspan=\"3\"><strong>".$this->title."</strong></td></tr>\n";
        $ret .= "<tr>\n";
        $ret .= "   <td><pre><strong>Request:</strong></pre>\n";
        $ret .= "   <td width=\"5\"></td>\n";
        $ret .= "   <td><pre><strong>Response:</strong></pre>\n";
        $ret .= "</tr>\n";
        $ret .= "<tr>\n";
//        $ret .= "   <td><pre>".print_r($result['info'], true)."</pre></td>\n";
        $ret .= "   <td width=\"5\"></td>\n";
        $ret .= "   <td><pre>";
        $ret .= "   Content-Type: ".$result['info']['content_type']."<br/>";
        $ret .= "   HTTP code: ".$result['info']['http_code']."<br/>";
        $ret .= "   </pre>";
        $ret .= "   </td>\n";
        $ret .= "</tr>\n";
        $ret .= "<tr>\n";
        $ret .= "<td colspan=\"3\"><pre><strong>Response body:</strong>\n";
        $ret .= "   ".htmlentities($result['content'])."</pre></td>\n";
        $ret .= "</tr>\n";
        $ret .= "</table></p>\n";
        return $ret;
    }
    
    
    /**ldp
     */
    public function connect() {
        // Send the request using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->uri);

        // Configure for POST and PUT
        if ($this->method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postData);
        } else if ($this->method == 'PUT') {
            curl_setopt($ch, CURLOPT_PUT, true);
            curl_setopt($ch, CURLOPT_INFILE, $this->putFile);
            curl_setopt($ch, CURLOPT_INFILESIZE, $this->putSize);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxRedirects);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        // SSL options

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLCERT, $this->cert_path);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->cert_pass);

        // Add additional user specified headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: ".$this->accept, 
                                                "Content-Type: ".$this->accept));

        // grab URL and pass it to the browser
        $content = curl_exec($ch);
        $info = curl_getinfo($ch);
        
        if (curl_errno($ch)) { 
            print "Error: " . curl_errno($ch) . ': '. curl_error($ch); 
        } else { 
            // Close the connection
            curl_close($ch); 
        } 
        $this->results = array("content" => $content, "info" => $info);
    }
        
    function setUri($uri) {
        $this->uri = $uri;
    }
    
    function setMethod($method) {
        $this->method = $method;
    }
    
    function setAccept($accept) {
        $this->accept = $accept;
    }
    
    function setMaxRedirects($max) {
        $this->maxRedirects = $max;
    }

    function setPostData($data) {
        $this->postData = $data;
    }

    // File can be the contents of a file
    function setPutFile($file) {
        $this->putFile = $file;
        $this->putSize = sizeof($file);
    } 
    
    function setTitle($title) {
        $this->title = $title;
    }

    function getResults() {
        return $this->results;
    }
    
    function getContent() {
        return trim($this->results['content']);
    }

    /**
     * Prepare the request headers
     *
     * @ignore
     * @return array
     */
    protected function prepareHeaders()
    {
        $headers = array();

        // Set the connection header
        if (!isset($this->headers['connection'])) {
            $headers[] = "Connection: close";
        }

        // Set the Accept header
        if (isset($this->accept)) {
            $headers[] = "Accept: " . $this->accept;
        }

        // Add all other user defined headers
        foreach ($headers as $header) {
            list($name, $value) = $header;
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $headers[] = "$name: $value";
        }

        return $headers;
    }

}
