# $Id$
# Joe Presbrey <presbrey@mit.edu>
# email exception hook

import sys
from traceback import format_exc as _format_exc

#def format_exc(exc=None):
#    """Return exc (or sys.exc_info if None), formatted."""
#    if exc is None:
#        exc = sys.exc_info()
#    if exc == (None, None, None):
#        return ""
#    import traceback
#    return "".join(traceback.format_exception(*exc))

def _excepthook(*argv):
    handler(tuple(argv))

from smtplib import SMTP as _SMTP
from threading import currentThread as _currentThread

def handler(exc=None, passthru=True):
    if exc is None:
        exc = sys.exc_info()
    TEXT = _format_exc(exc)
    try:
        _thread = _currentThread()
        TEXT = ("Exception in thread %s:\n" % _thread.name) + TEXT
    except: pass
    CMD = len(sys.argv) > 1 and sys.argv[1].endswith('.py') and sys.argv[1] or sys.argv[0]
    SUBJECT = CMD+': '+str(exc[1]).replace('\r','').replace('\n','')
    HEADERS = 'From: %s\r\nTo: %s\r\nSubject: %s\r\n\r\n' % (EMAIL_FROM, EMAIL_TO, SUBJECT)
    _SMTP('localhost').sendmail(EMAIL_FROM, [EMAIL_TO], HEADERS+TEXT)
    if passthru:
        sys.__excepthook__(*exc)
    return TEXT

_Thread_run_ = None
def _Thread_run(self):
    try:
        _Thread_run_(self)
    except Exception, e:
        handler(None)

def install(to):
    import os
    sys.excepthook = _excepthook
    global _Thread_run_, EMAIL_FROM, EMAIL_TO
    from threading import Thread
    _Thread_run_, Thread.run = Thread.run, _Thread_run
    try:
        EMAIL_FROM = os.getlogin()
    except:
        _f = os.path.abspath(__file__)
        if _f.startswith('/home/'):
            EMAIL_FROM = _f.split('/')[2]
        elif _f.startswith('/srv/'):
            EMAIL_FROM = 'root'
        else:
            EMAIL_FROM = 'nobody'
        del _f
    try:
        EMAIL_FROM += '@'+os.getenv('HOSTNAME')
    except:
        import socket
        EMAIL_FROM += '@'+socket.gethostname()
    EMAIL_TO = to

