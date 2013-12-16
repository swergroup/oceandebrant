backend default {
    .host = "127.0.0.1";
    .port = "8080";
}

acl purge{
    "localhost";
    "127.0.0.1";
}

sub vcl_recv {
    if (req.restarts == 0) {
        if (req.http.x-forwarded-for) {
            set req.http.X-Forwarded-For =
            req.http.X-Forwarded-For + ", " + client.ip;
        } else {
            set req.http.X-Forwarded-For = client.ip;
        }
    }
    if (req.request == "PURGE") {
        if ( !client.ip ~ purge) {
            error 405 "Not allowed.";
        }
        return (lookup);
    }
    if (req.request != "GET" &&
        req.request != "HEAD" &&
        req.request != "PUT" && 
        req.request != "POST" &&
        req.request != "TRACE" &&
        req.request != "OPTIONS" &&
        req.request != "DELETE") {
            return (pipe);
    }
    if (req.request != "GET" && req.request != "HEAD") {
        return (pass);
    }
    if (!(req.url ~ "wp-(login|admin)") &&
        !(req.url ~ "&preview=true" ) ) {
        unset req.http.cookie;
    }
 
    if (req.http.Authorization || req.http.Cookie) {
        return (pass);
    }
    return (lookup);
}
 
sub vcl_hit {
    if (req.request == "PURGE") {
        purge;
        error 200 "Purged.";
    }
    return (deliver);
}
 
sub vcl_miss {
    if (req.request == "PURGE") {
        purge;
        error 200 "Purged.";
    }
    return (fetch);
}
 
sub vcl_fetch {
    if (!(req.url ~ "wp-(login|admin)")) {
        unset beresp.http.set-cookie;
        set beresp.ttl = 96h;
    }
 
    if (beresp.ttl <= 0s ||
        beresp.http.Set-Cookie ||
        beresp.http.Vary == "*") {
            set beresp.ttl = 120 s;
            return (hit_for_pass);
    }
    return (deliver);
}