# Jenkins API Client for PHP

The Jenkins component provides an object-oriented interface to connect with a 
Jenkins installation through the REST API.

Please note that this library is still in development, usage in production
environments is highly discouraged. Feel free to [open an issue](https://github.com/codedmonkey/jenkins/issues) 
for any feature requests or unexpected behavior.

## Installation
This component requires an [HTTP Client](http://docs.php-http.org/en/latest/clients.html)
that integrates with HTTPlug. For integration with the Symfony framework, see [HttplugBundle](http://docs.php-http.org/en/latest/integrations/symfony-bundle.html).

This component is available as a package on [Packagist](https://packagist.org):

```bash
composer require codedmonkey/jenkins php-http/curl-client
```

## Usage
To set up a connection to the Jenkins installation, provide a URL to the Jenkins
client. This is usually in the format `http://username:token@hostname:port`.

```php
use CodedMonkey\Jenkins\Jenkins;

$jenkins = new Jenkins('http://tim:1234567890@codedmonkey.com:8080');
```

## Resources
* [GitHub repository](https://github.com/codedmonkey/jenkins)
* [Packagist package](https://packagist.org/packages/codedmonkey/jenkins)

## License
This component is released under the [MIT license](license.markdown).
