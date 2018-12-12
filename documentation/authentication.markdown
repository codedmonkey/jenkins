# Authentication

## Using an API token
To retrieve an API token, open Jenkins and browse to Profile > Configure > API
Token.

The easiest way to authenticate using an API token is to include the authentication
details in the Jenkins URL:

```php
use CodedMonkey\Jenkins\Jenkins;

$jenkins = new Jenkins('http://username:token@hostname:port');
```

Alternatively, it's possible to configure the HTTP client to automatically
authenticate any request to the Jenkins API with the [HTTPlug Authentication Plugin](http://docs.php-http.org/en/latest/plugins/authentication.html):

```php
use CodedMonkey\Jenkins\Jenkins;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Discovery\HttpClientDiscovery;
use Http\Message\Authentication\BasicAuth;

$authentication = new BasicAuth('username', 'token');
$authenticationPlugin = new AuthenticationPlugin($authentication);

$httpClient = new PluginClient(
    HttpClientDiscovery::find(),
    [$authenticationPlugin]
);

$jenkins = new Jenkins('http://hostname:port', $httpClient);
```
