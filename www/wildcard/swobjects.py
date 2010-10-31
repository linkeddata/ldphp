#!/usr/bin/python -u
# SWObjects Wrapper
# Joe Presbrey <presbrey@mit.edu>
#
# $Id$

import SWObjects as _SWObjects

class DefaultGraph(object):
    """provides a single, default graph for all operations"""
    def __init__(self, base_uri=''):
        self._base_uri = base_uri
        self._factory = _SWObjects.AtomFactory()
        self._database = _SWObjects.RdfDB()

    def append(self, data, base_uri=None, media_type='text/turtle'):
        if base_uri is None: base_uri = self._base_uri
        _T = _SWObjects.TurtleSDriver(base_uri, self._factory)
        _T.setGraph(self._database.ensureGraph(_SWObjects.cvar.DefaultGraph))
        _T.parse(_SWObjects.IStreamContext(data, _SWObjects.StreamContextIstream.STRING))
        return self

    def sparql(self, query, base_uri=None):
        if base_uri is None: base_uri = self._base_uri
        _S = _SWObjects.SPARQLfedDriver(base_uri, self._factory)
        _S.parse(_SWObjects.IStreamContext(query, _SWObjects.StreamContextIstream.STRING))
        _S.root.execute(self._database, _SWObjects.ResultSet(self._factory))
        return self

    def toMediaType(self, media_type=None):
        if media_type is None: media_type = 'text/turtle'
        return self._database.toString(_SWObjects.MediaType(media_type))
    def __str__(self): return self.toMediaType()

    def write(self, path, media_type=None):
        file(path, 'w').write(self.toMediaType(media_type))
        return self

def test():
    import logging
    graph = DefaultGraph('http://test/')
    for turtle in ('<a> <b> <c>.',
                   '<d> <e> <f>.'):
        print '# APPEND: '+turtle.replace('\n','')
        print graph.append(turtle)
    for sparql in ('INSERT { <g> <h> <i> }',):
        print '# SPARQL: '+sparql.replace('\n','')
        print graph.sparql(sparql)

def main(*argv, **kw):
    acl = """
@prefix acl: <http://www.w3.org/ns/auth/acl#> .

[]
    a acl:Authorization;
    acl:accessTo <.> ;
    acl:defaultForNew <.> ;
    acl:agent <http://presbrey.mit.edu/foaf#presbrey> ;
    acl:agentClass <http://xmlns.com/foaf/0.1/Agent> ;
    acl:mode acl:Control, acl:Read, acl:Write ."""
    dg = DefaultGraph(*argv)
    dg.append(acl)
    print dg.toMediaType()

    F = _SWObjects.AtomFactory()
    base_uri = ''
    query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX acl: <http://www.w3.org/ns/auth/acl#> SELECT ?rule WHERE { ?rule rdf:type acl:Authorization ; acl:accessTo <https://tabulator.org/wiki/> ; acl:mode acl:Read ; acl:agent <http://presbrey.mit.edu/foaf#presbrey> . }"
    sparser = _SWObjects.SPARQLfedDriver(base_uri, F)
    sparser.parse(_SWObjects.IStreamContext(query, _SWObjects.StreamContextIstream.STRING))
    s = _SWObjects.SPARQLSerializer()
    sparser.root.express(s)
    print 'SPARQL:', s.str()

if __name__ == '__main__':
    test()
    import sys
    print '# MAIN'
    main(*sys.argv[1:])

