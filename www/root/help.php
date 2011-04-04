<?php
/* help.php
 * service help page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div id="welcome"><strong>Welcome!</strong> This <a target="_blank" href="http://www.w3.org/DesignIssues/ReadWriteLinkedData.html">Read/Write</a> <a target="_blank" href="http://www.w3.org/DesignIssues/LinkedData.html">Linked Data</a> service is free (and open-source) for educational and personal use.<br />
<strong><?=BASE_DOMAIN?></strong> supports several <a target="_blank" href="http://en.wikipedia.org/wiki/Semantic_Web">Semantic Web</a> best practices such as <a target="_blank" href="http://www.w3.org/TR/sparql11-query/">SPARQL 1.1 Updates</a>, RDF content negotiation, <a target="_blank" href="http://enable-cors.org/">CORS</a>, and <a target="_blank" href="http://www.w3.org/wiki/WebID">WebID</a>.</div>

<div id="http-methods" class="left" style="clear: left; margin: 0.5em; padding: 0.5em; width: 350px;">
<h4>HTTP methods supported:</h4>
<ul>
<li>GET: RDF, SPARQL (?query=), JSON (*/json), JSON-P (?callback=), RSS (*/rss+xml), Atom (*/atom+xml)</li>
<li>POST: RDF, SPARQL 1.1 Update (*/sparql-query)</li>
<li>PUT: RDF</li>
<li>DELETE</li>
</ul>
</div>
<div id="media-types" class="left" style="margin: 0.5em; padding: 0.5em; width: 350px;">
<h4>RDF media types supported:</h4>
<ul>
<li>N3: */rdf+n3, */n3, */turtle</li>
<li>NTriples: */rdf+nt, */nt</li>
<li>RDF/XML: */rdf+xml</li>
<li>RDFa: */html, */xhtml</li>
</ul>
</div>

<p class="clear">Specify the media type of your request data with a <code>Content-Type</code> HTTP header.<br />
Specify your response type preference with an <code>Accept</code> HTTP header.</p>
<p>All endpoints interpret the HTTP request URI as the base URI for RDF operations and the default-graph URI for SPARQL operations.</p>

<div class="left info">
<p>In the identifers used above:</p>
<dl><dt>*/type</dt><dd>refers to a media type, typically specified in an HTTP header</dd>
<dt>?k=v</dt><dd>refers to a query string parameter 'k' with value 'v': passed in URL via GET or application/x-www-form-urlencoded via POST</dd></dl>
<p>Some query string options and response (HTTP Accept) media types are complementary.</p>
</div>

<p class="clear left">Though designed with reliability in mind, <?=BASE_DOMAIN?> should not be used to host critical applications that cannot tolerate downtime.</p>
<?php if (substr($_SERVER['SERVER_ADDR'], 0, 3) == '18.') { ?>
<p class="clear left">All uses of this service must comply with the <a target="_blank" href="http://ist.mit.edu/services/athena/olh/rules#mitnet">MITnet rules of use</a>.</p>
<?php }
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
