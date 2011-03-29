/* $Id$ */

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
cloud.init = function(data) { var k; for (k in data) { this[k] = data[k]; } }
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
Ajax.Responders.register({
    onCreate: cloud.updateStatus,
    onComplete: cloud.updateStatus
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
