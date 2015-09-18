The API Platform framework
==========================

*The new breed of web frameworks*

[![API Platform](https://api-platform.com/images/api-platform-logo.27a08537.png)](https://api-platform.com)

The official project documentation is available **[on the API Platform website][31]**.

API Platform is a next-generation PHP web framework designed to create
API-first projects easily but without compromise in the field of extensibility and
flexibility:

* Use our awesome code generator to **bootstrap a fully-functional data model from
  [Schema.org][8] vocabularies** with ORM mapping and validation (you can also do
  it manually)
* **Expose in minutes an hypermedia REST API** that works out of the box by reusing
  entity metadata (ORM mapping, validation and serialization) ; that embraces [JSON-LD][1],
  [Hydra][2] and provides a ton of features (CRUD, validation and error handling,
  relation embedding, filters, ordering...)
* Enjoy the **beautiful automatically generated API documentation** (Swagger-like)
* Add easily **[JSON Web Token][25] or [OAuth][26] authentication**
* Create specs and tests with a **developer friendly API context system** on top
  of [Behat][10]
* Develop your website UI, webapp, mobile app or anything else you want using
  **your preferred client-side technologies**! Tested and approved with **AngularJS**
  (integration included), **Ionic**, **React** and **native mobile** apps

API Platform embraces open web standards (JSON-LD, Hydra, JWT, OAuth,
HTTP, HTML5...) and the [Linked Data][27] movement. Your API will automatically
expose structured data in Schema.org/JSON-LD. It means that your API Platform application
is usable **out of the box** with technologies of the semantic
web.

It also means that **your SEO will be improved** because **[Google recommends these
formats][28]**.
And yes, Google crawls full-Javascript applications [as well as old-fashioned ones][29].

Last but not least, API Platform is built on top of the [Symfony][5]
full-stack framework and follows its best practices. It means than you can:

* use **thousands of Symfony bundles** with API Platform
* integrate API Platform in **any existing Symfony application**
* reuse **all your Symfony skills** and benefit of the incredible
  amount of Symfony documentation available
* enjoy the popular [Doctrine ORM][6] (used by default, but fully optional: you can
  use the data provider you want, including but not limited to MongoDB ODM and ElasticSearch)

Install
-------

Use [Composer][3] to create your new project:

    composer create-project api-platform/api-platform my-api

Start to hack
-------------

A demo application (a bookstore) is pre-installed.

* Run `app/console server:start` and open `http://localhost:8000` in any
  HTTP client to access the API
* Open `http://localhost:8000/doc` to read the HTML documentation an play
  with the sandbox
* Give a try to the [HydraConsole][4] client to leverage JSON-LD and Hydra
  features
* Build your first custom client using Javascript, CORS headers are already
  configured

What's inside?
--------------

API Platform provides rock solid foundations to build your project:

* [**The Schema Generator**][7] to generate PHP entities from [Schema.org][8] types with
Doctrine ORM mappings, Symfony validation and extended PHPDoc
* [**The API bundle**][9] to expose in minutes your entities as a JSON-LD and
 Hydra enabled hypermedia REST API
* [**NelmioApiDocBundle**][24] integrated with the API bundle to
automatically generate a beautiful human-readable documentation and a
sandbox to test the API
* [Behat][10] and [Behatch][11] configured to easily test the API
* The full power of the [**Symfony**][5] framework and its ecosystem
* **[Doctrine][6] ORM/DBAL**
* An AppBundle you can use to start coding
* Annotations enabled for everything
* Swiftmailer and Twig to create beautiful emails

It comes pre-configured with the following bundles:

  * [**Symfony**][5] - API Platform is built on top of the full-stack
    Symfony framework
  * [**API Platform's API bundle**][9] - Creates powerful Hypermedia APIs supporting JSON-LD
    and Hydra
  * [**NelmioCorsBundle**][12] - Support for CORS headers
  * [**NelmioApiDocBundle**][24] - Generates a human-readable documentation
  * [**FosHttpCacheBundle**][13] - Add powerful caching capacities, supports Varnish,
    Nginx a built-in PHP reverse proxy
  * [**SensioFrameworkExtraBundle**][14] - Adds several enhancements, including
    template and routing annotation capability
  * [**DoctrineBundle**][15] - Adds support for the Doctrine ORM
  * [**TwigBundle**][16] - Adds support for the Twig templating engine (useful
    in emails)
  * [**SecurityBundle**][17] - Authentication and roles by integrating Symfony's
    security component
  * [**SwiftmailerBundle**][18] - Adds support for Swiftmailer, a library for sending
    emails
  * [**MonologBundle**][19] - Adds support for Monolog, a logging library
  * **WebProfilerBundle** (in dev/test env) - Adds profiling functionality and
    the web debug toolbar
  * **SensioDistributionBundle** (in dev/test env) - Adds functionality for configuring
    and working with Symfony distributions
  * [**SensioGeneratorBundle**][20] (in dev/test env) - Adds code generation capabilities

All libraries and bundles included in API Platform are released under
the MIT or BSD license.

Authentication support
----------------------

Json Web Token is a lightweight and popular way to handle authentication in a
stateless way. Install [**LexikJWTAuthenticationBundle**][21] to adds JWT support
to API Platform.

Oauth support can also be easily added using [**FOSOAuthServerBundle**][22].

Verifying release signature
---------------------------

Software released by the API Platform project [can be verified with `git`](https://git-scm.com/book/tr/v2/Git-Tools-Signing-Your-Work#Verifying-Tags)
using the following GPG public signature:

```
-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG v1

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
xA==
=RO9E
-----END PGP PUBLIC KEY BLOCK-----
```

Enjoy!

Credits
-------

Created by [Kévin Dunglas][23]. Sponsored by [Les-Tilleuls.coop][30]
Commercial support available upon request.

[1]:  http://json-ld.org
[2]:  http://hydra-cg.com
[3]:  https://getcomposer.org
[4]:  http://www.hydra-cg.com/
[5]:  https://symfony.com
[6]:  http://www.doctrine-project.org
[7]:  https://api-platform.com/doc/1.0/schema-generator/
[8]:  http://schema.org
[9]:  https://api-platform.com/doc/1.0/api-bundle/
[10]: https://behat.readthedocs.org
[11]: https://github.com/Behatch/contexts
[12]: https://github.com/nelmio/NelmioCorsBundle
[13]: https://foshttpcachebundle.readthedocs.org
[14]: https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/index.html
[15]: https://symfony.com/doc/current/book/doctrine.html
[16]: https://symfony.com/doc/current/book/templating.html
[17]: https://symfony.com/doc/current/book/security.html
[18]: https://symfony.com/doc/current/cookbook/email.html
[19]: https://symfony.com/doc/current/cookbook/logging/monolog.html
[20]: https://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html
[21]: https://github.com/lexik/LexikJWTAuthenticationBundle
[22]: https://github.com/FriendsOfSymfony/FOSOAuthServerBundle
[23]: https://dunglas.fr
[24]: https://github.com/nelmio/NelmioApiDocBundle
[25]: http://jwt.io/
[26]: http://oauth.net/
[27]: https://en.wikipedia.org/wiki/Linked_data
[28]: https://developers.google.com/structured-data/
[29]: http://searchengineland.com/tested-googlebot-crawls-javascript-heres-learned-220157
[30]: https://les-tilleuls.coop
[31]: https://api-platform.com
