<?php
include_once('header.php');
?>
<p>Specify RDF graph by HTTP Document URI; 404s are prepopulated with an empty RDF container.</p>
<p>Specify request media types with a method-supported HTTP Content-Type header.</p>
<p>Specify desired response types with a method-supported HTTP Accept header.</p>
<hr />
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
<h3>RDF media types supported:</h3>
<ul>
<li>N3: */rdf+n3, */n3, */turtle</li>
<li>NTriples: */rdf+nt, */nt</li>
<li>RDF/XML: */rdf+xml</li>
<li>RDFa: */html, */xhtml</li>
</ul>
<?php
echo '<h3>RDF cloud metadata:</h3><pre>';
print_r($d);
echo '</pre>';
include_once('footer.php');
?>
