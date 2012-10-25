
# websocket pubsub

listeners = {}
subscriptions = {}

def proc(msg, sock=None):
    if not msg or not ' ' in msg or msg.find(' ', 1+msg.find(' ')) < 0:
        return
    cmd, uri, data = msg.split(' ', 2)
    cmd = cmd.lower()
    if cmd == 'pub':
        for k in subscriptions.get(uri, {}).keys():
            if k in listeners:
                listeners[k].put_nowait(msg)
            else:
                del subscriptions[uri][k]
    elif cmd == 'sub':
        if not uri in subscriptions:
            subscriptions[uri] = {}
        subscriptions[uri][sock] = True

def http(environ, respond):
    proc(environ['wsgi.input'].read())
    respond('204 OK', [])
    return ''

from wsgevent import Queue, Greenlet

def application(environ, respond):
    sock = environ.get('wsgi.websocket')
    if sock is None:
        return http(environ, respond)

    listeners[sock] = out = Queue()

    def sender():
        for msg in out:
            sock.send(msg)

    send = Greenlet(sender)
    send.start()

    try:
        while True:
            msg = sock.receive()
            if msg is None:
                break
            elif type(msg) is unicode:
                msg = msg.encode('utf-8')
            proc(msg, sock)

    except Exception, e: pass

    send.kill()
    del listeners[sock]

