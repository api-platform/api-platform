<p align="center"><img src="https://api-platform.com/logo-250x250.png" alt="API Platform"></p>

API Platform is a next-generation web framework designed to easily create API-first projects without
compromising extensibility and flexibility:

* Design your own data model as plain old PHP classes or [**import an existing one**](https://api-platform.com/docs/schema-generator/) from the [Schema.org](https://schema.org/) vocabulary
* **Expose in minutes a hypermedia REST API** with pagination, data validation, access control, relation embedding, filters and error handling...
* Benefit from Content Negotiation: [JSON-LD](http://json-ld.org), [Hydra](http://hydra-cg.com), [HAL](http://stateless.co/hal_specification.html), [YAML](http://yaml.org/), [JSON](http://www.json.org/), [XML](https://www.w3.org/XML/) and [CSV](https://www.ietf.org/rfc/rfc4180.txt) are supported out of the box
* Enjoy the **beautiful automatically generated API documentation** (Swagger/OpenAPI)
* Add [**a convenient Material Design administration interface**](https://github.com/api-platform/admin) built with [React](https://facebook.github.io/react/) without writing a line of code
* **Scaffold a fully functional Single-Page-Application** built with [React](https://facebook.github.io/react/), [Redux](http://redux.js.org/), [React Router](https://reacttraining.com/react-router/) and [Bootstrap](https://getbootstrap.com/) thanks to [the client generator](https://api-platform.com/docs/client-generator/)
* Install a development environment and deploy your project in production using **[Docker](https://docker.com)**
* Easily add **[JSON Web Token](https://jwt.io/) or [OAuth](https://oauth.net/) authentication**
* Create specs and tests with a **developer friendly API testing tool** on top
  of [Behat](http://behat.org/)

[![Build Status](https://travis-ci.org/api-platform/core.svg?branch=master)](https://travis-ci.org/api-platform/core)
[![Build status](https://ci.appveyor.com/api/projects/status/grwuyprts3wdqx5l?svg=true)](https://ci.appveyor.com/project/dunglas/dunglasapibundle)
[![Coverage Status](https://coveralls.io/repos/github/api-platform/core/badge.svg)](https://coveralls.io/github/api-platform/core)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/92d78899-946c-4282-89a3-ac92344f9a93/mini.png)](https://insight.sensiolabs.com/projects/92d78899-946c-4282-89a3-ac92344f9a93)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/api-platform/core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/api-platform/core/?branch=master)

The official project documentation is available **[on the API Platform website](https://api-platform.com)**.

API Platform embraces open web standards (Swagger, JSON-LD, Hydra, HAL, JWT, OAuth,
HTTP...) and the [Linked Data](https://www.w3.org/standards/semanticweb/data) movement. Your API will automatically
expose structured data in Schema.org/JSON-LD. It means that your API Platform application
is usable **out of the box** with technologies of the semantic
web.

It also means that **your SEO will be improved** because **[Google leverages these
formats](https://developers.google.com/structured-data/)**.

Last but not least, API Platform is built on top of the [Symfony](https://symfony.com) framework.
It means than you can:

* use **thousands of Symfony bundles** with API Platform
* integrate API Platform in **any existing Symfony application**
* reuse **all your Symfony skills** and benefit of the incredible
  amount of Symfony documentation
* enjoy the popular [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html) (used by default, but fully optional: you can
  use the data provider you want, including but not limited to MongoDB ODM and ElasticSearch)

Install
-------

[Read the official "Getting Started" guide](https://api-platform.com/docs/core/getting-started).

Credits
-------

Created by [Kévin Dunglas](https://dunglas.fr). Commercial support available at [Les-Tilleuls.coop](https://les-tilleuls.coop).
