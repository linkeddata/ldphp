<?php
/* help.php
 * service help page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<p>This <a href="http://www.w3.org/DesignIssues/ReadWriteLinkedData.html">read-write</a> <a href="http://www.w3.org/DesignIssues/LinkedData.html">Linked Data</a> service accepts <a href="http://www.w3.org/TR/sparql11-query/">SPARQL 1.1 Updates</a>. When issuing queries, the SPARQL default-graph is specified by the HTTP document or request URI.</p>
<p>In the identifers used below:</p>
<dl><dt>*/type</dt><dd>refers to a media type 'type' specified in an HTTP header</dd>
<dt>?k=v</dt><dd>refers to a query string parameter 'k' with value 'v': passed in URL via GET or application/x-www-form-urlencoded via POST</dd></dl>
<p>Some query string options and response (HTTP Accept) media types are complementary.</p>
<h3>HTTP methods supported:</h3>
<ul>
<li>GET: RDF, SPARQL (?query=), JSON (*/json), JSON-P (?callback=), RSS (*/rss+xml), Atom (*/atom+xml)</li>
<li>POST: RDF, SPARQL 1.1 Update (*/sparql-query)</li>
<li>PUT: RDF</li>
<li>DELETE</li>
</ul>
<p>Specify desired request media types with a method-supported HTTP Content-Type and response types with a method-supported HTTP Accept header.</p>
<h3>RDF media types supported:</h3>
<ul>
<li>N3: */rdf+n3, */n3, */turtle</li>
<li>NTriples: */rdf+nt, */nt</li>
<li>RDF/XML: */rdf+xml</li>
<li>RDFa: */html, */xhtml</li>
</ul>
<?php
/*
echo '<h3>RDF cloud metadata:</h3><pre>';
print_r($_domain_data);
echo '</pre>';
*/

TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
