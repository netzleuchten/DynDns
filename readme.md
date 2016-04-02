# DynDns

This is a simple HTTP endpoint to update a nameserver record at INWX. Initially written to support our router,
which is able to perform simple GET request against userdefined services, I'd like to share this small piece with
others who need some simple dynamic dns magic, but do not want to rely on external services.

The API class depends on `inwx/domrobot`. The endpoint itself uses `vlucas/phpdotenv`, `symfony/http-foundation`
to add easy configuration and simple handling of requests and responses.

So feel fork this project, extend it to your needs, just use the API class for a command-line tool etc.

## Setup

Your webserver should point directly to `public/`. If it is not possible to use `public/` as your webroot,
please secure your `.env` from access over HTTP.

Open rename the example file `.env` to `.env` and insert your username, password etc.

    INWX_API="https://api.domrobot.com/xmlrpc/"
    INWX_USERNAME="username"
    INWX_PASSWORD="password"
    DOMAIN="sub.domain.tld"
    SECRET_KEY="yoursecretkey"

Update the record with your current IP:

    index.php?key=<YOUR_SECRET_KEY>&ip=<YOUR_IP>

## Security

The endpoint itself will ensure a valid IP and the secret key will block random request.
But I recommend to add an additonal security by using HTTP Authentication.
When using Apache just, uncomment the section in the enclosed `.htaccess`, create and set the path to your `.htpasswd`.
To quickly create username/password use a service like: http://www.htaccesstools.com/htpasswd-generator/

Example for calling the endpoint with HTTP authentication:

    http://username:password@index.php?key=<YOUR_SECRET_KEY>&ip=<YOUR_IP>

## To do

* Add some logging