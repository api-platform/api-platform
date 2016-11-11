The API Platform Framework
==========================

[![Join the chat at https://gitter.im/api-platform/api-platform](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/api-platform/api-platform?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/api-platform/core.svg?branch=master)](https://travis-ci.org/api-platform/core)
[![Build status](https://ci.appveyor.com/api/projects/status/grwuyprts3wdqx5l?svg=true)](https://ci.appveyor.com/project/dunglas/dunglasapibundle)
[![Coverage Status](https://coveralls.io/repos/github/api-platform/core/badge.svg)](https://coveralls.io/github/api-platform/core)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/92d78899-946c-4282-89a3-ac92344f9a93/mini.png)](https://insight.sensiolabs.com/projects/92d78899-946c-4282-89a3-ac92344f9a93)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/api-platform/core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/api-platform/core/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5552e93306c318a32a0000fa/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5552e93306c318a32a0000fa)

*The new breed of web frameworks*

[![API Platform](https://api-platform.com/logo-250x250.png)](https://api-platform.com)

The official project documentation is available **[on the API Platform website][31]**.

API Platform is a next-generation PHP web framework designed to easily create
API-first projects without compromising extensibility and
flexibility:

* **Expose in minutes an hypermedia REST API** that works out of the box by reusing
  entity metadata (ORM mapping, validation and serialization) ; that embraces [JSON-LD][1],
  [Hydra][2] and such other data formats like [HAL][32], [YAML][33], [XML][34] or [CSV][35]
  and provides a ton of features (CRUD, validation and error handling, relation embedding, filters, ordering...)
* Enjoy the **beautiful automatically generated API documentation** (Swagger)
* Use our awesome code generator to **bootstrap a fully-functional data model from
  [Schema.org][8] vocabularies** with ORM mapping and validation (you can also do
  it manually)
* Easily add **[JSON Web Token][25] or [OAuth][26] authentication**
* Create specs and tests with a **developer friendly API context system** on top
  of [Behat][10]
* Develop your website UI, webapp, mobile app or anything else you want using
  **your preferred client-side technologies**! Tested and approved with **React**, **AngularJS**
  (integration included), **Ionic**  and **native mobile** apps

API Platform embraces open web standards (Swagger, JSON-LD, Hydra, HAL, JWT, OAuth,
HTTP...) and the [Linked Data][27] movement. Your API will automatically
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
  amount of Symfony documentation
* enjoy the popular [Doctrine ORM][6] (used by default, but fully optional: you can
  use the data provider you want, including but not limited to MongoDB ODM and ElasticSearch)

Install
-------

[Read the official "Getting Started" guide](https://api-platform.com/docs/core/getting-started).

What's inside?
--------------

API Platform provides rock solid foundations to build your project:

* [**The Schema Generator**][7] to generate PHP entities from [Schema.org][8] types with
Doctrine ORM mappings, Symfony validation and extended PHPDoc
* [**The API bundle**][9] to expose in minutes your entities as a JSON-LD and
 Hydra enabled hypermedia REST API
* [**Swagger UI**][24] integrated with the API bundle to
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
  * [**DunglasActionBundle**][36] - Automatically register actions, commands and event
   subscribers as a service
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
[7]:  https://api-platform.com/docs/schema-generator/
[8]:  http://schema.org
[9]:  https://api-platform.com/docs/core/getting-started#installing-api-platform-core
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
[24]: http://swagger.io/swagger-ui/
[25]: http://jwt.io/
[26]: http://oauth.net/
[27]: https://en.wikipedia.org/wiki/Linked_data
[28]: https://developers.google.com/structured-data/
[29]: http://searchengineland.com/tested-googlebot-crawls-javascript-heres-learned-220157
[30]: https://les-tilleuls.coop
[31]: https://api-platform.com
[32]: http://stateless.co/hal_specification.html
[33]: http://yaml.org/
[34]: https://www.w3.org/XML/
[35]: https://www.ietf.org/rfc/rfc4180.txt
[36]: https://github.com/dunglas/DunglasActionBundle
