#!/usr/bin/python -u
# SPARQL.py
# SPARQL HTTP POST handler
#
# $Id$

# exception email sink
import exception as _exception
_exception.install('root@localhost')

# assorted util
def strtok(buf, tok):
    next = buf.partition(tok)
    if next[1] == tok:
        return next[0], next[2]
    else:
        return None, buf

_elt_1 = lambda elt: elt[1]

# Stream.IO Response wrapper
class Response(object):
    def __init__(self, file, callback=None):
        self._f, self._cb = file, callback
    def __iter__(self):
        if self._cb:
            yield self._cb+'('+self._f.read()+');'
        else:
            yield self._f.read()

# FCGI server
from flup.server.fcgi import WSGIServer
import os
from subprocess import Popen, PIPE
from swobjects import DefaultGraph
from time import strftime
from urlparse import parse_qs

class Server(WSGIServer):
    def __init__(self):
        WSGIServer.__init__(self, self.application, debug=False)

    def _application(self, environ):
        self.devel = 'HTTP_X_DEVEL' in environ
        abs_path = '/srv/clouds/'+environ['SERVER_NAME']+environ['SCRIPT_URL']
        method = environ['REQUEST_METHOD'].upper()

        # parse Content-Type
        content_type = environ.get('CONTENT_TYPE', '')
        # drop charset (Python will do the Right Thing)
        content_type = content_type.split(';')[0]

        # parse conneg preference list
        accept_lst = []
        for elt in environ.get('HTTP_ACCEPT', '').split(','):
            t, q = strtok(elt, ';')
            if t is None:
                t, q = q, 'q=1.0'
            try:
                q = float(q[2:])
            except Exception, e:
                continue
            accept_lst.append((t, q))
        accept_lst = sorted(accept_lst, key=_elt_1, reverse=True)

        # compute base URI
        try:
            base_uri = environ['SCRIPT_URI']
        except KeyError:
            base_uri = (environ.get('HTTPS') and 'https' or 'http')+'://'+environ['HTTP_HOST']+environ['REQUEST_URI']

        r_status = '200 OK'
        r_headers = {}
        r_content = ''

        if method == 'POST':
            # HTTP read SPARQL query / turtle data
            data = environ['wsgi.input'].read()
            # FS ensure the directory path
            dir_path = os.path.dirname(abs_path)
            if not os.path.exists(dir_path):
                os.makedirs(dir_path)
            graph = DefaultGraph(base_uri)
            if os.path.exists(abs_path):
                graph.append(file(abs_path).read())
            if content_type == 'application/sparql-query':
                # HTTP log the query
                print '[%s] [sparql] [client %s] [uri=%s]' % (strftime('%c'), environ['REMOTE_ADDR'], base_uri), data
                # SPARQL and write the new data
                graph.sparql(data).write(abs_path)
            else:
                r_status = '415 Unsupported Media Type'
            # DEVEL send the DefaultGraph
            if self.devel:
                r_content = [graph.toMediaType()]
        else:
            r_status = '501 Method Not Implemented'
            r_content = ''

        # see application notes below
        return r_status, [(x[0].title(), x[1]) for x in r_headers.items()], r_content

    def application(self, environ, start_response, *argv, **kw):
        r, exception = None, None
        try:
            r = self._application(environ, *argv, **kw)
            start_response(r[0], r[1])
            # FCGI will output-buffer this iterable return type
            #   eg. a string gets done 1 char at a time
            #  rec. a list of strings
            return r[2]
        except Exception, e:
            exception = _exception.handler()
        start_response('500 Python Error', [('Content-Type', 'text/plain')])
        return [exception]

if __name__ == '__main__':
    Server().run()

