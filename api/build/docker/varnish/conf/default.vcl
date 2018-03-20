vcl 4.0;

import std;

backend default {
  .host = "api";
  .port = "80";
  # Health check
  #.probe = {
  #  .url = "/";
  #  .timeout = 5s;
  #  .interval = 10s;
  #  .window = 5;
  #  .threshold = 3;
  #}
}

# Hosts allowed to send BAN requests
acl ban {
  "localhost";
  "php";
}

sub vcl_backend_response {
  # Ban lurker friendly header
  set beresp.http.url = bereq.url;

  # Add a grace in case the backend is down
  set beresp.grace = 1h;
}

sub vcl_deliver {
  # Don't send cache tags related headers to the client
  unset resp.http.url;
  # Uncomment the following line to NOT send the "Cache-Tags" header to the client (prevent using CloudFlare cache tags)
  #unset resp.http.Cache-Tags;
}

sub vcl_recv {
  # Remove the "Forwarded" HTTP header if exists (security)
  unset req.http.forwarded;

  # To allow API Platform to ban by cache tags
  if (req.method == "BAN") {
    if (client.ip !~ ban) {
      return(synth(405, "Not allowed"));
    }

    if (req.http.ApiPlatform-Ban-Regex) {
      ban("obj.http.Cache-Tags ~ " + req.http.ApiPlatform-Ban-Regex);

      return(synth(200, "Ban added"));
    }

    return(synth(400, "ApiPlatform-Ban-Regex HTTP header must be set."));
  }
}

sub vcl_hit {
  if (obj.ttl >= 0s) {
    # A pure unadulterated hit, deliver it
    return (deliver);
  }
  if (std.healthy(req.backend_hint)) {
    # The backend is healthy
    # Fetch the object from the backend
    return (miss);
  }
  # No fresh object and the backend is not healthy
  if (obj.ttl + obj.grace > 0s) {
    # Deliver graced object
    # Automatically triggers a background fetch
    return (deliver);
  }
  # No valid object to deliver
  # No healthy backend to handle request
  # Return error
  return (synth(503, "API is down"));
}
