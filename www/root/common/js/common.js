/* $Id$ */

HTTP = Class.create(Ajax.Request, {
  request: function(url) {
    this.url = url;
    this.method = this.options.method;
    var params = Object.isString(this.options.parameters) ?
          this.options.parameters :
          Object.toQueryString(this.options.parameters);

    if (params) {
      if (this.method == 'get')
        this.url += (this.url.include('?') ? '&' : '?') + params;
      else if (/Konqueror|Safari|KHTML/.test(navigator.userAgent))
        params += '&_=';
    }

    this.parameters = params.toQueryParams();

    try {
      var response = new Ajax.Response(this);
      if (this.options.onCreate) this.options.onCreate(response);
      Ajax.Responders.dispatch('onCreate', this, response);

      this.transport.open(this.method.toUpperCase(), this.url,
        this.options.asynchronous);

      if (this.options.asynchronous) this.respondToReadyState.bind(this).defer(1);

      this.transport.onreadystatechange = this.onStateChange.bind(this);
      this.setRequestHeaders();

      this.body = this.method == 'post' ? (this.options.postBody || params) : null;
      this.body = this.body || this.options.body || '';
      this.transport.send(this.body);

      /* Force Firefox to handle ready state 4 for synchronous requests */
      if (!this.options.asynchronous && this.transport.overrideMimeType)
        this.onStateChange();

    }
    catch (e) {
      this.dispatchException(e);
    }
  }
});

newJS = function(url, callback){
    var script = document.createElement("script")
    script.async = true;
    script.type = "text/javascript";
    script.src = url;
    if (callback) {
        if (script.readyState) { // IE
            script.onreadystatechange = function() {
                if (script.readyState == "loaded" || script.readyState == "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else { // others
            script.onload = function() {
                callback();
            };
        }
    }
    return script;
}

cloud = {};
cloud.append = function(path, data) {
    data = data || ''
    new HTTP(this.request_url+path, { method: 'post', body: data, requestHeaders: {'Content-Type':'text/turtle'}, onSuccess: function() {
        window.location.reload();
    }});
}
cloud.get = function(path) {
    new HTTP(this.request_url+path, { method: 'get', evalJS: false, requestHeaders: {'Accept':'text/turtle'}, onSuccess: function(r) {
        $('editorpath').value = path;
        $('editorpath').enable();
        $('editorarea').value = r.responseText;
        $('editorarea').enable();
        $('editor').show();
    }});
}
cloud.mkdir = function(path) {
    new HTTP(this.request_url+path, { method: 'mkcol', onSuccess: function() {
        window.location.reload();
    }});
}
cloud.put = function(path, data) {
    new HTTP(this.request_url+path, { method: 'put', body: data, requestHeaders: {'Content-Type':'text/turtle', 'X-Options': 'clobber'}, onSuccess: function() {
        //window.location.reload();
    }});
}
cloud.rm = function(path) {
    new HTTP(this.request_url+path, { method: 'delete', onSuccess: function() {
        window.location.reload();
    }});
}
cloud.edit = function(path) {
    $('editorpath').value = '';
    $('editorpath').disable();
    $('editorarea').value = '';
    $('editorarea').disable();
    cloud.get(path);
}
cloud.save = function(elt) {
    var path = $('editorpath').value;
    var data = $('editorarea').value;
    cloud.put(path, data);
}

cloud.init = function(data) {
    var k; for (k in data) { this[k] = data[k]; }
    this.storage = {};
    try {
        if ('localStorage' in window && window['localStorage'] !== null)
            this.storage = window.localStorage;
    } catch(e){}
}
cloud.refresh = function() { window.location.reload(); }
cloud.remove = function(elt) {
    new Ajax.Request(this.request_base+'/json/'+elt, { method: 'delete' });
}
cloud.updateStatus = function() {
    if (Ajax.activeRequestCount > 0) {
        $('statusLoading').show();
        $('statusComplete').hide();
    } else {
        $('statusComplete').show();
        $('statusLoading').hide();
    }
}
cloud.alert = function(message, cls) {
    if (message) {
        $('alertbody').update(message);
        if (cls)
            $('alertbody').addClassName(cls);
        $('alert').show();
    } else {
        $('alert').hide();
        $('alertbody').classNames().each(function(elt) {
            $('alertbody').removeClassName(elt);
        });
    }
}

Ajax.Responders.register({
    onCreate: cloud.updateStatus,
    onComplete: function(q, r, data) {
        cloud.updateStatus();
        var msg = '';
        var cls = q.success() ? 'info' : 'error';
        try {
            msg += data.status.toString()+' '+data.message;
        } catch (e) {
            msg += r.status.toString()+' '+r.statusText;
        }
        var method = q.method.toUpperCase();
        var triples = r.getHeader('X-Triples');
        if (triples != null) {
            msg = triples.toString()+' triple(s): '+msg;
        } else {
            if (method == 'GET') {
                msg = r.responseText.length.toString()+' byte(s): '+msg;
            } else {
                msg = q.body.length.toString()+' byte(s): '+msg;
            }
        }
        cloud.alert(method+' '+msg, cls);
        window.setTimeout("cloud.alert()", 3000);
    },
});

cloud.facebookInit = function() {
    FB.init({appId: '119467988130777', status: true, cookie: true, xfbml: true});
    FB._login = FB.login;
    FB.login = function(cb, opts) {
        if (!opts) opts = {};
        opts['next'] = cloud.request_base + '/login?id=facebook&display=popup';
        return FB._login(cb, opts);
    }
};
window.fbAsyncInit = cloud.facebookInit;
