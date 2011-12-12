<html id="docHTML">
<head>
<link rel="stylesheet" href="http://dig.csail.mit.edu/hg/tabulator/raw-file/tip/content/tabbedtab.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="http://www.w3.org/2011/datastuff/init/mashlib.js"></script>
<script>
jQuery(document).ready(function() {
    var uri = window.location.href
    window.document.title = uri;
    var kb = tabulator.kb;
    var subject = kb.sym(uri);
    tabulator.outline.GotoSubject(subject, true, undefined, true, undefined);
});
</script>
</head>
<body>
<div class="TabulatorOutline" id="DummyUUID">
    <table id="outline"></table>
</div>
</body>
</html>
