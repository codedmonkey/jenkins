# Jenkins API Client for PHP

The Jenkins component provides an object-oriented interface to connect with a 
Jenkins installation through the REST API.

**Please note that this library is still in development**, usage in production
environments is highly discouraged. Feel free to [open an issue](https://github.com/codedmonkey/jenkins/issues) 
for any feature requests or unexpected behavior.

## Installation
This component requires an [HTTP Client](http://docs.php-http.org/en/latest/clients.html)
that integrates with HTTPlug. For integration with the Symfony framework, see [HttplugBundle](http://docs.php-http.org/en/latest/integrations/symfony-bundle.html).

This component is available as a package on [Packagist](https://packagist.org):

```bash
composer require codedmonkey/jenkins php-http/curl-client php-http/message
```

## Basic Usage
To set up a connection to the Jenkins installation, provide a URL containing
a username and an API token through HTTP basic authentication. This is usually
formatted as `http://username:token@hostname:port`.

```php
use CodedMonkey\Jenkins\Jenkins;

$jenkins = new Jenkins('http://tim:1234567890@codedmonkey.com:8080');

// Get an array of all jobs
$jenkins->jobs->all();
```

## Documentation
* [Authentication](documentation/authentication.markdown)
* [Jobs](documentation/jobs.markdown)

## Resources
* [GitHub repository](https://github.com/codedmonkey/jenkins)
* [Packagist package](https://packagist.org/packages/codedmonkey/jenkins)

## License
This component is released under the [MIT license](license.markdown).
