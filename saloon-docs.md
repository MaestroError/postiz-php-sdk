# Saloon

A Laravel / PHP package that helps you write beautiful API integrations and SDKs.

{% hint style="warning" %}
You are viewing the documentation for a **very out-of-date** version of Saloon. [Click here to view the latest.](https://app.gitbook.com/o/cASkBOXfspBu84TFkYqQ/s/AnnOFZ5KwPjnXI3FPOQw/)
{% endhint %}

![Making a request, sending it and retrieving the JSON data as an array.](https://1079118277-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2FWZee3WHlwQmemMUmidXH%2Fuploads%2F75HXVncS0JSqGFbSz6Gr%2FFJFXPeaXEAAihla.jpg?alt=media\&token=1d240972-811f-4759-84fb-fae82d9bc430)

### Introduction

Saloon offers a fluent, object-oriented wrapper to build your next API integration or PHP SDK. It makes sharing API requests throughout your application a breeze. You don’t have to configure a HTTP client, so you can start sending requests really quickly.

If you need request faking for your tests, Saloon has it out of the box alongside many other useful tools like OAuth2 boilerplate and caching. If you use Laravel, there's also a dedicated Laravel package with artisan console commands to help you build even faster.

### Features

* Simple, easy to learn syntax that standardises the way you interact with APIs
* Abstract API integrations into classes so you can keep your code DRY
* Configuration is fast and can be shared across all your requests
* Built on top of Guzzle, one of the most popular PHP HTTP clients.
* Framework agnostic
* Mocking requests for testing
* Great for building your own PHP SDKs
* Authentication & OAuth2 boilerplate already built for you
* Scalable with lots of API integrations across many team members

{% hint style="info" %}
Like the sound of Saloon? Consider [giving it a star on Github](https://github.com/sammyjo20/saloon)!
{% endhint %}

### Why Saloon?

Building API integrations can be time consuming. After you have found an API client to use, you’re faced with lots of configuration to remember and it’s hard to repeat requests without copying and pasting, and then when you introduce patterns like OAuth2 everything gets complicated. You’ll often find yourself writing the same boilerplate code over and over again.&#x20;

We’ve standardised the way we talk to APIs with PSR-7 and PSR-18 but we haven’t got a standard structure to build API integrations.

Saloon aims to solve this.


# Installation

To get started with Saloon, you will need to install it through Composer.&#x20;

If you are building an SDK or a package with Saloon, it's recommended that you use the standard PHP version.

If you are using Laravel, you can install the **saloon-laravel** package which installs Saloon and also provides you with Artisan commands to help you get up and running quickly, as well as better testing functionality.

{% tabs %}
{% tab title="Non-Laravel / PHP Installation" %}

#### Non-Laravel / PHP Installation

```bash
composer require saloonphp/saloon "^1.0"
```

{% hint style="info" %}
Saloon requires PHP 8.0+
{% endhint %}
{% endtab %}

{% tab title="Laravel" %}

#### Laravel Installation

```bash
composer require saloonphp/laravel-plugin "^1.0"
```

{% hint style="info" %}
Saloon requires PHP 8.0+ and Laravel 8+
{% endhint %}
{% endtab %}
{% endtabs %}

After you have installed Saloon, you're ready to start building your first Connector!


# Connectors

Connectors are classes that encapsulate the default attributes of an API integration. At the minimum, a connector expects a Base URL to be defined. You can also register default properties that would be shared with all your requests like headers or HTTP client config.

### Getting Started

We firstly recommend creating a directory for your API integrations. Once you have a chosen directory, create a class that extends the `SaloonConnector` abstract class. After that, just extend the `defineBaseUrl` function.

See the example connector for Laravel Forge, an API for server management.

{% hint style="info" %}
If you are using Laravel, Use the artisan command to create a connector for you.

**php artisan saloon:connector \<Integration Name> \<Connector Name>**
{% endhint %}

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class LaravelForgeConnector extends SaloonConnector
{
    public function defineBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }
}
```

### Default Headers and Query Parameters

Most API integrations will have common headers that should be shared with every request, like the `Content-Type` or the `Accept` headers. Some API integrations may even have default query parameters to be applied to every request. Saloon allows you to define default properties like these easily.

To add default headers you can use the `defaultHeaders` method to your connector. This method expects a keyed array to be returned. You may use an array in the value of a header for multiple header values.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class LaravelForgeConnector extends SaloonConnector
{
    public function defineBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Multiple-Values-Header' => ['Value1', 'Value2'], // Value1;Value2
        ];
    }
}
```

You may also add a `defaultQuery` method to your connector to specify default query parameters to be used on every request. This method expects a keyed array to be returned.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class LaravelForgeConnector extends SaloonConnector
{
    public function defineBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    public function defaultQuery(): array
    {
        return [
            'per_page' => 500, // ?per_page=500
        ];
    }
}
```

### Default HTTP Client Configuration

When creating a connector, you may also want to define custom options to send to the HTTP Client. For example you may want to register a default timeout of 60 seconds for every request. Saloon uses Guzzle as the HTTP Client so you may use any of Guzzle’s options inside the. `defaultConfig` method. This method expects a keyed array to be returned.

[Click here to see a list of the available options Guzzle provide.](https://docs.guzzlephp.org/en/stable/request-options.html)

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class LaravelForgeConnector extends SaloonConnector
{
    public function defineBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    public function defaultConfig(): array
    {
        return [
            'timeout' => 60,
        ];
    }
}
```


# Requests

Saloon's requests are classes that store all the information required to make a request. Within a request, you can define the connector, the HTTP Method (GET, POST, etc.) and the endpoint that you would like to make a request. You can also define headers and query parameters. Traditionally, you would write your HTTP requests each time you need to, but this way, you can write a request class once and use it multiple times in your application.

### Getting Started

Create a class that is in a similar place to your connector. The class should extend the `SaloonRequest` abstract class. After that, create two properties. The first property should be the class name for your connector, the second should be the HTTP method.

* `protected ?string $connector = LaravelForgeConnector::class`
* `protected ?string $method = 'GET'`

You should also extend the `defineEndpoint` public method. This method should contain the endpoint of the request you are making. You may wish to leave this string blank if you do not have a specific endpoint, like when consuming GraphQL APIs. The endpoint will be concatenated with the base URL that you have defined within your connector.

See the example request. This request will GET all of the servers from the Laravel Forge API.

{% hint style="info" %}
If you are using Laravel, Use the artisan command to create a request for you.

**php artisan saloon:request \<Integration Name> \<Request Name>**
{% endhint %}

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;
use App\Http\Integrations\LaravelForge\LaravelForgeConnector;

class GetServersRequest extends SaloonRequest
{
    protected ?string $connector = LaravelForgeConnector::class;

    protected ?string $method = 'GET';

    public function defineEndpoint(): string
    {
        return '/servers';
    }
}
```

### Default Headers and Query Parameters

Some requests require specific headers or query parameters to be sent. To define default headers on your request, you can extend the `defaultHeaders` method. This method expects a keyed array to be returned. You may use an array in the value of a header for multiple header values. These headers will be merged with the connector’s headers.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;
use App\Http\Integrations\LaravelForge\LaravelForgeConnector;

class GetServersRequest extends SaloonRequest
{
    protected ?string $connector = LaravelForgeConnector::class;

    protected ?string $method = 'GET';

    public function defineEndpoint(): string
    {
        return '/servers';
    }

    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Multiple-Values-Header' => ['Value1', 'Value2'], // Value1;Value2
        ];
    }
}
```

When you want to add query parameters to your request you can extend the `defaultQuery` method. This method expects a keyed array to be returned. These query parameters will be merged with the connector’s query parameters.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;
use App\Http\Integrations\LaravelForge\LaravelForgeConnector;

class GetServersRequest extends SaloonRequest
{
    protected ?string $connector = LaravelForgeConnector::class;
    
    protected ?string $method = 'GET';
    
    public function defineEndpoint(): string
    {
        return '/servers';
    }
    
    public function defaultQuery(): array
    {
        return [
            'per_page' => 500, // ?per_page=500,
            'page' => 1, // &page=1
        ];
    }
}
```

### Default HTTP Client Config

When creating a request, you may also want to define custom options to send to the HTTP Client. For example you may want to register a default timeout of 120 seconds on the request. Saloon uses Guzzle as the HTTP Client so you may use any of Guzzle’s options inside the. `defaultConfig` method. This method expects a keyed array to be returned. The configuration options will be merged with the connector’s config.

[Click here to see a list of the available options Guzzle provide.](https://docs.guzzlephp.org/en/stable/request-options.html)

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;
use App\Http\Integrations\LaravelForge\LaravelForgeConnector;

class GetServersRequest extends SaloonRequest
{
    protected ?string $connector = LaravelForgeConnector::class;
    
    protected ?string $method = 'GET';
    
    public function defineEndpoint(): string
    {
        return '/servers';
    }
    
    public function defaultConfig(): array
    {
        return [
            'timeout' => 120,
        ];
    }
}
```

### Using Constructor Arguments

You will often have variables that you want to pass into the request. You may add your own properties to your request class or use a constructor to provide variables into the request instance. Since the request is still a regular class you may customise it how you like.

For example, I want to create a request to retrieve an individual server by an ID. I will add a constructor to accept the server ID and I will concatenate the variable with the endpoint. This way I can pass the ID into every instance of the request.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;
use App\Http\Integrations\LaravelForge\LaravelForgeConnector;

class GetServerRequest extends SaloonRequest
{
    protected ?string $connector = LaravelForgeConnector::class;

    protected ?string $method = 'GET';

    public function __construct(protected int $id)
    {
        //
    }
    
    public function defineEndpoint(): string
    {
        return '/servers/' . $this->id;
    }
} 

// 

$request = new GetServerRequest(id: 12345);
```


# Attaching Data

Most API integrations you will write will often require sending data using a POST/PUT/PATCH request. Saloon makes this easy for you. There are three traits that can be added to attach data to your request. For example, if the API you are integrating with accepts JSON, you should attach the **HasJsonBody** trait to your request.

* HasJsonBody
* HasFormParams
* HasMultipartBody

After you have added one of these three traits to your request, you will have access to use the available methods and can define default data.

### Example Request

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class CreateForgeSiteRequest extends SaloonRequest
{
    use HasJsonBody;
    
    //...
    
    public function defaultData(): array
    {
        return [
            'domain' => $this->domain,
            'type' => 'php',
        ];
    }
    
    public function __construct(
        public string $serverId,
        public string $domain,
    ){}
}
```

{% hint style="info" %}
If you would like to define data on both the connector and the request, you will need to add the same trait to both classes.
{% endhint %}

### Modifying data

During runtime, you can also overwrite or add to the request's data.

```php
<?php

$request = new CreateForgeSiteRequest($serverId, $domain);

$request->setData(['domain' => $customDomain]);

$request->mergeData(['database' => 'test123']);

$request->addData('name', 'my-saloon-server');

$request->getData('name'); // Returns "my-saloon-server".
```

### Available Methods

#### setData($array)

This method lets you overwrite the default data with an array of new data.

#### mergeData($array)

This method lets you merge a new array into the existing data array.

#### addData($parameterName, $parameterValue)

This method lets you add a single piece of data.

#### getData($parameterName)

This method lets you get a particular item in the data array by name.

### Other Form Body Types

Sometimes you will want to send form data that isn't composed in an array format. Saloon comes with a **HasBody** trait dedicated to custom form data like XML and even file streams. Saloon does have a dedicated XML trait, so if that is what you are looking for, [click here](#sending-xml).&#x20;

After you have added the trait, you should add a `defineBody()` method which returns the response.

```php
<?php

namespace App\Http\Saloon\Requests;

use App\Http\Saloon\Connectors\ForgeConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasBody;

class GraphQLRequest extends SaloonRequest
{
    use HasBody;

    // ...
    
    public function defineBody(): mixed
    {
        return 'custom-string-response';
    }   
}
```

{% hint style="info" %}
When using the **HasBody** trait, you should also specify the Content-Type header.
{% endhint %}

### Sending XML

You can use the **HasBody** trait for sending XML, but Saloon also has a dedicated **HasXMLBody** trait to send XML. It automatically adds the headers you need for sending XML.

After you have added the trait, you should add a `defineXMLBody()` method that should return the XML response as a string.

```php
<?php

namespace App\Http\Saloon\Requests;

use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasXMLBody;

class XMLRequest extends SaloonRequest
{
    use HasXMLBody;

    // ...
    
    public function defineXmlBody(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>';
    }
}
```


# Query Parameters

Saloon Connectors and Requests can also be configured to send query parameters with the request.

### Example Request

To add query parameters to your request, you can use the **defaultQuery** method on the connector or request. Here you can specify any query parameters that should be sent.

```php
<?php

namespace App\Http\Saloon\Requests;

use App\Http\Saloon\Connectors\ForgeConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class GetForgeServersRequest extends SaloonRequest
{
    // ...
    
    public function defaultQuery(): array
    {
        return [
            'sort' => 'updated_at',
        ];
    }
}
```

### Modifying query parameters

During runtime, you can also overwrite or add to the request's query parameters.

```php
<?php

$request = new GetForgeServersRequest();

$request->setQuery(['sort' => $sort]);

$request->mergeQuery(['include' => 'user']);

$request->addQuery('X-Identifier', 'Saloon');

$request->getQuery('X-Identifier'); // Returns "Saloon".
```

### Available Methods

#### setQuery($array)

This method lets you overwrite the default query parameters with an array of new ones.

#### mergeQuery($array)

This method lets you merge a new array into the existing query parameter array.

#### addQuery($parameterName, $parameterValue)

This method lets you add a query parameter in your code after you have instantiated the request class.

#### getQuery($parameterName)

This method lets you get a particular query parameter by name.


# Sending Requests

After you have created your request, it is ready to be sent off to the internet! 🚀

There are three ways to send requests. You can use the request class directly, send requests through your connector, or you can register requests on your connector and use method-style access to send your requests.

### Sending requests using the request class

Just instantiate your request class and use the `send()` method.

```php
<?php

$request = new GetForgeServerRequest(serverId: '123456');

$response = $request->send();
```

### Sending requests using the connector class

This is useful if your connector has any constructor arguments. Just instantiate your connector class and use the `send()` method.

```php
<?php

$connector = new ForgeConnector($apiToken);
$request = $connector->request(new GetForgeServerRequest(serverId: '123456'));

$response = $request->send();

// or...

$connector->send(new GetForgeServerRequest(serverId: '123456'));
```

#### Connector Constructors

If your connector has a constructor inside with specific data, you should consider sending requests through the connector rather than the request. [Click here to read more.](#sending-requests-using-your-connector)

Alternatively, you can overwrite the connector that a request uses, by using the **setConnector()** method on the request before calling the **send()** method.

```php
<?php

$connector = new ForgeConnector($apiKey);
$request = new GetForgeServerRequest(serverId: '123456');

$request->setConnector($connector);

$request->send();
```

### Sending requests inside an SDK

Saloon offers a great framework for building SDKs. [Click here to read more](https://docs.saloon.dev/the-basics/sdk-style-connectors)

### Modifying the request before it is sent

As mentioned previously, you can also use methods like `addHeader()` and `setConfig()` right before you send the request. This is useful if you have conditional headers, config or form data you need to pass in at the last minute.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;
use App\Http\Saloon\Requests\GetForgeServerRequest;

$request = new GetForgeServerRequest(serverId: '123456');

$request->setHeaders($array);
$request->addConfig('debug', true);
$request->addData('my-field', 'my-value');

$response = $request->send();

// Or if you are using the connector...

$connector = new ForgeConnector;
$request = $connector->request(new GetForgeServerRequest(serverId: '123456'));

$request->setHeaders($array);
$request->addConfig('debug', true);
$request->addData('my-field', 'my-value');

$response = $request->send();

// Or if you are using method-style access

$request = ForgeConnector::getForgeServerRequest(serverId: '123456');

$request->setHeaders($array);
$request->addConfig('debug', true);
$request->addData('my-field', 'my-value');

$response = $request->send();
```

{% hint style="info" %}
The **addData, setData** and **mergeData** methods will not do anything unless you [attach a form data trait](https://docs.saloon.dev/the-basics/attaching-data).
{% endhint %}


# Responses

Saloon requests will return with a **SaloonResponse** class that you can interact with the response with.

### Available Methods

* json(): array
* body(): string
* stream(): StreamInterface
* object(): object
* collect(): Collection
* dto(): object
* dom(): Crawler (Requires [symfony/dom-crawler](https://packagist.org/packages/symfony/dom-crawler))
* header($header): string
* headers(): array
* status(): int
* successful(): bool
* ok(): bool
* redirect(): bool
* clientError(): bool
* serverError(): bool
* onError(callable $callback)
* toException()
* throw()
* xml()
* getRequestOptions(): array
* getGuzzleException(): ?RequestException
* isCached(): bool
* isMocked(): bool
* toGuzzleResponse(): Response
* toPsrResponse(): Response

### Handling Failures

Sometimes the API you are connecting with will fail. When this happens, Saloon has a really easy way to see the error response as well as handling it. If the response failed, you will still receive a **SaloonResponse**, but the status will represent the failure. To get the data from the response, you should use the `json()` or the `body()` methods to see the message from the server.

#### Throwing exceptions on failures

Sometimes you want to just let Saloon throw an exception if the request failed. To do this, you can use the `throw()` method on the response. This will create a **SaloonRequestException** containing the error as well as methods to access the original response.

```php
<?php

use App\Http\Saloon\Requests\GetForgeServerRequest;

$request = new GetForgeServerRequest(serverId: '123456');
$response = $request->send();

$response->throw(); // Will throw SaloonRequestException if the request fails.

$data = $response->json();
```

### Casting to Data Transfer Objects (DTOs)

You may wish to cast the data you receive in an API response to a data transfer object (DTO). Saloon has a built-in plugin that makes this easy. [Click here to read more about casting to DTOs.](https://docs.saloon.dev/the-basics/responses/data-transfer-objects)

### Custom Responses

Sometimes you may want to use your own response class. This is is useful if you would like to add your own methods or overwrite Saloon's response methods.&#x20;

To use a custom response, firstly create your custom response class and then overwrite the **$response** property on your connector or request.

{% hint style="info" %}
If you are using Laravel, you can use the **php artisan saloon:response** Artisan command to create a response.
{% endhint %}

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class ForgeConnector extends SaloonConnector
{
    // ...

    protected ?string $response = CustomResponse::class;
    
    // ...
}
```

{% hint style="info" %}
If the custom response is added to the connector, it will be applied to all requests made by the connector.
{% endhint %}
{% endtab %}

{% tab title="Request" %}

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;

class GetForgeServerRequest extends SaloonRequest
{
    // ...

    protected ?string $response = CustomResponse::class;
    
    // ...
}
```

{% endtab %}
{% endtabs %}

{% hint style="warning" %}
Your custom response must extend **SaloonResponse.**
{% endhint %}


# Data Transfer Objects

You may wish to cast the data you receive in an API response to a data transfer object (DTO). Saloon has a built-in plugin that makes this easy. With this plugin, you can specify the DTO on a per-request basis.&#x20;

### Configuring

Firstly, add the **CastsToDto** trait to your request.

```php
<?php

use App\Http\Saloon\Connectors\ForgeConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\CastsToDto;

class GetForgeServerRequest extends SaloonRequest
{
    use CastsToDto;

    protected ?string $method = Saloon::GET;

    protected ?string $connector = ForgeConnector::class;
    
    // ...
}
```

After that, add the `castToDto` method to your request. It has one argument which is an instance of **SaloonResponse.** This method is only run after a successful response has been received from the API. This method should return your data transfer object fully constructed from the response data you received.&#x20;

```php
<?php

// ...

use App\Data\Server;

class GetForgeServerRequest extends SaloonRequest
{
    use CastsToDto;
    
    // ...
    
    protected function castToDto(SaloonResponse $response): object
    {
        return Server::fromSaloon($response);
    }
}
```

This is how my data transfer object looks internally. I have created a `fromSaloon` method however you can use any method of populating your DTO.

```php
<?php

class Server
{
    public function __construct(
        public string $serverId,
        public string $name,
    ){}

    public static function fromSaloon(SaloonResponse $response): self
    {
        $data = $response->json();

        return new static($data['id'], $data['name']);
    }
}
```

### Retrieving your DTO

Finally, when you retrieve a successful response from the API, you can use the `dto()` method on the response to get the fully constructed DTO.

```php
<?php

$request = new GetForceServerRequest(serverId: '12345');
$response = $request->send();

$server = $response->dto();
```

{% hint style="warning" %}
Saloon will only cast the response to your DTO if the response was successful.
{% endhint %}


# Building SDKs

Saloon provides everything you need to build a great SDK for an API. Saloon already offers the ability to mock responses, provide authentication, implement OAuth2 boilerplate and even record your API requests in your tests so that you can write tests for the API integration without hitting the real API each time. With Saloon you will be up and running in minutes. No more writing HTTP boilerplate code!

### Saloon SDK Template

Saloon has an official SDK template that you can clone straight from GitHub or download and use. It has everything you need to get started building an SDK, and even has a configuration command to set up the class namespace and file names. [Click here to get started.](https://github.com/Sammyjo20/saloon-sdk-template)

### Getting Started

To start building an SDK with Saloon, we recommend that you create your base SDK class and extend the **SaloonConnector** class. The **SaloonConnector** class allows you to configure the base URL, default headers, configuration and apply plugins. You can also configure request mocking really easily, so this will come in really handy when you want to write tests for the API without making real requests.

#### Example SDK Connector

Here is an example, I have created an SDK for the popular movie database API - TMDB. As you can see, I have defined the API base URL, as well as use the constructor to request that the person using the API always provides an authentication token.

```php
<?php

namespace Sammyjo20\TMDB;

use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\TMDB\Responses\TMDBResponse;
use Sammyjo20\Saloon\Traits\Plugins\AcceptsJson;

class TMDB extends SaloonConnector
{
    use AcceptsJson;

    protected string $apiBaseUrl = 'https://api.themoviedb.org/3';

    protected array $requests = [];

    // Define the base URL.

    public function defineBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    // Constructor requires the token.
    // Also allow to overwrite the API base URL for local servers.

    public function __construct(string $token, string $baseUrl = null)
    {
        $this->withTokenAuth($token);

        if (isset($baseUrl)) {
            $this->apiBaseUrl = $baseUrl;
        }
    }

    // Headers that will be used on all requests

    public function defaultHeaders(): array
    {
        return [
	    'Content-Type' => 'application/json',
	];
    }

    // Default Guzzle Config Options

    public function defaultConfig(): array
    {
        return [
	    'timeout' => 30,
	];
    }
}
```

#### Using the SDK connector

Now that we have created the SDK class that extends the SaloonConnector class, all we need to do is instansiate it and provide the API token.

```php
<?php

use Sammyjo20\TMDB\TMDB;

$tmdb = new TMDB('my-api-token');

// Ready to make requests!
```

### Recommended Method: Using Requests Directly

One of the ways that you can build SDKs in Saloon is by creating requests and then calling them from the SDK connector. This is the simplest way and you will get up and running really quickly with Saloon.

#### 1. Make a Saloon Request

Firstly, you will need to make a SaloonRequest, this is exactly the same as making a normal request described in the documentation. Any requirements like data or pagination should just be provided in each request’s constructor.

#### 2. Call your request

When you have created the request, all that developers would need to do is run it! You can use the `send` method to send a request straight away, or the `request` method to instantiate the request.

```php
<?php

use Sammyjo20\TMDB\TMDB;

$tmdb = new TMDB('my-api-token');

// Send a request straight away.

$response = $tmdb->send(new GetPopularMovies(page: 1));

// Or if you would like to do something with the request before sending it.

$request = $tmdb->request(new GetPopularMovies(page: 1));
```

With this method, it’s really simple to build your SDK. All you would need to do is create all the requests and then document them in your README. Developers using your SDK can just instantiate your SDK and then use the `send` or`request` methods.

### Method Two: Create Request Collections

Alternatively, you can define request classes and groups of requests on your SDK class by using the `$requests` property to define requests. By using this method, you will have to register your API routes, but then developers can use methods to make API calls.

#### Example

Here is an example using the same TMDB SDK as used earlier. You are able to register requests and request collections using Saloon.

```php
$tmdb = new TMDB('my-api-token');

$request = $tmdb->movies()->getPopular(page: 1);
$response = $request->send();
```

#### Individual Requests

If you define a `$requests` array property on your connector. You can specify individual requests that will be converted into "magic" methods.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class TMDB extends SaloonConnector
{
    protected array $requests = [
        GetPopularMovies::class, // $tmdb->getPopularMovies()
    ];

    // ...
}
```

Saloon will create a method for the request based on its name in camelCase. For example, our registered **GetPopularMovies** class will now have a method for it on the connector called `GetPopularMovies`. When you call this method, Saloon will instantiate the **GetPopularMovies** class.

```php
<?php

$tmdb = new TMDB('my-api-token');

$request = $connector->getPopularMovies(page: 1);

$response = $request->send();
```

#### Customising the request methods

You may want to use your own method names for requests on your connector. If you would like to do this, just add a key for the request to rename the method.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class TMDB extends SaloonConnector
{
    protected array $requests = [
        'get_popular_movies' => GetPopularMovies::class, // $tmdb->get_popular_movies()
    ];

    // ...
}
```

#### Request Collections

You can also have many requests in an SDK, each separated into their own groups. Saloon also makes this easy with two ways of doing it. You can build custom request collections that will be returned automatically through magic methods. This is great if you would like to separate your SDK into different groups, like a "movies" group and a "tv shows" group containing different requests.

To get started, create a class and extend the base `RequestCollection` abstract class. Request collections will be given an instance of the connector which you can access.

```php
<?php

use Sammyjo20\Saloon\Http\RequestCollection;

class MoviesCollection extends RequestCollection
{
    public function getPopular(int $page): array
    {
        // You can access $this->connector to make requests...
        
        $request = $this->connector->request(new GetPopularMovies($page));
        
	return $request->send();
    }
    
    // ... Your other methods...
}
```

{% hint style="info" %}
The base **RequestCollection** class contains a constructor which provides the class with the connector instance.
{% endhint %}

After you have created the class, specify the collection in your `$requests` array on your connector.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

class TMDB extends SaloonConnector
{
    protected array $requests = [
        'movies' => MoviesCollection::class,
    ];

    // ...
}
```

Now you can call the custom collection and methods inside of it!

```php
<?php

$tmdb = new TMDB('my-api-token');

$response = $tmdb->movies()->getPopular();
```

#### IDE Auto-Completion

When using request collections and requests inside your SDK connector class, your IDE will not likely know that the methods exist since Saloon uses magic methods to call the requests. You can get around this by defining the methods in a doc-block above the class definition.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;

/**
 * Define this in your doc-block.
 * 
 * @method MoviesCollection movies
 */
class TMDB extends SaloonConnector
{
    protected array $requests = [
        'movies' => MoviesCollection::class,
    ];

    // ...
}
```

### Custom Responses

You may wish to return a custom response instead of the default response to users to make it more developer-friendly. For example, if I wanted to return a TMDBResponse instead of SaloonResponse, I can create a custom response and define it on the SDK. [Click here to read more.](https://docs.saloon.dev/responses#custom-responses)


# Authentication

There are several ways to authenticate with an API, most of the time it happens in headers and/or in request data. Saloon has a couple of common built-in authentication methods to help you with authentication, but you can also create your own authenticators for advanced authentication. You can also provide defaults for authentication if you require it.

If you are using one API key across all your requests, then you can also use the **defaultHeaders** and **defaultConfig** methods. This authentication is recommended if you need to authenticate on a per-request/connector basis.

### Token Authentication

Token Authentication is used if the API expects an *Authorization* header. For example:

```http
"Authorization": "Bearer my-authentication-token"
```

To use the token authentication, just add the `withTokenAuth` method on your requests before sending.

```php
<?php

$request = new GetForgeServerRequest(serverId: 123456);

$request->withTokenAuth('my-token');

$response = $request->send();
```

You can customize the prefix of the Authorization header with the second argument. By default it is set to *Bearer.*

{% hint style="info" %}
If you would like to throw an exception if a token is not provided, add the **RequiresTokenAuth** trait to your connector or your request. If it is added to the connector, the trait's functionality will be applied to all requests.
{% endhint %}

#### Default Authentication

If you would like to provide a default, then add the **defaultAuth** method to your connector or request and return an instance of **TokenAuthenticator**.

```php
<?php

use Sammyjo20\Saloon\Http\Auth\TokenAuthenticator;

class GetForgeServerRequest extends SaloonRequest
{
    // ...
    
    public function defaultAuth(): ?AuthenticatorInterface
    {
        return new TokenAuthenticator('username', 'password');
    }
}
```

### Basic Authentication

Basic authentication is used when the API requires Username + Password basic authentication.

To use basic authentication, just add the `withBasicAuth` method to your requests before sending.

```php
<?php

$request = new GetForgeServerRequest(serverId: 123456);

$request->withBasicAuth('username', 'password');

$response = $request->send();
```

{% hint style="info" %}
If you would like to throw an exception if basic auth is not provided, add the **RequiresBasicAuth** trait to your connector or your request. If it is added to the connector, the trait's functionality will be applied to all requests.
{% endhint %}

#### Default Authentication

If you would like to provide a default, then add the **defaultAuth** method to your connector or request and return an instance of **BasicAuthenticator**.

```php
<?php

use Sammyjo20\Saloon\Http\Auth\BasicAuthenticator;

class GetForgeServerRequest extends SaloonRequest
{
    // ...
    
    public function defaultAuth(): ?AuthenticatorInterface
    {
        return new BasicAuthenticator('username', 'password');
    }
}
```

### Digest Authentication

Digest authentication is used when the API requires Username + Password + Digest authentication.

To use digest authentication, just add the `withDigestAuth` method to your requests before sending.

```php
<?php

$request = new GetForgeServerRequest(serverId: 123456);

$request->withDigestAuth('username', 'password', 'digest');

$response = $request->send();
```

{% hint style="info" %}
If you would like to throw an exception if digest auth is not provided, add the **RequiresDigestAuth** trait to your connector or your request. If it is added to the connector, the trait's functionality will be applied to all requests.
{% endhint %}

#### Default Authentication

If you would like to provide a default, then add the **defaultAuth** method to your connector or request and return an instance of **DigestAuthenticator**.

```php
<?php

use Sammyjo20\Saloon\Http\Auth\DigestAuthenticator;

class GetForgeServerRequest extends SaloonRequest
{
    // ...
    
    public function defaultAuth(): ?AuthenticatorInterface
    {
        return new DigestAuthenticator('username', 'password', 'digest');
    }
}
```

### Creating your own authenticators

Sometimes the API integration you are building requires multiple ways to authenticate, like a token and a certificate or perhaps authenticating an Oauth 2 API. When the built-in authentication is not sufficient, You can build custom "authenticators" that can be transported between your application and Saloon's requests.

#### Authenticators

Authenticators are classes that can be added to connectors or requests and can be programmed to add headers, data or even handlers to requests in order to fully authenticate them before they are sent. Authenticators are really powerful as you can accept as much data as you need, and can even provide a default.

To create an authenticator, make a class that implements the **AuthenticatorInterface**. It will require you to create a `set` method. The set method accepts any properties from the constructor and apply it to the request passed through the `set` method.

{% hint style="info" %}
If you are using Laravel, you can use the **php artisan saloon:auth** Artisan command to create an authenticator.
{% endhint %}

#### Example Authenticator

Here is an example of an authenticator class. As you can see, I am accepting a single public property called "apiKey" and in the set method, I am using that property and adding a header in the set method of the authenticator.

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Interfaces\AuthenticatorInterface;

class CustomAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        public string $apiKey,
    ) {
        //
    }

    public function set(SaloonRequest $request): void
    {
        $request->addHeader('X-API-Key', $this->apiKey);
    }
}
```

#### Using your authenticator

After you have created your authenticator, to apply it to your requests or connector, you can use the `withAuth` method. You can even use it on the underlying connector if you are making multiple requests that should be authenticated with the same connector.

```php
<?php

$user = auth()->user();

$request = new GetForgeServerRequest(serverId: 123456);

$request->withAuth(new CustomAuthenticator($user->forge_api_key));

$response = $request->send();

// You can also apply it to the connector once!

$connector = new ForgeConnector;
$connector->withAuth(new CustomAuthenticator($user->forge_api_key));

$requestA = $connector->request(new GetForgeServerRequest(...));
$requestB = $connector->request(new CreateForgeServerRequest(...));
```

#### Default authenticator

You can also provide a default authenticator to use if one is not provided. On your connector or your request, just add the `defaultAuth` method and return an instance of your custom authenticator.

```php
class ForgeConnector extends SaloonConnector
{
    // ...
    
    public function defaultAuth(): ?AuthenticatorInterface
    {
        return new CustomAuthenticator('generic-forge-api-key');
    }
}
```

{% hint style="info" %}
If you would like to throw an exception if auth is not provided, add the **RequiresAuth** trait to your connector or your request. If it is added to the connector, the trait's functionality will be applied to all requests.
{% endhint %}

### OAuth2 Authentication

Some API providers require you to authenticate using an OAuth2 grant. Saloon has out-of-the-box boilerplate code written to help you get set up really quickly with OAuth2 APIs. [Click here to learn more.](https://docs.saloon.dev/advanced/oauth2-authentication)


# Plugins

Saloon comes with a powerful plugin pattern that allows you to add functionality to your connectors and requests in the form of traits. Saloon has a number of useful plugins that you can make changes to your requests like headers, config or data.

Plugins can be added to a connector to be used on every request or can be added to an individual request.

### Using Plugins

Plugins are really easy to install. They are PHP traits that can be added to either the connector or the request.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\Plugins\AcceptsJson;

class ForgeConnector extends SaloonConnector
{
    use AcceptsJson;

    public function defineBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }
}
```

{% hint style="info" %}
&#x20;If they are added to the connector, their logic will be applied to all requests.
{% endhint %}
{% endtab %}

{% tab title="Request" %}

```php
<?php

use App\Http\Saloon\Connectors\ForgeConnector;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Plugins\AcceptsJson;

class GetForgeServerRequest extends SaloonRequest
{
    use AcceptsJson;

    protected ?string $method = Saloon::GET;

    protected ?string $connector = ForgeConnector::class;

    public function defineEndpoint(): string
    {
        return '/servers/' . $this->serverId;
    }
    
    // ...
}
```

{% endtab %}
{% endtabs %}

### Available plugins

#### AcceptsJson

This plugin will add the `Accept: application/json` header to your requests.

#### AlwaysThrowsOnErrors

This plugin will run `$response->throw()` automatically for you so you don't have to.

#### HasTimeout

This plugin allows you to specify your own timeout in the `$requestTimeout` property (in seconds).

#### WithDebugData

This plugin will enable Guzzle debug mode so you can see more information about your requests.

#### DisablesSSLVerification

This disables SSL verification. This is useful for local development when you may not have a signed SSL certificate.

{% hint style="danger" %}
Do not use this on any production API. Disabling SSL verification means hackers could see your request unencrypted and steal sensitive information like API credentials.
{% endhint %}

#### CastsToDto

This allows you to specify a DTO that a request should cast to. [Click here to read more about DTO casting.](https://docs.saloon.dev/the-basics/responses/data-transfer-objects)

### Creating your own plugins

If you would like to add your own plugins, for example, if you want to install a Guzzle middleware package or if you would like to apply a specific header to your requests, you can do this really easily.&#x20;

Firstly, create a trait in your application. Let's create a **WithTransactionID** plugin. Saloon will look for a "boot" function and will execute it during the request lifecycle. The "boot" function should be the name of the class, prefixed with the word "boot".

{% hint style="info" %}
If you are using Laravel, you can use the **php artisan saloon:plugin** Artisan command to create a plugin.
{% endhint %}

```php
<?php

use Sammyjo20\Saloon\Http\SaloonRequest;

trait WithTransactionID
{
    public function bootWithTransactionID(SaloonRequest $request)
    {
        $request->mergeHeaders([
            'X-Transaction-ID' => '123-YeeHaw-456'
        ]);
    }
}
```

### Available Plugin Methods

Plugins will inherit all of the available methods from the connector or request it is added to, including the ability to add [response interceptors](https://docs.saloon.dev/advanced/response-interceptors) and [handlers](https://docs.saloon.dev/advanced/handlers-middleware).

* [Connector Methods](https://docs.saloon.dev/the-basics/connectors#available-methods)
* [Request Methods](https://docs.saloon.dev/the-basics/requests#available-methods)


# Caching

Sometimes you may wish to cache the responses that come back from the API integrations you are integrating. Saloon has a first-party package plugin that you can install to enable this functionality.

{% hint style="warning" %}
Currently the caching plugin only supports explicit caching, where you define when a request should be cached. There will be an update in the future that introduces caching based on the Cache-Control headers.
{% endhint %}

### Installation

Firstly, install the Saloon Cache Plugin from Composer into your project.

```bash
composer require saloonphp/cache-plugin "^1.0"
```

### Configuration

After you have installed the plugin, add the **AlwaysCacheResponses** trait to your request or connector. After you have added the trait, you will need to define the cache driver and the cache TTL.

```php
<?php

use Sammyjo20\SaloonCachePlugin\Traits\AlwaysCacheResponses;

class GetForgeServerRequest extends SaloonRequest
{
    use AlwaysCacheResponses;

    // ...
    
    public function cacheDriver(): DriverInterface
    {
        //
    }
    
    public function cacheTTLInSeconds(): int
    {
        return 7200;
    }
}
```

{% hint style="danger" %}
If you add the caching plugin to your connector, it will cache every single request that the connector uses.
{% endhint %}

### Cache Drivers

There are three available cache drivers that you can use. The cache driver defines how the cache file will be stored and retrieved by the cache plugin.

#### FlysystemDriver

Allows you to define a Flysystem storage driver that will be used to store the cache files. This allows you to store the cache files in many places like Amazon S3. [Learn more about Flysystem](https://flysystem.thephpleague.com/docs/)

```php
<?php

use League\Flysystem\Filesystem;
use Sammyjo20\SaloonCachePlugin\Drivers\FlysystemDriver;

public function cacheDriver(): DriverInterface
{
    return new FlysystemDriver(new Filesystem(...));
}
```

#### LaravelCacheDriver

Allows you to define a Laravel Cache store like database/redis. This should only be used if you are using Saloon in Laravel.

```php
<?php

use Illuminate\Support\Facades\Cache;
use Sammyjo20\SaloonCachePlugin\Drivers\LaravelCacheDriver;

public function cacheDriver(): DriverInterface
{
    return new LaravelCacheDriver(Cache::store('database'));
}
```

#### SimpleCacheDriver (PSR-16 Compatible)

Allows you to use any PSR-16 compatible cache.

```php
<?php

use Sammyjo20\SaloonCachePlugin\Drivers\SimpleCacheDriver;

public function cacheDriver(): DriverInterface
{
    return new SimpleCacheDriver(new ArrayCache(...));
}
```

### Response

Saloon will respond with a Saloon response (or your custom response if defined) when retrieving a cached response. To check if a response has been cached, you can use the `isCached()` method.

```php
<?php

$request = new GetForgeServerRequest(serverId: '123456');
$response = $request->send();

$response->isCached(); // False on the first request.

$requestTwo = new GetForgeServerRequest(serverId: '123456');
$responseTwo = $requestTwo->send();

$responseTwo->isCached(); // True on the second request
```

### Custom Cache Keys

By default, the cache key will be built up from the full URL of the request, the class name and the headers of the request. The plugin will create a SHA-256 hash based on these three items. If you would like to have your own custom cache key, like using the UUID of a user, then you can extend the `cacheKey` method.

```php
protected function cacheKey(SaloonRequest $request, array $headers): string
{
    return 'my-custom-key';
}
```

### Disable Caching

Sometimes you may wish to disable the caching on a per-request basis for debugging or to bypass caching. You can do this by using the `disableCaching` method on the request.

```php
<?php

$request = new GetForgeServerRequest(serverId: '123456');
$request->disableCaching();

// Send request, will always skip caching.
```

### Invalidating Cache

You may want to make a request and purge the existing cache before making the request. You can use the `invalidateCache` method on the request before sending the request and Saloon will delete any existing cache for that request.

```php
<?php

$request = new GetForgeServerRequest(serverId: '123456');
$request->invalidateCache();

// Send request, will delete any existing cache.
```

### Source Code

[Interested in the source code of this plugin? Click here.](https://github.com/Sammyjo20/saloon-cache-plugin)


# Mocking Responses

It's important when building API integrations or SDKs to test your application's response to particular API requests without actually making a request to the API. Saloon has a fluent, and easy to understand workflow to help you build your tests.

* [Click here for testing in Non-Laravel / PHP projects](https://docs.saloon.dev/next-steps/testing-faking-responses/non-laravel-php)
* [Click here for testing in Laravel projects](https://docs.saloon.dev/next-steps/testing-faking-responses/laravel)


# Non-Laravel / PHP

Saloon provides a way to test your PHP applications and SDKs really easily. You will need to create an instance of `MockClient` and then pass the `MockClient` as an argument to the `send` method on your request. There are multiple ways you can write tests.

### The MockResponse class

The **MockResponse** class is used to create fake responses Saloon understands. It can accept data, a status, headers and config.

```php
use Sammyjo20\Saloon\Http\MockResponse;

MockResponse::make(['name' => 'Sam'], 200, $headers, $config);
```

You can also create a **MockResponse** class from an existing request.

```php
use Sammyjo20\Saloon\Http\MockResponse;

MockResponse::fromRequest(new GetForgeServerRequest(12345), 200);
```

### Basic Usage (Sequence Mocking)

Basic sequence testing allows you to define a number of fake responses. When your application uses Saloon, it will pull out the next response in the sequence, removing it too.

```php
use Sammyjo20\Saloon\Clients\MockClient;
use Sammyjo20\Saloon\Http\MockResponse;

$mockClient = new MockClient([
    MockResponse::make(['name' => 'Sam'], 200),
    MockResponse::make(['name' => 'Alex'], 200),
    MockResponse::make(['error' => 'Server Unavailable'], 500),
]);

(new GetForgeServerRequest)->send($mockClient) // Will return with `['name' => 'Sam']` and status `200`
(new GetForgeServerRequest)->send($mockClient) // Will return with `['name' => 'Alex']` and status `200`
(new GetForgeServerRequest)->send($mockClient) // Will return with `['error' => 'Server Unavailable']` and status `500`
```

### Connector Mocking

You may also explicitly define mock responses for a particular connector that is used. Unlike sequence tests, these are kept even after the response has been sent.

```php
use Sammyjo20\Saloon\Clients\MockClient;
use Sammyjo20\Saloon\Http\MockResponse;

$mockClient = new MockClient([
    ForgeConnector::class => MockResponse::make(['name' => 'Sam'], 200),
    OtherServiceConnector::class => MockResponse::make(['name' => 'Alex'], 200),
]);

(new GetForgeServerRequest)->send($mockClient) // Will return with `['name' => 'Sam']` and status `200`
(new GetForgeServerRequest)->send($mockClient) // Will return with `['name' => 'Sam']` and status `200`
(new OtherServiceRequest)->send($mockClient) // Will return with `['name' => 'Alex']` and status `200`
```

### Request Mocking

You may also explicitly define mock responses for a particular request that is used. Unlike sequence tests, these are kept even after the response has been sent.

```php
use Sammyjo20\Saloon\Clients\MockClient;
use Sammyjo20\Saloon\Http\MockResponse;

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::make(['name' => 'Sam'], 200),
    OtherServiceRequest::class => MockResponse::make(['name' => 'Alex'], 200),
]);

(new GetForgeServerRequest)->send($mockClient) // Will return with `['name' => 'Sam']` and status `200`
(new OtherServiceRequest)->send($mockClient) // Will return with `['name' => 'Alex']` and status `200`
```

### URL Mocking

You can also define fake responses for particular URL patterns. Whenever a request is made for a particular pattern, Saloon will respond to that request.

```php
use Sammyjo20\Saloon\Clients\MockClient;
use Sammyjo20\Saloon\Http\MockResponse;

$mockClient = new MockClient([
    'forge.laravel.com/api/*' => MockResponse::make(['name' => 'Sam'], 200),
    'samcarre.dev/*' => MockResponse::make(['name' => 'Alex'], 200),
    'samcarre.dev/exact' => MockResponse::make(['name' => 'Taylor'], 200), // Exact requests
    '*' => MockResponse::make(['name' => 'Wildcard'], 200), // Any other requests
]);

(new GetForgeServerRequest)->send($mockClient) // Will return with `['name' => 'Sam']` and status `200`
(new OtherServiceRequest)->send($mockClient) // Will return with `['name' => 'Alex']` and status `200`
(new ExactRequest)->send($mockClient) // Will return with `['name' => 'Taylor']` and status `200`
(new WildcardServiceRequest)->send($mockClient) // Will return with `['name' => 'Wildcard']` and status `200`
```

### Adding Expectations

When using faking responses, it's important to be able to check that a specific make request was sent and with the correct data, headers, and config. Saloon provides you with various ways to add expectations to your tests.&#x20;

#### Available Expectations&#x20;

* AssertSent
* AssertNotSent
* AssertSentJson
* AssertNothingSent
* AssertSentCount

To use one of the expectations, you can simply call the method on your **Mock Client**.

```php
<?php

// ...

$response = (new GetForgeServerRequest(123456))->send($mockClient);

$mockClient->assertSent(GetForgeServerRequest::class);
```

The **AssertSent / AssertNotSent** are the two most powerful expectation methods. They can accept a Saloon request, a URL pattern or even a closure where you define if a request/response is what you expect.

```php
<?php

// ... 

$response = (new GetForgeServerRequest(123456))->send($mockClient);

$mockClient->assertSent(GetForgeServerRequest::class);

$mockClient->assertSent('/servers/*');

$mockClient->assertSent(function (SaloonRequest $request, SaloonResponse $response) {
    return $request instanceof GetForgeServerRequest::class 
    && $request->serverId === 123456;
});
```

### Mocking Exceptions

Your test may require you to mock exceptions like Guzzle's RequestException or even your own exceptions that might get thrown. To mock an exception, chain the `throw` method after you have defined your mock response.

```php
<?php

$mockClient = new MockClient([
    MockResponse::make(['name' => 'Sam'], 200)->throw(new MyException('Something bad!'))
]);
 
// ...
```

If you would like to test one of the Guzzle exceptions like RequestException, it will expect you to pass in a PSR-7 request as one of the arguments. For these exceptions, provide a closure and Saloon will fulfil the closure with the PSR-7 request.

```php
<?php

use GuzzleHttp\Exception\ConnectException;

$mockClient = new MockClient([
    MockResponse::make()->throw(fn ($guzzleRequest) => new ConnectException('Unable to connect!', $guzzleRequest))
]);
```

### Using a mock client for all requests

Sometimes you may want to use a mock client for all requests within a connector. This is especially needed for SDKs, where you need to pass data down into different methods. You can also use the `withMockClient` method on either your connector or your request, and it will mean you don't have to define it on every request's `send` method.

```php
<?php

$forgeConnector = new ForgeConnector;

$mockClient = new MockClient([
    GetForgeUserRequest::class => MockResponse::make(['name' => 'Sam'], 200),
]);

$forgeConnector->withMockClient($mockClient);

// Make as many requests as you like without having to pass in the mock client!

$forgeConnector->send(new GetForgeUserRequest);
$forgeConnector->send(new GetForgeUserRequest);
```

### Using closures for mocking

Sometimes, you may need to return a custom mock response based on the request that is currently trying to be sent. With closure/callable mocking, you can do this! Just provide an anonymous function or an invokable class when defining the mock response, and you will get access to the current request before it is converted into a mock response. This is great if you have stored fixtures based on the request and need to load the fixture data up. Yes, this will work with all of the methods above of mocking the request.

```php
<?php

$mockClient = new MockClient([
    function (SaloonRequest $request): MockResponse {
        // Write some custom logic here...
    
        return new MockResponse([...]);
    },
]);
```


# Laravel

When testing in Laravel, use the Saloon facade and call `Saloon::fake()` method before testing your application logic that makes a Saloon request. Saloon will automatically detect when an API request is about to be made and will respond with the fake response - stopping the real API request from being sent. This powerful feature allows you to test **any** feature in your application that uses Saloon requests.

### The MockResponse class

The **MockResponse** class is used to create fake responses Saloon understands. It can accept data, a status, headers and config.

```php
use Sammyjo20\Saloon\Http\MockResponse;

MockResponse::make(['name' => 'Sam'], 200, $headers, $config);
```

You can also create a **MockResponse** class from an existing request.

```php
use Sammyjo20\Saloon\Http\MockResponse;

MockResponse::fromRequest(new GetForgeServerRequest(12345), 200);
```

### Basic Usage (Sequence Mocking)

Basic sequence testing allows you to define a number of fake responses. When your application uses Saloon, it will pull out the next response in the sequence, removing it too.

```php
use Sammyjo20\SaloonLaravel\Facades\Saloon;
use Sammyjo20\Saloon\Http\MockResponse;

Saloon::fake([
    MockResponse::make(['name' => 'Sam'], 200),
    MockResponse::make(['name' => 'Alex'], 200),
    MockResponse::make(['error' => 'Server Unavailable'], 500),
]);

(new GetForgeServerRequest)->send() // Will return with `['name' => 'Sam']` and status `200`
(new GetForgeServerRequest)->send() // Will return with `['name' => 'Alex']` and status `200`
(new GetForgeServerRequest)->send() // Will return with `['error' => 'Server Unavailable']` and status `500`
```

### Connector Mocking

You may also explicitly define mock responses for a particular connector that is used. Unlike sequence tests, these are kept even after the response has been sent.

```php
use Sammyjo20\SaloonLaravel\Facades\Saloon;
use Sammyjo20\Saloon\Http\MockResponse;

Saloon::fake([
    ForgeConnector::class => MockResponse::make(['name' => 'Sam'], 200),
    OtherServiceConnector::class => MockResponse::make(['name' => 'Alex'], 200),
]);

(new GetForgeServerRequest)->send() // Will return with `['name' => 'Sam']` and status `200`
(new GetForgeServerRequest)->send() // Will return with `['name' => 'Sam']` and status `200`
(new OtherServiceRequest)->send() // Will return with `['name' => 'Alex']` and status `200`
```

### Request Mocking

You may also explicitly define mock responses for a particular request that is used. Unlike sequence tests, these are kept even after the response has been sent.

```php
use Sammyjo20\SaloonLaravel\Facades\Saloon;
use Sammyjo20\Saloon\Http\MockResponse;

Saloon::fake([
    GetForgeServerRequest::class => MockResponse::make(['name' => 'Sam'], 200),
    OtherServiceRequest::class => MockResponse::make(['name' => 'Alex'], 200),
]);

(new GetForgeServerRequest)->send() // Will return with `['name' => 'Sam']` and status `200`
(new OtherServiceRequest)->send() // Will return with `['name' => 'Alex']` and status `200`
```

### URL Mocking

You can also define fake responses for particular URL patterns. Whenever a request is made for a particular pattern, Saloon will respond to that request.

```php
use Sammyjo20\SaloonLaravel\Facades\Saloon;
use Sammyjo20\Saloon\Http\MockResponse;

Saloon::fake([
    'forge.laravel.com/api/*' => MockResponse::make(['name' => 'Sam'], 200),
    'samcarre.dev/*' => MockResponse::make(['name' => 'Alex'], 200),
    'samcarre.dev/exact' => MockResponse::make(['name' => 'Taylor'], 200), // Exact requests
    '*' => MockResponse::make(['name' => 'Wildcard'], 200), // Any other requests
]);

(new GetForgeServerRequest)->send() // Will return with `['name' => 'Sam']` and status `200`
(new OtherServiceRequest)->send() // Will return with `['name' => 'Alex']` and status `200`
(new ExactRequest)->send() // Will return with `['name' => 'Taylor']` and status `200`
(new WildcardServiceRequest)->send() // Will return with `['name' => 'Wildcard']` and status `200`
```

### Adding Expectations

When using faking responses, it's important to be able to check that a specific make request was sent and with the correct data, headers, and config. Saloon provides you with various ways to add expectations to your tests.

#### Available Expectations

* AssertSent
* AssertNotSent
* AssertSentJson
* AssertNothingSent
* AssertSentCount

To use one of the expectations, you can simply call the static method on the **Saloon Facade.**

```php
<?php

// ...

$response = (new GetForgeServerRequest(123456))->send();

Saloon::assertSent(GetForgeServerRequest::class);
```

The **AssertSent / AssertNotSent** are the two most powerful expectation methods. They can accept a Saloon request, a URL pattern or even a closure where you define if a request/response is what you expect.

```php
<?php

// ... 

$response = (new GetForgeServerRequest(123456))->send();

Saloon::assertSent(GetForgeServerRequest::class);

Saloon::assertSent('/servers/*');

Saloon::assertSent(function (SaloonRequest $request, SaloonResponse $response) {
    return $request instanceof GetForgeServerRequest 
    && $request->serverId === 123456;
});
```

### Mocking Exceptions

Your test may require you to mock exceptions like Guzzle's RequestException or even your own exceptions that might get thrown. To mock an exception, chain the `throw` method after you have defined your mock response.

```php
<?php

Saloon::fake([
    MockResponse::make(['name' => 'Sam'], 200)->throw(new MyException('Something bad!'))
]);
 
// ...
```

If you would like to test one of the Guzzle exceptions like RequestException, it will expect you to pass in a PSR-7 request as one of the arguments. For these exceptions, provide a closure and Saloon will fulfil the closure with the PSR-7 request.

```php
<?php

use GuzzleHttp\Exception\ConnectException;

Saloon::fake([
    MockResponse::make()->throw(fn ($guzzleRequest) => new ConnectException('Unable to connect!', $guzzleRequest))
]);
```


# Recording Requests

When writing tests for an API integration, it is best to simulate a real request as much as possible. With Saloon's MockResponse class, you can build up example responses however, this can be time-consuming and if an API returns a huge amount of data, it would take a long time to manually write MockResponses and keep it maintained.

Saloon has a feature called fixture recording, this feature will allow you to make a real request to the API you are integrating with and then it will store that response in a file for later. This is a common practice for people writing integrations for APIs, but Saloon makes it effortless.&#x20;

### Setup

Getting started with fixture recording is easy. When defining your mock responses, instead of defining a MockResponse with headers, data and config - use the fixture static property. This property will accept a single argument, the fixture name.

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);
```

{% endtab %}
{% endtabs %}

{% hint style="info" %}
The above example will configure a fixture to be used every time the **UserRequest** is called during mocking. You may also use a sequence of fixtures, connector mocking, or use a fixture on a specific URL path. Read the mocking pages for more information.
{% endhint %}

### How does it work?

Once you have defined a fixture to be used for a particular request pattern, you can make a request just like you normally would. Saloon will check if the fixture already exists, and if it doesn't - it will make the real API request and store the response for next time.

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);

// The initial request will check if a fixture called "singleServer" 
// exists. Because it doesn't exist yet, the real request will be
// sent and the response will be recorded.

$request = new GetForgeServerRequest(12345);
$response = $request->send($mockClient);

// However, the next time the request is made, the fixture will 
// exist, and Saloon will not make the request again.

$request = new GetForgeServerRequest(12345);
$response = $request->send($mockClient);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);

// The initial request will check if a fixture called "singleServer" 
// exists. Because it doesn't exist yet, the real request will be
// sent and the response will be recorded.

$request = new GetForgeServerRequest(12345);
$response = $request->send();

// However, the next time the request is made, the fixture will 
// exist, and Saloon will not make the request again.

$request = new GetForgeServerRequest(12345);
$response = $request->send();
```

{% endtab %}
{% endtabs %}

### Namespacing

Depending on the size of your application and the number of API integrations you have, you may want to namespace the fixtures into their own folders, for example, I may have a "forge" namespace and a "digitalOcean" namespace.

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::fixture('forge/singleServer'),
    GetDigitalOceanServerRequest::class => MockResponse::fixture('digitalOcean/singleServer'),
]);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    GetForgeServerRequest::class => MockResponse::fixture('forge/singleServer'),
    GetDigitalOceanServerRequest::class => MockResponse::fixture('digitalOcean/singleServer'),
]);
```

{% endtab %}
{% endtabs %}

### Configuration

#### Fixture Path

Ordinarily, Saloon will store all fixtures in a **tests/Fixtures/Saloon** directory. If you would like to customise this, you may use the MockConfig class in your tests or in your setUp methods.

```php
<?php

MockConfig::setFixturePath('tests/other-directory')
```

#### Preventing Unwanted Requests

Once you have written all of your tests, you might want to prevent accidental API requests in the future for fixtures that don't exist. If you would like Saloon to throw exceptions if a fixture does not exist, you may do this with the MockConfig class.

```php
<?php

MockConfig::throwOnMissingFixtures()
```

### Advanced Usage

You may want to return custom fixtures based on the request without specifying exact names of fixtures. For example, I might want to build a fixture name based on the name of the request being sent. You may use a closure inside the mock client and write the custom logic to meet these needs.&#x20;

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    '*' => function (SaloonRequest $request) {
        $reflection = new ReflectionClass($request);

        return MockResponse::fixture($reflection->getShortName());
    },
]);

// This will create a fixture called "GetForgeServerRequest"

$request = new GetForgeServerRequest(12345);
$response = $request->send($mockClient);

// This will create a fixture called "GetAllForgeServersRequest"

$request = new GetAllForgeServersRequest($data);
$response = $request->send($mockClient);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    '*' => function (SaloonRequest $request) {
        $reflection = new ReflectionClass($request);

        return MockResponse::fixture($reflection->getShortName());
    },
]);

// This will create a fixture called "GetForgeServerRequest"

$request = new GetForgeServerRequest(12345);
$response = $request->send();

// This will create a fixture called "GetAllForgeServersRequest"

$request = new GetAllForgeServersRequest($data);
$response = $request->send();
```

{% endtab %}
{% endtabs %}


# OAuth2 Authentication

Some API providers implement the OAuth 2 *Authorization Code Flow* for authentication. Implementing this grant type every time you create a new API integration can be tedious and time-consuming. Saloon offers a simple, extendable OAuth2 framework to help you get up and running quickly.

{% hint style="info" %}
Saloon has implemented the Authorization Code grant type since it is the most common OAuth2 flow. If you require any of the other grant types, please open a discussion on the GitHub repository.
{% endhint %}

### Overview

Saloon has provided methods for the full Authorization Code grant.

```php
$authConnector = new SpotifyAuthConnector;

// 1. Redirect the user to the authorization URL...

$authorizationUrl = $authConnector->getAuthorizationUrl($scopes, $state);

// 2. Handle the callback from the API provider and create an access token...

$authenticator = $authConnector->getAccessTokens($code, $state);

// 3. Authenticate your requests!

$request = new GetTracksRequest;
$request->authenticate($authenticator);
$request->send(); // 🚀

// 4. Refresh your access tokens...

$newAuthenticator = $authConnector->refreshAccessTokens($authenticator);
```

### Prerequisites

This section of the documentation assumes that you are familiar with OAuth2 and specifically the *Authorization Code Grant*.

This feature has been heavily inspired by the [“OAuth2 Client” package by “The PHP League”](https://github.com/thephpleague/oauth2-client) installed millions of times.

### Getting Setup

Let’s get started by preparing our Saloon connector ready to support the Authorization Code Flow. We recommend that you create a new connector in your integration just for authentication with the third-party provider. This can help keep the authentication and API code separate. Some providers may even have a separate OAuth2 server on a different subdomain to the API.

#### 1. Add the AuthorizationCodeGrant trait to your connector

{% hint style="warning" %}
We strongly recommend that you create a new connector just for the OAuth2 flow.
{% endhint %}

```php
<?php

use Sammyjo20\Saloon\Helpers\OAuth2\OAuthConfig;
use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyAuthConnector extends SaloonConnector
{
    use AuthorizationCodeGrant;
}
```

#### 2. Configure the base endpoint

After you have created the connector and added the trait, make sure to set the base endpoint to the URL of the OAuth2 server. For example:

```php
<?php

use Sammyjo20\Saloon\Helpers\OAuth2\OAuthConfig;
use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyAuthConnector extends SaloonConnector
{
    use AuthorizationCodeGrant;

    public function defineBaseUrl(): string
    {
        return 'https://accounts.spotify.com';
    }
}
```

#### 3. Configure the default OAuth config

Extend the **defaultOauthConfig()** method into your connector and start with setting your client ID, secret and redirect URI. You can also customise the various endpoints if the third party requires it.

```php
<?php

use Sammyjo20\Saloon\Helpers\OAuth2\OAuthConfig;
use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyAuthConnector extends SaloonConnector
{
    use AuthorizationCodeGrant;

    public function defineBaseUrl(): string
    {
        return 'https://accounts.spotify.com';
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId('my-client-id')
            ->setClientSecret('my-client-secret')
	    ->setDefaultScopes(['user-read-currently-playing'])
            ->setRedirectUri('https://my-app.saloon.dev/auth/callback')
	    ->setAuthorizeEndpoint('authorize')
            ->setTokenEndpoint('token')
            ->setUserEndpoint('user')
    }
}
```

#### Per-User/Tenant Config

If your OAuth2 config is dependent on a per-user/tenant basis, you can modify the config after you have instantiated the connector.

```php
<?php

$user = auth()->user(); // Your tenant/user.

$authConnector = new SpotifyAuthConnector;

// Overwrite the config just for this connector.

$authConnector->oauthConfig()->setClientId($user->spotify_client_id);
$authConnector->oauthConfig()->setClientSecret($user->spotify_client_secret);
```

{% hint style="warning" %}
Make sure to use the **oauthConfig()** method instead of accessing the property directly since it will not be set during instantiation of your class.
{% endhint %}

### Generating An Authorization URL

To generate an authorization URL, you can use the **getAuthorizationUrl()** method on the connector. Firstly instantiate the connector and then run the method. You can also provide scopes as well. It will also generate state for you if it has been provided.

```php
<?php

$authConnector = new SpotifyAuthConnector;

$scopes = ['user-library-read'];

$authorizationUrl = $authConnector->getAuthorizationUrl($scopes, $state);

// Redirect the user to the URL...
```

#### Optional State

To help prevent CSRF attacks, It’s highly recommended that you create a token in your authorization URL that you can confirm when the user redirects back to your application. This is known as state. Saloon will generate a 32-character alpha-numeric string for the state if you do not provide your own state.

You should generate the authorization URL first, then store the state securely. If you are using a framework like Laravel, you could store this state token in the user’s session.

```php
<?php

$authConnector = new SpotifyAuthConnector;

$state = 'secret';

$authorizationUrl = $authConnector->getAuthorizationUrl($scopes, $state);

// Get the state, secure it somewhere like the session.

$state = $authConnector->getState(); // 'secret'
```

### Creating Access Tokens

After the user has approved your application, the API provider will redirect you back to your application with an authorization code and state. This should be passed into your **getAccessToken()** method on your connector. If successful, the method will return an **AccessTokenAuthenticator**. This is a Saloon Authenticator that can be used to authenticate the rest of your requests.

```php
<?php

$authConnector = new SpotifyAuthConnector;

$authenticator = $authConnector->getAccessTokens($code);

// ... Use authenticator in your other requests.
```

{% hint style="info" %}
The method will return an **AccessTokenAuthenticator**. This is a Saloon Authenticator that can be used to authenticate the rest of your requests. [Click here to read more about using authenticators.](https://docs.saloon.dev/next-steps/authentication)
{% endhint %}

#### Verifying State

As mentioned above, if you stored the state that was generated during creating an authorization url, you should pass this expected state alongside the state sent back by the API provider's OAuth2 server.

```php
<?php

$authConnector = new SpotifyAuthConnector;

// It will throw an exception if the state and expected state don't match,
// but both must be present.

$authenticator = $authConnector->getAccessTokens($code, $state, $expectedState);
```

### Storing Authentication On Users

You will likely need to store the authenticator securely against a user, like in an encrypted field in the database. You may serialise and unserialise the authenticator using the helper methods below.

```php
<?php

$authConnector = new SpotifyAuthConnector;

$authenticator = $authConnector->getAccessTokens($code);

$serialized = $authenticator->serialize(); // Securely store this against your user.

$authenticator = AccessTokenAuthenticator::unserialize($serialized); // You can unserialize it too.
```

{% hint style="info" %}
If you are using Laravel Eloquent, you can use the **EncryptedOAuthAuthenticatorCast / OAuthAuthenticatorCast** casts to automatically cast the authenticator for storing into your database.
{% endhint %}

### Authenticate Your Requests

After you have created the access tokens above, you should have an **AccessTokenAuthenticator.** This class can be used to authenticate your other connectors/requests with the access token you have just received. Just use the **authenticate()** method on either your request or your connector.

```php
<?php

$authConnector = new SpotifyAuthConnector;

$authenticator = $authConnector->getAccessTokens($code);

$request = new CurrentSongRequest;
$request->authenticate($authenticator);
$response = $request->send();

// Or you can authorize the whole connector

$connector = new SpotifyApiConnector;
$connector->authenticate($authenticator);

// Make requests...
```

### Refreshing Access Tokens

Before using the authenticator, you should always check if the access token has expired and if it needs refreshing. When you need to refresh access tokens, you can call the **refreshAccessToken()** method which will create a fresh authenticator.

```php
<?php

$authenticator = $user->auth; // Your authenticator class.

// Check if the authenticator has expired, if it has - we can refresh
// the access token.

if ($authenticator->hasExpired() === true) {
    $authConnector = new SpotifyAuthConnector;
    $authenticator = $authConnector->refreshAccessToken($authenticator);
    
    $user->auth = $authenticator;
    $user->save();
}

// Continue to make your request...

$request = new CurrentSongRequest;
$request->authenticate($authenticator);
$response = $request->send();
```

### Customising The Authenticator

Sometimes the API provider you are authenticating with may require additional information to be used in the authenticator. You can customise how the authenticator will be created by extending the **createAccessTokenAuthenticator()** protected method. Make sure that you return a class that implements the **AccessTokenAuthenticatorInterface**.

```php
<?php

protected function createOAuthAuthenticator(string $accessToken, string $refreshToken, CarbonInterface $expiresAt): OAuthAuthenticatorInterface
{
    return new SpotifyAuthenticator($accessToken, $refreshToken, $expiresAt);
}
```

### Customising How The Authenticator Is Created

Sometimes the API provider you are authenticating with may have a different way that they respond with their tokens. If you need to customise the way Saloon creates the authenticator you can extend the **createOAuthAuthenticatorFromResponse()** method.

```php
protected function createOAuthAuthenticatorFromResponse(SaloonResponse $response, string $fallbackRefreshToken = null): OAuthAuthenticatorInterface
{
    $responseData = $response->object();

    $accessToken = $responseData->access_token;
    $refreshToken = $responseData->refresh_token ?? $fallbackRefreshToken;
    $expiresAt = CarbonImmutable::now()->addSeconds($responseData->expires_in);

    return $this->createOAuthAuthenticator($accessToken, $refreshToken, $expiresAt);
}
```

### Per User/Tenant OAuth Config

Sometimes you may have separate client credentials for every tenant or user that you need to authenticate the third party with. You can instantiate the connector and then change the configuration depending on your user.

```php
<?php

$user = auth()->user(); // Your tenant/user.

$authConnector = new SpotifyAuthConnector;

// Overwrite the config just for this connector.

$authConnector->oauthConfig()->setClientId($user->spotify_client_id);
$authConnector->oauthConfig()->setClientSecret($user->spotify_client_secret);

// Continue like normal...

$authorizationUrl = $authConnector->getAuthorizationUrl(...);
```

### Real-world example

If you would like to see an example integration using the OAuth2 methods mentioned above, see the following Laravel app.

<https://github.com/Sammyjo20/saloon-spotify-example>

#### Controller

<https://github.com/Sammyjo20/saloon-spotify-example/blob/main/app/Http/Controllers/SpotifyController.php>

#### Saloon Connector/Requests

<https://github.com/Sammyjo20/saloon-spotify-example/tree/main/app/Http/Integrations/Spotify>

### Available Methods / API

#### Connector

* **defaultOauthConfig()**

  Allows you to define the default configuration for the OAuth2 server that you are attempting to authenticate with.
* **oauthConfig()**

  Method that allows you to access and modify the OAuth2 configuration in the connector and after the connector has been created. This is useful if you need to set the credentials on a per-tenant basis.
* **getAuthorizationUrl($scopes, $state, $scopeSeparator)**

  This method will return the authorization URL that the user should be redirected to in order to authorize your application with the third-party provider.
* **getAccessToken($code, $state)**

  This method will make a request to create an access token from the authorization code that your application receives in the call-back URL.
* **refreshAccessToken($accessTokenAuthenticator|$refreshToken)**

  This method will make a request to refresh an access token. It will return an instance of AccessTokenAuthenticator which can be used to authenticate your connectors.
* **getState()**

  If state has not been provided in the **getAuthorizationUrl()** method, this method will return the randomly generated state that was sent to the provider. You should store this in a secure location and use it to verify the flow was not tampered with.
* **getUser($accessTokenAuthenticator)**

  This method can be used to retrieve the resource owner/user once you have successfully authenticated. **This may not work, depending on if the OAuth2 server/API provides it.**

#### OAuth2 Config Methods

* **getClientId / setClientId**
* **getClientSecret / setClientSecret**
* **getRedirectUri / setRedirectUri**
* **getAuthorizeEndpoint / setAuthorizeEndpoint**
* **getTokenEndpoint / setTokenEndpoint**
* **getUserEndpoint / setUserEndpoint**
* **getDefaultScopes / setDefaultScopes**
* **validate**


# Response Interceptors

Saloon already allows you to add functionality to your requests in the form of plugins, but if you would like to intercept the response before it is passed back to you, you can add a response interceptor. These can be added into Saloon plugins, or they can be added to the `boot` method on the Connector/Request.

### Example Response Interceptor

This response interceptor will tell all responses to "throw" an exception if it fails. Response interceptors provide you with the **SaloonRequest** and the request and the **SaloonResponse**.

```php
<?php

class CreateForgeServerRequest extends SaloonRequest
{
    //...

    public function boot(SaloonRequest $request): void
    {
        $this->addResponseInterceptor(function (SaloonRequest $request, SaloonResponse $response) {
            $response->throw();
    
            return $response;
        });
    }
}
```

{% hint style="info" %}
This interceptor example is a plugin pre-built into Saloon, have a look at the [**AlwaysThrowsOnErrors** plugin](https://docs.saloon.dev/next-steps/plugins#available-plugins)
{% endhint %}

### Macroable

Saloon Responses are also "Macroable" which means you can add your own methods to them to use later.

```php
<?php

class CreateForgeServerRequest extends SaloonRequest
{
    //...

    public function boot(SaloonRequest $request): void
    {
        $this->addResponseInterceptor(function (SaloonRequest $request, SaloonResponse $response) {
            $response::macro('hello', function ($name) {
                return 'Hello ' . $name;
            });
    
            return $response;
        });
    }
}
```

After we have defined the macro, we can use it like this.

```php
$response->hello('Sam'); // Returns "Hello Sam" 
```

### Custom Responses

If you are looking to overwrite Saloon's response methods or if you would like to add lots of your own methods, consider creating a custom response. [Read more](https://docs.saloon.dev/the-basics/responses#custom-responses).


# Handlers / Middleware

If you need to modify the underlying Guzzle request/response right before it is sent, you can use handlers. This is an incredibly useful feature that Guzzle provides to view/modify the request before it is sent.

To add a handler or middleware, simply use the `addHandler` method in your plugin or `boot` method on your connector/request.

[Click here to read more about Guzzle Handlers / Middleware](https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html)

### Example

```php
use Psr\Http\Message\RequestInterface;

class CreateForgeServerRequest extends SaloonRequest
{
    //...

    public function boot(SaloonRequest $request): void
    {
        $this->addHandler('customHeaderHandler', function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request->withHeader('X-Custom-Header', 'Hello');
                
                return $handler($request, $options);             
            };
        });
    }
}
```

{% hint style="info" %}
Saloon will not know about any extra headers, configuration or data you add inside of handlers.
{% endhint %}


# Showcase

Here are some SDKs and packages built with Saloon 🤠

{% embed url="<https://github.com/Astrotomic/ecologi-sdk>" %}

{% embed url="<https://github.com/Astrotomic/tmdb-sdk>" %}

{% embed url="<https://github.com/Astrotomic/steam-sdk>" %}

{% embed url="<https://github.com/MyOutDeskLLC/SalesforcePhp>" %}

{% embed url="<https://github.com/sawirricardo/midtrans-api>" %}

{% embed url="<https://github.com/SamuelMwangiW/africastalking-laravel>" %}

{% embed url="<https://github.com/SamuelMwangiW/linode>" %}

{% embed url="<https://github.com/kilianweisl/hellocash-php-sdk>" %}

{% embed url="<https://github.com/madebybob/carbone-sdk>" %}


# Tutorials

## API Integrations using Saloon in Laravel

Steve McDougall wrote a super in-depth tutorial for building a GitHub integration in Laravel using Saloon that enabled you to see a status of a repositories workflows. It's a great example of using Saloon and is good to follow if you would like to get started

<https://laravel-news.com/api-integrations-using-saloon-in-laravel>

### Podcast

Want to \*hear\* more about Saloon? The creator of Saloon, Sam Carré and Jake Bennet recorded an episode all about how Saloon works on the North Meets South podcast. Click on the link below to listen to the podcast episode.

#### [Listen on their website](https://www.northmeetssouth.audio/112)

#### [Listen on Apple Podcasts](https://podcasts.apple.com/us/podcast/north-meets-south-web-podcast/id1123508667?i=1000551046555)

#### [Listen on Spotify](https://open.spotify.com/episode/1oT4LC4bQSx2PNNMQDAkIc?si=b695129aa0d34327)


# Saloon

Build beautiful API integrations and SDKs with Saloon

{% hint style="info" %}
You are viewing the documentation for an **outdated version** of Saloon. If you would like to see the latest, [click here](https://app.gitbook.com/o/cASkBOXfspBu84TFkYqQ/s/AnnOFZ5KwPjnXI3FPOQw/).
{% endhint %}

<figure><img src="https://2652606861-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F29ap30WDt824nN7WNz2U%2Fuploads%2Fio9Djw5V2hWwYoWDdTWs%2Fheader.png?alt=media&#x26;token=244d6085-c6c9-4447-8372-1dde821ad277" alt=""><figcaption></figcaption></figure>

[![Build Status](https://github.com/saloonphp/saloon/actions/workflows/tests.yml/badge.svg)](https://img.shields.io/github/actions/workflow/status/saloonphp/saloon/tests.yml?label=tests)![](https://img.shields.io/packagist/dm/saloonphp/saloon)![](https://img.shields.io/github/stars/saloonphp/saloon)

### Introduction

Saloon is a PHP library that gives you the tools to build beautifully simple API integrations and SDKs. Saloon moves your API requests into reusable classes so you can keep all your API configurations in one place. It can be easily customised with plugins, but It comes pre-configured for you so you can get to sending API requests right away. Saloon comes with many exciting features out of the box like recording requests in your tests, caching, OAuth2 and pagination.

<figure><img src="https://2652606861-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F29ap30WDt824nN7WNz2U%2Fuploads%2Fv3r0kTuEBzGuAr5AoXa5%2Fcarbon.png?alt=media&#x26;token=c06b3ae4-0773-4e84-9597-f748a1445aea" alt=""><figcaption></figcaption></figure>

### Key Features

* Provides a simple, easy-to-learn, and modern way to build clean, reusable API integrations
* Built on top of Guzzle, the most popular and feature-rich HTTP client
* Works great within a team as it provides a standard everyone can follow
* Great for building your next PHP SDK or library
* Packed full of features like request recording, request concurrency, caching, data-transfer-object support, and full Laravel support.
* Framework agnostic
* Low on dependencies

{% hint style="info" %}
[Please consider giving Saloon a star on GitHub!](https://github.com/sammyjo20/saloon) (Over 1,000 stars already!)
{% endhint %}

{% content-ref url="getting-started/installation" %}
[installation](https://docs.saloon.dev/getting-started/installation)
{% endcontent-ref %}


# What's new in v2

Version two of Saloon is an upgrade in everything you already love in version one but refines and improves your developer experience. It has many internal changes that make it more maintainable and lightweight. This results in a fantastic experience building API integrations or your next SDK.

<figure><img src="https://2652606861-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F29ap30WDt824nN7WNz2U%2Fuploads%2FwFnDzvs1i3CF0F2s93Gc%2F%401x.png?alt=media&#x26;token=8e7da3af-022e-41c4-bbdd-a17c4e5766ab" alt=""><figcaption></figcaption></figure>

### Fundamental Changes

#### Namespace and class name changes

Previously, Saloon had "Sammyjo20" in the namespace. This has now been removed so namespaces start with "Saloon". Many of the classes in Saloon had the word "Saloon" in, which has also been dropped, for example, the request and collection.

* SaloonRequest -> Request
* SaloonConnector -> Connector

#### Reducing the dependency on Guzzle

Previously, Saloon was highly dependent on Guzzle as the underlying HTTP Client that sent requests. While this on its own is not inherently *bad* it meant that Saloon was very closely tied to Guzzle's versioning, support and breaking changes. Version two still requires Guzzle as its primary HTTP Client, but there have been many changes which decouple the library from Guzzle.&#x20;

Saloon used to send a request through a class called  `RequestSender.` this class would build up all the request properties like headers, config and body and would pass it into the Guzzle client. From version two, Saloon now has a new `PendingRequest` class that is responsible for building up all the request properties. After a PendingRequest has been made, it is sent to a `Sender.` This approach means that in theory you could use any HTTP client you like with Saloon, so if the maintainers of Guzzle decided to drop the project entirely, Saloon wouldn't be left in the dark.

While Saloon still uses Guzzle for it's very useful PSR-compliant objects, in future versions of Saloon, it may not use Guzzle as a dependency. The work has already been completed to allow you to use any HTTP Client of your choice.

<figure><img src="https://2652606861-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F29ap30WDt824nN7WNz2U%2Fuploads%2FTjWmXxWWVwRrREtME3Dm%2FSaloon%20v2.png?alt=media&#x26;token=bf60f8c2-60da-4d7b-a1a2-28096bc41611" alt=""><figcaption><p>The flow of Saloon v2</p></figcaption></figure>

#### Improving the developer experience

Saloon's purpose is to make it easy for developers to build and maintain API integrations. From simple integrations in a small PHP or Laravel project to building SDKs as separate packages for people to enjoy. It must be fun for a developer to use so there has been many changes made.

One of the notable changes is the simplification of the request and connector API. Previously, the request class had over 50 methods available and many of those methods didn't add much value. With version two, there are now \~28 methods (at the time of writing). Every feature of Saloon has been re-evaluated to decide if it adds value.

Continue to read to see more information on the rest of the changes made in version two.

{% tabs %}
{% tab title="Version 1" %}

```php
array:57 [
  0 => "defineEndpoint"
  1 => "__construct"
  2 => "boot"
  3 => "getMethod"
  4 => "getConnector"
  5 => "setConnector"
  6 => "getFullRequestUrl"
  7 => "traitExistsOnConnector"
  8 => "setIsRecordingFixture"
  9 => "isRecordingFixture"
  10 => "isNotRecordingFixture"
  11 => "__call"
  12 => "make"
  13 => "defaultData"
  14 => "mergeData"
  15 => "setData"
  16 => "addData"
  17 => "getData"
  18 => "ignoreDefaultData"
  19 => "defaultQuery"
  20 => "mergeQuery"
  21 => "setQuery"
  22 => "addQuery"
  23 => "getQuery"
  24 => "ignoreDefaultQuery"
  25 => "defaultHeaders"
  26 => "mergeHeaders"
  27 => "setHeaders"
  28 => "addHeader"
  29 => "getHeaders"
  30 => "getHeader"
  31 => "ignoreDefaultHeaders"
  32 => "defaultConfig"
  33 => "mergeConfig"
  34 => "setConfig"
  35 => "addConfig"
  36 => "getConfig"
  37 => "ignoreDefaultConfig"
  38 => "addHandler"
  39 => "mergeHandlers"
  40 => "getHandlers"
  41 => "addResponseInterceptor"
  42 => "mergeResponseInterceptors"
  43 => "getResponseInterceptors"
  44 => "defaultAuth"
  45 => "getAuthenticator"
  46 => "withAuth"
  47 => "authenticate"
  48 => "withTokenAuth"
  49 => "withBasicAuth"
  50 => "withDigestAuth"
  51 => "getResponseClass"
  52 => "send"
  53 => "sendAsync"
  54 => "getRequestManager"
  55 => "withMockClient"
  56 => "getMockClient"
]
```

{% endtab %}

{% tab title="Version 2" %}

```php
array:28 [
  0 => "__construct"
  1 => "createPendingRequest"
  2 => "sender"
  3 => "send"
  4 => "sendAsync"
  5 => "getMethod"
  6 => "getAuthenticator"
  7 => "authenticateWith"
  8 => "withTokenAuth"
  9 => "withBasicAuth"
  10 => "withDigestAuth"
  11 => "withQueryAuth"
  12 => "headers"
  13 => "queryParameters"
  14 => "config"
  15 => "middleware"
  16 => "createDtoFromResponse"
  17 => "getResponseClass"
  18 => "withMockClient"
  19 => "getMockClient"
  20 => "hasMockClient"
  21 => "when"
  22 => "unless"
  23 => "connector"
  24 => "setConnector"
  25 => "getRequestUrl"
  26 => "boot"
  27 => "make"
]
```

{% endtab %}
{% endtabs %}

#### Connector-first design

With previous versions of Saloon, the recommended way to send requests was with the request, and using the request `send` methods.

```php
$request = new UserRequest;
$response = $request->send();
```

One of the points developers found frustrating was defining a connector class on every request that you make. This was solely so you could make a request directly without instantiating the connector.

This approach was very minimalist, but it introduced complexity and friction for the developer.

From version two, the connector property is being dropped entirely from the request. This means that you must send your requests through the connector like this:

```php
$connector = new TwitterConnector;
$response = $connector->send(new UserRequest);

// or

TwitterConnector::make()->send(new UserRequest);
```

This allows you to have constructor arguments on the connector, perfect for API tokens or configuration. Similar to before, the request can have its own headers, config, query parameters and body but the connector will provide the very top-level defaults.

Although this is being taken out of the request, you may still add the functionality back with the `HasConnector` trait on the request. Although, if you add it back - you need to be aware of the downsides like not being able to have constructor arguments on your connector.

### New Features

#### Middleware Pipeline

Saloon now has its own middleware pipeline which lets you tap into requests before they are sent and responses before they are handed back to the application. This is really handy to add additional logic to all your requests or modify something before a request is sent to the sender. Middleware methods are chain-able and you can use invokable classes for them too.

This replaces the legacy response interceptors. You may still use Guzzle middleware but you must add them from within the connector's constructor. &#x20;

```php
<?php

$request = new GetForgeServerRequest(12345);

$request->middleware()
        ->onRequest(function (PendingRequest $pendingRequest) {
             // Run before the request is sent
        })
        ->onResponse(function (Response $response) {
             // Run after the request has been sent
        });
```

#### Senders

Senders are responsible for taking a PendingRequest and actually sending it. Currently the default sender is the GuzzleSender, but other senders can be built and you can specify the default sender on the connector.

```php
<?php

class ForgeConnnector extends Connector
{
    protected function defaultSender(): Sender
    {
        return new CustomSender;
    }
}
```

#### Request Concurrency & Pooling

Previously, Saloon would create a new Guzzle client for every request. This means that it didn't support concurrent requests as the curl connection was closed after every request. With Saloon v2, the sender remains active on the connector until the connector is destructed. This means that concurrent requests and pools are supported! You can use the connector's `pool` method to create a pool of requests. Pools accept arrays, generators or closures.

```php
<?php

$forge = new ForgeConnector;

$forge->pool()

$pool = $connector->pool([
    new GetForgeServersRequest,
    new GetForgeServersRequest,
    new GetForgeServersRequest,
]);

$pool->setConcurrency(10);

$pool->withResponseHandler(function (Response $response) {
    //
]);

$pool->withExceptionHandler(function (Exception $exception) {
    //
]);

$promise = $pool->send();
```

#### Better Interface Adoption

Saloon v2 now offers interfaces for all of the major classes so you can build your own implementation for unlimited customisation. Some of the interfaces include:

* Request
* Connector
* PendingRequest
* Sender
* Dispatcher
* Response

#### Better Way To Interact With Request, Headers, Query Parameters and Config

Another notable change would be the simplification of interacting with headers, config and request body. Instead of individual methods for interacting with these properties, they are now wrapped in easy-to-understand methods with unified method names. Additionally, previously you wouldn't be able to access the default properties after instantiating the request, but now you can.

{% tabs %}
{% tab title="Version 1" %}

```php
<?php

$request = new GetForgeServerRequest(12345);

$request->addHeader($value);
$request->getHeader($value);
$request->setHeaders($value);
$request->mergeHeaders(...$values);
$request->getHeaders();

$request->addQuery($value);
$request->getQuery(?$value);
$request->setQuery($value);
$request->mergeQuery(...$values);

$request->addConfig($value);
$request->getConfig(?$value);
$request->setConfig($value);
$request->mergeConfig(...$values);

$request->addData($value);
$request->getData(?$value);
$request->setData($value);
$request->mergeData(...$values);
```

{% endtab %}

{% tab title="Version 2" %}

```php
<?php

$request = new GetForgeServerRequest(12345);

$request->headers()->add($value);
$request->headers()->get($value, $default);
$request->headers()->set($value);
$request->headers()->merge(...$values);
$request->headers()->all();

$request->query()->add($value);
$request->query()->get($value, $default);
$request->query()->set($value);
$request->query()->merge(...$values);
$request->query()->all();

$request->config()->add($value);
$request->config()->get($value, $default);
$request->config()->set($value);
$request->config()->merge(...$values);
$request->config()->all();

// Data has been moved to body()... more on that below
```

{% endtab %}
{% endtabs %}

#### &#x20;Better Way To Interact With Request Body/Data

Additionally, sending request payload/body has been reworked. Previously the same `data` object was used for all types of data, which meant Saloon had to throw exceptions when you use certain methods, for example, if I tried to "add" when using a string body because you can't easily add to a string. Additionally, the `data()` methods were always available even if you didn't add a trait to activate them, which was confusing to the developer and could lead to annoying issues.

With version two, you can now add a trait based on the data type like before, but when you add the trait, it will add the `body()` method. You also add the `HasBody` interface so Saloon knows you intend to send body. The body method implements a contract called `BodyRepository` but depending on the trait added, it used a different implementation of BodyRepository to support the data you have requested. For example, if you add the `HasJsonBody` trait, it will use the `ArrayBodyRepository` which provides the additional methods like add/merge. However, if you use the `HasXmlBody` trait, it will use `StringBodyRepository` which only has a few methods.

This also means that the `defaultBody` method is implemented differently depending on the trait.

```php
<?php

class CreateForgeServerRequest extends Request implements HasBody
{
    use HasJsonBody;
    
    protected function defaultBody(): array
    {
        return [
            'name' => 'Default',
        ];
    }
}

$request = new CreateForgeServerRequest();

$request->body()->add('name', 'Server-One');
$request->body()->add('os', 'Ubuntu');

$request->body()->all();

// Result

[
    'name' => 'Server-One',
    'os' => 'Ubuntu',
]
```

#### Better Multipart Requests

With the changes to request body, Saloon has also made it easier to send multipart form requests for attaching files. Saloon makes this easy by adding the `HasBody` interface and `HasMultipartBody` trait. You can use the `add` method to attach a file to your request.&#x20;

```php
<?php

class UpdateUserRequest extends Request implements HasBody
{
    use HasMultipartBody;
}

$request = new CreateForgeServerRequest();

$request->body()->add(
    name: 'logo',
    contents: 'your-file-contents-or-stream', 
    filename: 'logo.png', 
    headers: [
       // Optional custom headers
    ]
);

$connector = new ForgeConnector;
$connector->send($request);
```

#### Even Better Laravel Support (HTTP Client)

If you are using Saloon in a Laravel environment, and have installed the Saloon-Laravel library, then version two will come with a different sender than the Guzzle sender that you can use. The HTTP Client sender uses Laravel's HTTP Client. Since this is built on top of Guzzle anyway, all request options will work, but all the typical HTTP Client events will be sent, which means other Laravel packages using these events will now work with Saloon. One great example is recording requests in Telescope.

#### Better Exception Handler

Saloon v2 also ships with a completely overhauled exception handler. You can customise when exceptions are thrown and what exceptions are thrown without creating custom response classes. There is also a brand new set of default exceptions that are thrown depending on status codes.

```
SaloonException
├── FatalRequestException (Connection Errors)
└── RequestException (Request Errors)
    ├── ServerException (5xx)
    │   ├── InternalServerErrorException (500)
    │   ├── ServiceUnavailableException (503)
    │   └── GatewayTimeoutException (504)
    └── ClientException (4xx)
        ├── UnauthorizedException (401)
        ├── ForbiddenException (403)
        ├── NotFoundException (404)
        ├── MethodNotAllowedException (405)
        ├── RequestTimeOutException (408)
        ├── UnprocessableEntityException (422)
        └── TooManyRequestsException (429)
```

#### Solo Requests

Version two will also introduce a new `SoloRequest` class which will be perfect for making just one request for API integration. With SoloRequests, you don't need a connector at all - you can define everything in the request and send it above like you used to do.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

class GetPokemonRequest extends SoloRequest
{
     protected Method $method = Method::GET;
     
     public function resolveEndpoint()
     {
          return 'https://pokeapi.co/api/v2/pokemon';
     }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

// No Connector Needed!

$request = new GetPokemonRequest;
$response = $request->send();
```

{% endtab %}
{% endtabs %}

#### Pagination

Saloon v2 has also introduced pagination helpers to make it super easy to iterate through hundreds of pages of results without having to write your own boilerplate code.

```php
<?php

$connector = new SpotifyConnector;

// Create a paginator and pass in a request class, in this example
// we'll pass in the LikedSongsRequest which will retrieve all
// the liked songs of the authenticated user.

$paginator = $connector->paginate(new LikedSongsRequest);

// Create a Laravel LazyCollection from the paginator and iterate
// over each of the results. Traditionally, the liked songs endpoint
// only lets you get 50 tracks per request, but the paginator will
// automatically grab every page of results and pass it into a 
// single collection! 🔥

$collection = $paginator->collect('items')->map(function ($track) {
    return sprintf('%s - %s', $track['artist'], $track['name']);
});

// Convert the LazyCollection into an array.

$data = $collection->all();
```

### Other Improvements / Changes

#### Tidier Codebase

Like with every library, new methods of programming is learned and better patterns are implemented. Saloon version two is pretty much a rewrite and has a much easier-to-understand, tidier codebase.

#### Reduced Dependencies

Saloon previously depended on Laravel's Illuminate Support package and Carbon. These dependencies have now been completely removed which reduce's Saloon's dependency tree and makes it much more lightweight, especially with the removal of the Illuminate Support package as that contains many Laravel-specific class which would be installed in every simple PHP library or SDK.

Saloon now only has three dependencies

* Guzzle (For The Default GuzzleSender)
* Guzzle's Promise Library (For Request Pooling)
* PSR Message Library (For PSR Interfaces)

#### New Query Authenticator

With Saloon v2, a new query parameter authenticator has added, to support some APIs where their authentication is provided through a query parameter.

#### Request Collections & Magic Methods Removed

Previously, Saloon had request collections which can be added to connectors alongside a `$requests` array which allowed you to define custom properties on the connector. These properties would then link directly to a request or a request collection. Saloon v2 has removed this feature in order to have less "magical" code going on. It was also difficult for IDEs to understand.


# Upgrading from v1

### Introduction

It's time for the big upgrade to version two! There have been several major changes to Saloon in version two, so it's recommended that you read through [what's new in v2](https://docs.saloon.dev/upgrade/whats-new-in-v2) before starting the upgrade. You'll definitely need a cup of tea, coffee or beer if you fancy.

#### Estimated Upgrade Time

Simple integrations: 15-30 minutes

Advanced integrations and SDKs: \~30-45 minutes

### Need help migrating from version one?

You can [open a discussion](https://github.com/sammyjo20/saloon) on Saloon's Github page if you need any help with the migration process.

### Installation

First, update Saloon in your `composer.json` file to use the version `^2.0`. if you are using the additional Laravel package, you should update this to `^2.0` too. After that, run `composer update`.

Additionally, Saloon has moved to it's own Github organization so the name has been changed from `sammyjo20` to `saloonphp` and the Laravel package has been renamed from `sammyjo20/saloon-laravel` to `saloonphp/laravel-plugin.`

{% tabs %}
{% tab title="Non-Laravel" %}

```json
"require": {
    "saloonphp/saloon": "^2.0"
}
```

{% endtab %}

{% tab title="Laravel" %}

```json
"require": {
    "saloonphp/saloon": "^2.0",
    "saloonphp/laravel-plugin": "^2.0"
}
```

> If you previously used `sammyjo20/saloon-laravel` or `saloonphp/laravel-plugin` it's recommended that you add `saloonphp/saloon` as an additional required dependency.
> {% endtab %}
> {% endtabs %}

{% hint style="danger" %}
Version two requires PHP 8.1 and Laravel 9 if you are using the additional Laravel helper package.
{% endhint %}

### Namespace Changes

<mark style="color:red;">Estimated Impact: High</mark>

All of Saloon’s classes have a new namespace `Saloon` instead of `Sammyjo20\Saloon`. This change will affect every use of Saloon’s internal classes, so it’s recommended to run a find-and-replace.

* Find: `use Sammyjo20\Saloon`
* Replace: `use Saloon`

#### Laravel Namespace Changes

<mark style="color:red;">Estimated Impact: High</mark>

If you are using the Laravel helpers package for Saloon, you should also change the namespaces.

* Find: `use Sammyjo20\SaloonLaravel`
* Replace: `use Saloon\Laravel`

### Class Name Changes

<mark style="color:red;">Estimated Impact: High</mark>

To help make Saloon more readable and to improve the developer experience, Saloon’s classes have changed names. There were a number of classes that had the name `Saloon` within, like `SaloonRequest` and `SaloonConnector.` you should find and replace these too. Please make sure that you have renamed the namespaces as instructed above first.

#### Connector

* Find: `use Saloon\Http\SaloonConnector`
* Replace: `use Saloon\Http\Connector`
* Find: `extends SaloonConnector`
* Replace: `extends Connector`

#### Request

* Find: `use Saloon\Http\SaloonRequest`
* Replace: `use Saloon\Http\Request`
* Find: `extends SaloonRequest`
* Replace: `extends Request`

#### MockClient

* Find: `use Saloon\Clients\MockClient`
* Replace: `use Saloon\Http\Faking\MockClient`

#### MockResponse

* Find: `use Saloon\Http\MockResponse`
* Replace: `use Saloon\Http\Faking\MockResponse`

#### Response (if using custom responses)

* Find `use Saloon\Http\SaloonResponse`
* Replace: `use Saloon\Http\Response`
* Find: `extends SaloonResponse`
* Replace: `extends Response`

### Connector Changes

<mark style="color:red;">Estimated Impact: High</mark>

Saloon has also had a major refactor with the methods that are used to build and interact with connectors and requests. You should carefully find and replace the given strings.

#### Connector Methods

The connector’s base URL method has changed.

* Find: `public function defineBaseUrl(): string`
* Replace: `public function resolveBaseUrl(): string`

The method to define a custom response has changed.

* Find: `public function getResponseClass(): string`
* Replace: `public function resolveResponseClass(): string`

The `request` method has been removed.

The `__call` and `__callStatic` methods have been removed alongside the magic methods that build up requests. Including the `requests` property.

### Request Changes

<mark style="color:red;">Estimated Impact: High</mark>

#### Request Methods

The request’s `defineEndpoint` method has changed.

* Find: `public function defineEndpoint(): string`
* Replace: `public function resolveEndpoint(): string`

The method to define a custom response has changed.

* Find: `public function getResponseClass(): string`
* Replace: `public function resolveResponseClass(): string`

The `send`, `sendAsync`, `getConnector` and `setConnector` methods and the `connector` property has been removed from the request. Please see below to add connector support back to your request if you need it

The `getFullRequestUrl` method has been removed from the request. You can get the URL of the request with the `PendingRequest` inside of the boot method, traits and middleware.

The `__call` method has been removed from the request. Any methods that no longer exist on the request will not be proxied to the connector.

The `traitExistsOnConnector` method has been removed from the request.

#### Request Properties

The request’s method property has changed to use a new `Saloon\Enums\Method` Enum. Make sure to migrate to use the new Enum too. For example: `protected Method $method = Method::GET`.

* Find: `protected ?string $method`
* Replace: `protected Method $method`

### Updated way to send requests

<mark style="color:red;">Estimated Impact: High</mark>

With previous versions of Saloon, the recommended way to send requests was with the request and using the request `send` methods.

```php
$request = new UserRequest;
$response = $request->send();
```

One of the points developers found frustrating was defining a connector class on every request that you make. This was solely so you could make a request directly without instantiating the connector.

This approach was very minimalist, but it introduced complexity and friction for the developer.

From version two, the connector property is being dropped entirely from the request. This means that you must send your requests through the connector like this:

```php
$connector = new TwitterConnector;
$response = $connector->send(new UserRequest);

// or

TwitterConnector::make()->send(new UserRequest);
```

This allows you to have constructor arguments on the connector, perfect for API tokens or configuration. Similar to before, the request can have its own headers, config, query parameters and body but the connector will provide the very top-level defaults.

You should make sure that your requests use this new way of sending requests.

#### Using Request-First Sending

Although this is being taken out of the request, you may still add the functionality back with the `HasConnector` trait on the request. However, if you add it back - you need to be aware of the downsides like not being able to have constructor arguments on your connector.

### Updated Request, Headers, Query Parameters and Config Methods

<mark style="color:red;">Estimated Impact: High</mark>

Another notable change would be the simplification of interacting with headers, config and request body. Instead of individual methods for interacting with these properties, they are now wrapped in easy-to-understand methods with unified method names. Additionally, previously you wouldn't be able to access the default properties after instantiating the request, but now you can. You should make sure any references to headers, query parameters or config use the new methods.

{% tabs %}
{% tab title="Version 1" %}

```php
<?php

$request = new GetForgeServerRequest(12345);

$request->addHeader($value);
$request->getHeader($value);
$request->setHeaders($value);
$request->mergeHeaders(...$values);
$request->getHeaders();

$request->addQuery($value);
$request->getQuery(?$value);
$request->setQuery($value);
$request->mergeQuery(...$values);

$request->addConfig($value);
$request->getConfig(?$value);
$request->setConfig($value);
$request->mergeConfig(...$values);

$request->addData($value);
$request->getData(?$value);
$request->setData($value);
$request->mergeData(...$values);
```

{% endtab %}

{% tab title="Version 2" %}

```php
<?php

$request = new GetForgeServerRequest(12345);

$request->headers()->add($value);
$request->headers()->get($value, $default);
$request->headers()->set($value);
$request->headers()->merge(...$values);
$request->headers()->all();

$request->query()->add($value);
$request->query()->get($value, $default);
$request->query()->set($value);
$request->query()->merge(...$values);
$request->query()->all();

$request->config()->add($value);
$request->config()->get($value, $default);
$request->config()->set($value);
$request->config()->merge(...$values);
$request->config()->all();

// Data has been moved to body()... more on that below
```

{% endtab %}
{% endtabs %}

### Migrating to the new request body API

<mark style="color:red;">Estimated Impact: High</mark>

Saloon has also rebuilt the way that request data/body is sent using POST/PUT/PATCH requests. First, make sure that your data traits are using the new namespaces. It's recommended that you [read through the new section](https://docs.saloon.dev/the-basics/request-body-data) on request body/data to understand why the changes have been made.

**HasJsonBody**

* Find: `use Saloon\Traits\Plugins\HasJsonBody`
* Replace: `Saloon\Traits\Body\HasJsonBody`

**HasFormParams**

* Find: `use Saloon\Traits\Plugins\HasFormParams`
* Replace: `use Saloon\Traits\Body\HasFormBody`

**HasMultipartBody**

* Find: `use Saloon\Traits\Plugins\HasMultipartBody`
* Replace: `use Saloon\Traits\Body\HasMultipartBody`

**HasXMLBody**

* Find: `use Saloon\Traits\Plugins\HasXMLBody`
* Replace: `use Saloon\Traits\Body\HasXmlBody`
* Replace: `use HasXMLBody`
* Find: `use HasXmlBody`

**HasBody**

* Find: `use Saloon\Traits\Plugins\HasBody`
* Replace: `use Saloon\Traits\Body\HasBody`

Next, add the `HasBody` interface to your request or connector. This interface is required for Saloon to properly detect if you are using request body or not. Since you have already added a body trait, the required `body` method should be implemented. You may need to re-index your IDE before it understands the changes made.

#### **Syntax changed from data to body**

Previously, Saloon called request body "data". To match PSR standards better, this has been renamed to "body".

#### Changing default

Previously, you may have defined a method like `defaultData` this needs to be renamed to `defaultBody`. The methods have also changed from being public to protected.

{% tabs %}
{% tab title="Version One" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

class GetServersRequest extends Request
{
    // {...}
    
<strong>    public function defaultData(): array    
</strong>    {
        return [
            // ...
        ];
    }
}
</code></pre>

{% endtab %}

{% tab title="Version Two" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

class GetServersRequest extends Request
{
    // {...}
    
<strong>    public function defaultBody(): array
</strong>    {
        return [
            // ...
        ];
    }
}
</code></pre>

{% endtab %}
{% endtabs %}

{% hint style="info" %}
Make sure that you define the correct return type for your request body trait.
{% endhint %}

#### HasXMLBody defineXmlBody removed

Saloon has removed the `defineXmlBody` method when you use the `HasXmlTrait`. You must replace this with `defaultBody`

#### Request Body Methods

Saloon has also changed the previous request data methods. You should update your code accordingly if you used these old methods.

{% tabs %}
{% tab title="Version One" %}

```php
<?php

$request = new CreateForgeSiteRequest($serverId, $domain);

$request->setData(['domain' => $customDomain]);
$request->mergeData(['database' => 'test123']);
$request->addData('name', 'my-saloon-server');
$request->getData('name');
```

{% endtab %}

{% tab title="Version Two" %}

```php
<?php

$request = new CreateForgeSiteRequest($serverId, $domain);

$request->body()->set(['domain' => $customDomain]);
$request->body()->merge(['database' => 'test123']);
$request->body()->add('name', 'my-saloon-server');
$request->body()->get('name');
```

{% endtab %}
{% endtabs %}

### Removing Connector Magic Properties & Request Collections

<mark style="color:red;">Estimated Impact: High</mark>

With regards to request collections/request groups, Saloon has removed support for them entirely in v2. Previously, Saloon had a lot of "magic" logic which was cool, but tricky for IDEs to support. As request collections were just classes that passed in the connector, it's recommended that you create your own classes that support this, and then add methods into your connector.

[Click here for an example SDK resource instead of request collections](https://docs.saloon.dev/the-basics/building-sdks#resources)

### Authentication

<mark style="color:red;">Estimated Impact: High</mark>

Saloon version two has removed the `withAuth` method. You should use the `authenticate` method instead.

### Response Interceptors

<mark style="color:red;">Estimated Impact: High</mark>

Previously, Saloon had the concept of `ResponseInterceptors` which were functions that Saloon would call before returning the response back to the application. This API has been removed in favour of using the new [Middleware API](https://docs.saloon.dev/digging-deeper/middleware). It's recommended that you get yourself familiar with middleware, but here is an example of migrating from response interceptors to response middleware.

{% tabs %}
{% tab title="Version One" %}

```php
<?php

$request->addResponseInterceptor(function (SaloonRequest $request, SaloonResponse $response) {
    $response->throw();
    
    return $response;
});
```

{% endtab %}

{% tab title="Version Two" %}

```php
<?php

use Saloon\Contracts\Response;

$request->middleware()->onResponse(function (Response $response) {
    $response->throw();
});
```

{% endtab %}
{% endtabs %}

### AlwaysThrowOnErrors Trait Rename

<mark style="color:red;">Estimated Impact: High</mark>

From Saloon version two, the `AlwaysThrowsOnErrors` trait has been renamed to `AlwaysThrowOnErrors`.

* Find: `Saloon\Traits\Plugins\AlwaysThrowsOnErrors`
* Replace: `Saloon\Traits\Plugins\AlwaysThrowOnErrors`
* Find: `use AlwaysThrowsOnErrors`
* Replace: `use AlwaysThrowOnErrors`

### Boot Method Arguments

<mark style="color:red;">Estimated Impact: High</mark>

Saloon has a method that you can add on your connector and request to write logic while a request is being sent. This `boot` method has changed arguments in version two. It used to provide you with an instance of `Request` but will now provide you with an instance of `PendingRequst.` You should ensure any modifications are made on this PendingRequest instance and not use `$this` or modify the connector/request.

{% tabs %}
{% tab title="Version One" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

class CreateForgeServerRequest extends SaloonRequest
{
    // {...}

<strong>    public function boot(SaloonRequest $request): void
</strong>    {
        $request->addHeader('X-Example', 'Hello');
    }
}
</code></pre>

{% endtab %}

{% tab title="Version Two" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Contracts\PendingRequest;

class CreateForgeServerRequest extends Request
{
    // {...}

<strong>    public function boot(PendingRequest $pendingRequest): void
</strong>    {
        $pendingRequest->headers()->add('X-Example', 'Hello');
    }
}
</code></pre>

{% endtab %}
{% endtabs %}

### Caching Plugin

<mark style="color:red;">Estimated Impact: High</mark>

Saloon's caching plugin has also had a full overhaul to work with Saloon v2. It's recommended that you follow the steps for configuring the new caching plugin in the [documentation here.](https://docs.saloon.dev/plugins/caching-responses)

### Data Transfer Objects

<mark style="color:red;">Estimated Impact: High</mark>

Previously, Saloon provided data transfer objects through a `CastsToDto` trait. This trait could be added to the connector or request and would allow you to define a `castToDto` method. From Saloon v2, DTO casting is a built-in feature for every connector and request and the `CastsToDto` trait has been removed. All you need to do is change the **protected**  `castToDto` method to a **public** `createDtoFromResponse` method.

{% tabs %}
{% tab title="Version One" %}

```php
<?php

use Sammyjo20\Saloon\Traits\Plugins\CastsToDto;

class ForgeConnector extends SaloonConenctor
{
    use CastsToDto;
    
    protected function castToDto(SaloonResponse $response): mixed
    {
        // ...
    }
}
```

{% endtab %}

{% tab title="Version Two" %}

```php
<?php

class ForgeConnector extends SaloonConenctor
{
    protected function createDtoFromResponse(SaloonResponse $response): mixed
    {
        // ...
    }
}
```

{% endtab %}
{% endtabs %}

### Responses

<mark style="color:purple;">Estimated Impact: Medium</mark>

Saloon’s `Response` class has changed to be a more generic, PSR-compatible response. If you are extending the existing Response class, you should make sure that it is still working correctly.

### Plugin Traits

<mark style="color:purple;">Estimated Impact: Medium</mark>

From version two, Saloon has updated its plugins. You can choose to add plugins to both your connector or your request. Previously, plugins would receive an instance of `SaloonRequest` in the arguments. Now, plugins will receive a `PendingRequest` instance. You should update your plugins accordingly.

You should also make any changes to the `PendingRequest` instance and **not** use `$this` as it's bad practice to overwrite the connector/request instance.

### Guzzle Handlers/Middleware

<mark style="color:purple;">Estimated Impact: Medium</mark>

Previously, Saloon allowed you to use the `addHandler` method to use a Guzzle middleware. From version two, Guzzle middleware is still supported with the default GuzzleSender, but you must migrate your handlers to the new API.

It's also recommended that you move any Guzzle middleware from requests into your connector class as middleware should only be executed once.

{% tabs %}
{% tab title="Version One" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

class Forge extends SaloonConnector
{
    //...

    public function boot(SaloonRequest $request): void
    {
<strong>        $this->addHandler('customHeaderHandler', function (callable $handler) {
</strong>            return function (RequestInterface $request, array $options) use ($handler) {
                $request->withHeader('X-Custom-Header', 'Hello');
                
                return $handler($request, $options);             
            };
        });
    }
}
</code></pre>

{% endtab %}

{% tab title="Version Two" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

class Forge extends Connector
{
    //...

    public function boot(PendingRequest $pendingRequest): void
    {
<strong>        $pendingRequest->sender()->addMiddleware(function (callable $handler) {
</strong>            return function (RequestInterface $request, array $options) use ($handler) {
                $request->withHeader('X-Custom-Header', 'Hello');
                
                return $handler($request, $options);             
            };
<strong>        }, 'customHandlerMiddleware');
</strong>    }
}
</code></pre>

{% endtab %}
{% endtabs %}

### Authenticator Traits

<mark style="color:blue;">Estimated Impact: Low</mark>

Previously, Saloon had five traits which would throw an exception if a request or connector wasn’t authenticated. The following traits have now been removed:

* RequiresBasicAuth
* RequiresDigestAuth
* RequiresTokenAuth

You should now use the generic `RequiresAuth` trait if you would still like to throw an exception.

### OAuth Carbon Removal

<mark style="color:blue;">Estimated Impact: Low</mark>

Saloon no longer has Carbon as a dependency, so all dates returned that used to return a `CarbonInterface` now return `DateTimeImmutable`

* OAuthAuthenticator: `getExpiresAt()`
* AccessTokenAuthenticator: `getExpiresAt()`

### Mock Response From Request

<mark style="color:blue;">Estimated Impact: Low</mark>

The `MockResponse::fromRequest` method has been removed from version two.


# Installation

To get started with Saloon, you will need to install it through Composer.&#x20;

```bash
composer require saloonphp/saloon "^2.0"
```

> Saloon supports PHP 8.1+

### Dependencies

Saloon has just three dependencies.

* [Guzzle](https://github.com/guzzle/guzzle) (For The Default GuzzleSender)
* [Guzzle's Promise Library](https://github.com/guzzle/promises) (For Request Pooling & Asynchronous Requests)
* [PSR Message Library](https://github.com/php-fig/http-message) (For PSR-7 Interfaces)

### Using Laravel?

There is an additional Laravel Plugin you can install for Saloon which provides useful testing helpers, a Saloon facade and Artisan commands! To read more about the Laravel plugin [click here](https://docs.saloon.dev/plugins/laravel-integration)


# Connectors

Connectors are classes that hold the basic requirements of an API integration. Connectors communicate with the HTTP client (Sender). A connector expects a Base URL to be defined, but you can also register defaults that would be shared with all your requests, like headers or HTTP client config.

### Are you building an integration for just one request?

Saloon connectors are great for most API integrations; however, you may not need a connector if you make an API integration with only one request. If this is your scenario, [read through the "Solo Request" section.](https://docs.saloon.dev/digging-deeper/solo-requests) Creating a solo request means you don't need to create a connector.

### Getting Started

First, create a directory for your API integrations. Once you have a chosen directory, create a class that extends the `Connector` abstract class. After that, extend the `resolveBaseUrl` function.

See the example connector for Laravel Forge, an API for server management. We'll name it `Forge` for the best readability.

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }
}
```

{% hint style="info" %}
Using the Laravel Saloon Plugin? Use the following Artisan command to create a connector!

**php artisan saloon:connector \<Integration Name> \<Connector Name>**
{% endhint %}

### Default Headers and Query Parameters

Most API integrations will have headers that should be shared with every request, like the `Content-Type` or the `Accept` headers. Some API integrations may even have default query parameters to be applied to every request. Saloon allows you to define default properties like these easily.

To add default headers, you can extend the `defaultHeaders` method to your connector. This method expects a keyed array to be returned. You may use an array in the value of a header for multiple header values.

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
```

You may also add a `defaultQuery` method to your connector to specify default query parameters for every request. This method expects a keyed array to be returned.

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    protected function defaultQuery(): array
    {
        return [
            'per_page' => 500, // ?per_page=500
        ];
    }
}
```

### Default HTTP Client Configuration

You may want to define custom options to send to the HTTP client. For example, you may want to register a default timeout of 60 seconds for every request. Saloon uses Guzzle as the default HTTP client, so that you may use any of Guzzle’s options inside the `defaultConfig` method. This method expects a keyed array to be returned.

[Click here to see a list of the available options Guzzle provide.](https://docs.guzzlephp.org/en/stable/request-options.html)

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    public function defaultConfig(): array
    {
        return [
            'timeout' => 60,
        ];
    }
}
```

### Using Constructor Arguments

You may add properties to your connector class and use a constructor to provide variables into the connector instance, like an API token. This is great when building SDK-style classes.

{% tabs %}
{% tab title="Definition" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

<strong>    public function __construct(
</strong><strong>        protected string $apiToken,
</strong><strong>    ){
</strong><strong>       $this->withTokenAuth($this->apiToken); 
</strong><strong>    }
</strong>}
</code></pre>

{% endtab %}

{% tab title="Usage" %}

```php
$forge = new ForgeConnector('api-token');
```

{% endtab %}
{% endtabs %}

{% hint style="info" %}
This example uses a method `withTokenAuth` which is documented on the [authentication ](https://docs.saloon.dev/the-basics/authentication)page.
{% endhint %}


# Requests

The Saloon request class stores the information of a single API request. Within a request, you can set the HTTP Method (GET, POST, etc.) and define the endpoint of that request. You don't have to include the base URL because your connector will provide it for you. You can also define headers, query parameters and HTTP client config. Saloon request classes are reusable, so you can write a request class once and use it multiple times in your application.

### Getting Started

Create a class that is in a similar place to your connector. The class should extend the `Request` abstract class. After that, overwrite the `method` property and set the HTTP method your request needs. You should import the `Saloon\Enums\Method` enum class.

* `protected Method $method = Method::GET;`

After that, extend the `resolveEndpoint` public method. This method should contain the endpoint of the request. You may wish to leave this string blank if you do not have a specific endpoint, like when consuming GraphQL APIs. The endpoint will be combined with the base URL defined within your connector.

See the example request. This request will GET all of the servers from a Laravel Forge account.

{% hint style="info" %}
Using the Laravel Saloon Plugin? Use the following Artisan command to create a request!

**php artisan saloon:request \<Integration Name> \<Request Name>**
{% endhint %}

```php
<?php

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetServersRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/servers';
    }
}
```

{% hint style="info" %}
Typically, you should let Saloon combine the Base URL in your connector with the endpoint in your request, but you can also provide fully qualified URLs in requests which overwrite the base URL if you need to.
{% endhint %}

```php
public function resolveEndpoint(): string
{
    return 'https://some-other-domain.com/endpoint';
}
```

### Using Constructor Arguments

You may add properties to your request class and use a constructor to provide variables into the request instance. Since the request is still a regular class, you may customise it how you like.

For example, If I want to create a request to retrieve an individual server by an ID. I could add a constructor to accept the server ID and use the variable within the endpoint method. This way, I can pass the ID into every request instance.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetServersRequest extends Request
{
    protected Method $method = Method::GET;
    
    public function resolveEndpoint(): string
    {
        return '/servers/' . $this->id;
    }

    public function __construct(
        protected int $id,
    ) { }
} 
```

{% endtab %}

{% tab title="Usage" %}

```php
$request = new GetServerRequest(id: 12345);
```

{% endtab %}
{% endtabs %}

### Default Headers and Query Parameters

Some requests require specific headers or query parameters to be sent. To define default headers on your request, you can extend the `defaultHeaders` method. This method expects a keyed array to be returned. You may use an array in the value of a header for multiple header values. These headers will be merged with the connector’s headers.

```php
<?php

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetServersRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/servers';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
```

You may also add a `defaultQuery` method to your request to specify default query parameters. This method expects a keyed array to be returned.

```php
<?php

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetServersRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/servers';
    }
    
    protected function defaultQuery(): array
    {
        return [
            'per_page' => 500, // ?per_page=500,
            'page' => 1, // &page=1
        ];
    }
}
```

### Default HTTP Client Config

You may want to define custom options to send to the HTTP Client when creating a request. For example, you may want to register a default timeout of 120 seconds on the request. Saloon uses Guzzle as the default HTTP client, so you may use any of Guzzle’s options inside the`defaultConfig` method. This method expects a keyed array to be returned. The configuration options will be merged with the connector’s config.

[Click here to see a list of the available options Guzzle provide.](https://docs.guzzlephp.org/en/stable/request-options.html)

```php
<?php

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetServersRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/servers';
    }
    
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 120,
        ];
    }
}
```


# Headers

Headers can be added by using the `headers()` method on either the connector or the request. When you add headers to a connector instance, every request sent through that connector will merge those headers with the request. When you add headers to a request instance, it will just be added to that one request instance.

### Default Headers

You may configure default headers on the connector or request using the protected `defaultHeaders` method.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    // {...}
    
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json'
        ];
    }
}
```

{% hint style="info" %}
Default headers on a connector will be applied to every request. This is handy for providing specific headers like Content-Type or Accept.
{% endhint %}
{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json'
        ];
    }
}
```

{% endtab %}
{% endtabs %}

#### Using Properties With Default Headers

You may also use properties in requests to populate headers, for example populating a username custom header.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
    protected string $username;
    
    public function __construct(string $username)
    {
        $this->username = $username;
    }
    
    protected function defaultHeaders(): array
    {
        return [
            'X-Username' => $this->username,
        ];
    }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$request = new GetServersRequest('Sammyjo20');

// X-Username: Sammyjo20
```

{% endtab %}
{% endtabs %}

### Using the Headers Method

Saloon also offers a handy headers API to manage your headers easily after a request instance has been created. Use the `headers()` method on your request to manage them. Headers added to the request are prioritised more than the connector's headers. This is useful for changing the headers before a request is sent.

```php
<?php

$request = new GetServersRequest();

$request->headers()->add('Content-Type', 'application/json');

$all = $request->headers()->all();

// Content-Type: application/json
```

{% hint style="warning" %}
When you have default headers to a connector, they won't be visible to the request instance as they are merged later in the request lifecycle. Still, request headers will have a higher priority than connector headers.
{% endhint %}

### Available Methods

#### set(array $items)

Overwrite the headers on the request with a new array.

#### merge(...$items)

Merge arrays of headers.

#### remove(string $key)

Remove a given header by its key.

#### get(string $key, mixed $default = null)

Get a given header by its key or return the default.

#### all()

Retrieve all headers as an array.

#### isEmpty

Check if the header object is empty.

{% hint style="info" %}
Click here to view the API reference for this method.
{% endhint %}


# Query Parameters

Like headers, query parameters can be added by using the `query()` method on either the connector or the request. When you add query parameters to a connector instance, every request sent through that connector will merge those parameters with the request. When you add query parameters to a request instance, it will just be added to that one request instance.

### Default Query Parameters

You may configure default query parameters on the connector or request using the protected `defaultQuery` method.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    // {...}
    
    protected function defaultQuery(): array
    {
        return [
            'per_page' => 50,
        ];
    }
}
```

{% hint style="info" %}
Default query parameters on a connector will be applied to every request that uses the connector.
{% endhint %}
{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
    protected function defaultQuery(): array
    {
        return [
            'per_page' => 50,
        ];
    }
}
```

{% endtab %}
{% endtabs %}

#### Using Properties With Default Query Parameters

You may also use properties in requests to populate query parameters, for example - requesting a specific page on a paginated API.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
    protected int $perPage;
    
    protected int $page;
    
    public function __construct(int $perPage, int $page)
    {
        $this->perPage = $perPage;
        $this->page = $page;
    }
    
    protected function defaultQuery(): array
    {
        return [
            'per_page' => $this->perPage,
            'page' => $this->page,
        ];
    }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$request = new GetServersRequest(perPage: 50, page: 5);
```

{% endtab %}
{% endtabs %}

### Using the query Method

Saloon also offers a handy query parameters API to manage them easily after a request instance has been created. Use the `query()` method on your request to manage them. Query parameters added to the request are prioritised more than the connector's query parameters. This is useful for changing them before a request is sent.

```php
<?php

$request = new GetServersRequest();

$request->query()->add('page', 5);

$all = $request->query()->all();

// array: ['page' => 5]
```

{% hint style="warning" %}
When you have default query parameters on a connector, they won't be visible to the request instance as they are merged later in the request lifecycle. Still, request query parameters will have a higher priority than connector query parameters.
{% endhint %}

### Available Methods

#### set(array $items)

Overwrite the query parameters on the request with a new array.

#### merge(...$items)

Merge arrays of query parameters.

#### remove(string $key)

Remove a given query parameter by its key.

#### get(string $key, mixed $default = null)

Get a given query parameter by its key or return the default.

#### all()

Retrieve all query parameters as an array.

#### isEmpty

Check if the query parameter object is empty.

{% hint style="info" %}
Click here to view the API reference for this method.
{% endhint %}


# HTTP Client Config

Like headers and query parameters, HTTP config can be added by using the `config()` method on either the connector or the request. When you add config to a connector instance, every request sent through that connector will merge those configuration options with the request. When you add config to a request instance, it will just be added to that one request instance.

### Guzzle Config

Saloon's default sender is the GuzzleSender. By default, the config options are added to the guzzle request. You can use any of the options Guzzle provide in the Saloon's config. [Click here to see a list of the available options Guzzle provide.](https://docs.guzzlephp.org/en/stable/request-options.html)

### Default HTTP Config

You may configure the default HTTP config on the connector or request using the protected `defaultConfig` method.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    // {...}
    
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 30,
        ];
    }
}
```

{% hint style="info" %}
Default HTTP config on a connector will be applied to every request that uses the connector.
{% endhint %}
{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 30,
        ];
    }
}
```

{% endtab %}
{% endtabs %}

#### Using Properties With Default HTTP Config

You may also use properties in requests to populate HTTP config, for example - specifying an exact timeout on a per-request basis.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
    protected int $timeout;
    
    public function __construct(int $timeout)
    {
        $this->timeout = $timeout;
    }
    
    protected function defaultConfig(): array
    {
        return [
            'timeout' => $this->timeout,
        ];
    }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$request = new GetServersRequest(timeout: 30);
```

{% endtab %}
{% endtabs %}

### Using the config Method

Saloon also offers a handy config API to manage them easily after a request instance has been created. Use the `config()` method on your request to manage them. HTTP config added to the request is prioritised more than the connector's config. This is useful for changing them before a request is sent.

```php
<?php

$request = new GetServersRequest();

$request->config()->add('timeout', 30);

$all = $request->config()->all();

// array: ['timeout' => 30]
```

{% hint style="warning" %}
When you have default HTTP config on a connector, they won't be visible to the request instance as they are merged later in the request lifecycle. Still, request HTTP config will have a higher priority than connector config.
{% endhint %}

### Available Methods

#### set(array $items)

Overwrite the config on the request with a new array.

#### merge(...$items)

Merge arrays of config.

#### remove(string $key)

Remove a given config option by its key.

#### get(string $key, mixed $default = null)

Get a givenconfig option by its key or return the default.

#### all()

Retrieve all config options as an array.

#### isEmpty

Check if the config object is empty.

{% hint style="info" %}
Click here to view the API reference for this method.
{% endhint %}


# Sending Body/Data

When sending HTTP requests, a common requirement is to attach a payload/body to POST, PUT, or PATCH requests, like JSON, XML or multipart data. Saloon makes this easy for you with built-in body traits.

### Getting Started

To get started, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client. Also make sure to change your method to POST, PUT or PATCH depending on the requirements of the API.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add a trait to provide an implementation for the missing `body()` method. Saloon has a trait for all the common types of request bodies.

Continue reading below to understand more about the specific body type that you need.

{% content-ref url="request-body-data/json-body" %}
[json-body](https://docs.saloon.dev/the-basics/request-body-data/json-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/multipart-form-body" %}
[multipart-form-body](https://docs.saloon.dev/the-basics/request-body-data/multipart-form-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/stream-body" %}
[stream-body](https://docs.saloon.dev/the-basics/request-body-data/stream-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/form-body-url-encoded" %}
[form-body-url-encoded](https://docs.saloon.dev/the-basics/request-body-data/form-body-url-encoded)
{% endcontent-ref %}

{% content-ref url="request-body-data/xml-body" %}
[xml-body](https://docs.saloon.dev/the-basics/request-body-data/xml-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/string-plain-text-body" %}
[string-plain-text-body](https://docs.saloon.dev/the-basics/request-body-data/string-plain-text-body)
{% endcontent-ref %}


# JSON Body

To get started, make change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasJsonBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasJsonBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasJsonBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: application/json` header for you when using the `HasJsonBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $type,
        protected string $provider
    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->ubuntuVersion,
            'type' => $this->type,
            'provider' => $this->provider,
        ];
    }
}
```

Sometimes there can be too many items to define as constructor arguments. If this is the case for you, you can require the user to provide an array, or even a DTO to populate the default body of your request.

{% tabs %}
{% tab title="Using an array" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected array $payload
    ){}
    
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
```

{% endtab %}

{% tab title="Using a DTO (Data Transfer Object)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;
    
    public function __construct(
<strong>        protected Server $server
</strong>    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->server->ubuntuVersion,
            'type' => $this->server->type,
            'provider' => $this->server->provider,
        ];
    }
}
</code></pre>

{% endtab %}
{% endtabs %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the JSON request body:

* add(string $key, mixed $value) -> **Add items to the JSON body**
* remove(string $key) -> **Remove items from the JSON body**
* merge(…$values) -> **Merge another array of items into the JSON body**
* set(array $value) -> **Overwrite the JSON body entirely**
* all(): array -> **Get all the values of the JSON body**
* isEmpty(): bool -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->add('ubuntu_version', '22.04');

$request->body()->merge([
    'type' => 'app',
    'provider' => 'ocean2',
]);

$body = $request->body()->all();

// array: [
//    'ubuntu_version' => '22.04',
//    'type' => 'app',
//    'provider' => 'ocean2',
// ]

```

### Connector Body

If you would like to also have JSON body on your connector, you can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the properties will be merged. This is useful if you want to have a shared JSON body across every request, like an authentication token.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasJsonBody;

    protected function defaultBody(): array
    {
        return [
            'name' => 'Sam',
        ];
    }
}
```

### JSON Flags

When using the `HasJsonBody` trait, you may want to customise the flags used when encoding the body to be sent to the API you are integrating with. You may do this with the `setJsonFlags` method. It's recommended that you set this within the constructor of your request or connector to ensure that the flags are always used.

For example, if I know that my JSON will contain URLs, but I don't want the URL slashes to be escaped, I can use the `JSON_UNESCAPED_SLASHES` flag. You can also specify multiple flags at once by using the `|` pipe to separate the flags.

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;
    
    public function __construct()
    {
        $this->body()->setJsonFlags(JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
```


# Multipart Form Body

Multipart body (multipart/form-data) is often used in modern APIs as a way to upload a mixture of files and data. Saloon makes handling multipart bodies easy by providing you with a standardised way of interacting with the values to be sent. You don't have to worry about calculating boundaries or properly encoding the multipart values, you can just use simple arrays and a value object to define the values.

To get started, make change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class UploadProfilePictureRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasMultipartBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasMultipartBody;
</strong>
class UploadProfilePictureRequest extends Request implements HasBody
{
<strong>    use HasMultipartBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: multipart/form-data` header for you when using the `HasMultipartBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### The MultipartValue

To build up multipart data, Saloon uses a class `Saloon\Data\MultipartValue`. This class is used as a value object and when your request is sent, it will automatically be converted into a stream to be sent to the API. To see how to use this class, continue reading below.

```php
<?php

use Saloon\Data\MultipartValue;

new MultipartValue(
    name: 'picture', // Required: the name of the multipart value
    value: 'file-path-or-stream', // Required: Absolute path or file stream
    filename: 'profile.png', // Optional: File name
    headers: [], // Optional: Headers to be sent with the individual value
)
```

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more. In this example, we are uploading a profile picture to a server so we will expect a file path through our constructor - however, Saloon can support file paths or streams.

```php
<?php

use Saloon\Http\Request;
use Saloon\Data\MultipartValue;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasMultipartBody;

class UploadProfilePictureRequest extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $filePath,
    ){}
    
    protected function defaultBody(): array
    {
        return [
            new MultipartValue(name: 'picture', value: $this->filePath)
        ];
    }
}
```

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the multipart request body:

* add(string $name, mixed $value, string $filename = null, array $headers = \[]) -> **Add a multipart value to the  multipart body**
* attach(MultipartValue $value) -> **Attach a multipart directly to the  multipart body**
* remove(string $key) -> **Remove an item from the multipart body**
* merge(... $arrays) -> **Merge another array of multipart values into the multipart body**
* set(array $value) ->  **Overwrite the entire multipart body with a different set of values**
* all() -> **Get the array of multipart values**
* get(string $key) **-> Get an individual multipart value**
* isEmpty() **-> Check if the multipart body is empty**
* isNotEmpty() **-> Check if the multipart body is not empty**

```php
<?php

$request = new UploadProfilePictureRequest;

$request->body()->add(
    name: 'picture',
    contents: 'your-file-contents-or-stream', 
    filename: 'picture.png', // Optional file name
    headers: [
       // Optional custom headers
    ]
);

// You may also get a multipart value by name.

$request->body()->get('picture'); // MultipartValue class

```

{% hint style="info" %}
The `contents` of the `MultipartValue` class can be either the raw-text contents of the file or a PHP stream/resource.
{% endhint %}

### Connector Body

If you would like to also have multipart body on your connector, you can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the properties will be merged. This is useful if you want to have a shared value across every request.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Data\MultipartValue;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasMultipartValue;

class ForgeConnector extends Connector implements HasBody
{
    use HasMultipartValue;

    protected function defaultBody(): array
    {
        return [
            new MultipartValue(name: 'logo', value: 'image-contents'), 
            new MultipartValue(name: 'logo', value: StreamObject), 
            new MultipartValue(name: 'logo', value: 'image-contents', filename: 'logo.png', headers: [...]),
        ];
    }
}
```


# Form Body (URL Encoded)

To get started, make change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasFormBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasFormBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasFormBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: application/x-www-form-urlencoded` header for you when using the `HasFormBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $type,
        protected string $provider
    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->ubuntuVersion,
            'type' => $this->type,
            'provider' => $this->provider,
        ];
    }
}
```

Sometimes there can be too many items to define as constructor arguments. If this is the case for you, you can require the user to provide an array, or even a DTO to populate the default body of your request.

{% tabs %}
{% tab title="Using an array" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected array $payload
    ){}
    
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
```

{% endtab %}

{% tab title="Using a DTO (Data Transfer Object)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;
    
    public function __construct(
<strong>        protected Server $server
</strong>    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->server->ubuntuVersion,
            'type' => $this->server->type,
            'provider' => $this->server->provider,
        ];
    }
}
</code></pre>

{% endtab %}
{% endtabs %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the form request body:

* add(string $key, mixed $value) -> **Add items to the form body**
* remove(string $key) -> **Remove items from the form body**
* merge(…$values) -> **Merge another array of items into the form body**
* set(array $value) -> **Overwrite the form body entirely**
* all(): array -> **Get all the values of the form body**
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->add('ubuntu_version', '22.04');

$request->body()->merge([
    'type' => 'app',
    'provider' => 'ocean2',
]);

$body = $request->body()->all();

// array: [
//    'ubuntu_version' => '22.04',
//    'type' => 'app',
//    'provider' => 'ocean2',
// ]

```

### Connector Body

If you would like to also have form body on your connector, you can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the properties will be merged. This is useful if you want to have a shared form body across every request, like an authentication token.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasFormBody;

    protected function defaultBody(): array
    {
        return [
            'name' => 'Sam',
        ];
    }
}
```


# XML Body

To get started, make change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasXmlBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasXmlBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasXmlBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: application/xml` header for you when using the `HasXmlBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasXmlBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasXmlBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $type,
        protected string $provider
    ){}
    
    protected function defaultBody(): string
    {
        return '
            <?xml version="1.0"?>
            <root>
                <ubuntu-version>' . $this->ubuntuVersion . '</ubuntu-version>
                <type>' . $this->type . '</type>
                <provider>' . $this->provider . '</provider>
            </root>
        ';
    }
}
```

{% hint style="info" %}
While you are expected to return a raw string for the XML body, it can be tedious writing XML as text. Saloon strongly recommends using [Spatie's "Array to XML"](https://github.com/spatie/array-to-xml) package which helps you convert a simple array into XML which is much more developer friendly. See [below](#using-spaties-array-to-xml-package) for an example using the package.
{% endhint %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the XML request body:

* set(string $value) -> **Overwrite the XML body entirely**
* all(): array -> **Get the XML body**&#x20;
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->set('<?xml version="1.0"?><root></root>');

$body = $request->body()->all();

// string: 'plain-text-response-body'

```

### Using Spatie's Array To XML Package

Writing out XML body can be laborious and time-consuming. In some previous integrations, we have leaned on a library to help write out the XML request bodies. This library is [Spatie's "Array to XML"](https://github.com/spatie/array-to-xml) package. You can use this to provide a simple array of keys and values to build up XML data and then pass it through to Saloon.

{% tabs %}
{% tab title="Default Body" %}

```php
<?php

use Saloon\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasXmlBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasXmlBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $type,
        protected string $provider
    ){}
    
    protected function defaultBody(): string
    {
        return ArrayToXml::convert([
            'ubuntu-version' => $this->ubuntuVersion,
            'type' => $this->type,
            'provider' => $this->provider,
        ]);
    }
}
```

{% endtab %}

{% tab title="Using Body Methods" %}

```php
<?php

$request = new CreateServerRequest;

$request->body()->set(ArrayToXml::convert([
   'ubuntu-version' => '22.04',
   'type' => 'web',
   'provider' => 'aws',
]));
```

{% endtab %}
{% endtabs %}

### Connector Body

You can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the body on the request will take priority. If you have the body on just the connector but not on the request, the request will inherit the body on the connector.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasXmlBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasXmlBody;

    protected function defaultBody(): string
    {
        return '<?xml version="1.0"?><root></root>';
    }
}
```


# String / Plain Text Body

This body type has been created if you need to send a plain string/text to the server. This body type does not come with a default Content-Type, so you must provide this.

To get started, make change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client. We recommend aliasing it because the string body trait is also called `HasBody`.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody as HasBodyContract;

<strong>class CreateServerRequest extends Request implements HasBodyContract
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
<strong>use Saloon\Traits\Body\HasBody;
</strong>use Saloon\Contracts\Body\HasBody as HasBodyContract;

class CreateServerRequest extends Request implements HasBodyContract
{
<strong>    use HasBody;
</strong>
    protected Method $method = Method::POST;
    
    // Define our own Content-Type...
    
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'text/plain',
        ];
    }
}
</code></pre>

{% hint style="info" %}
Saloon won't add a Content-Type header for you for plain string bodies so you must provide your own Content-Type header.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

```php
<?php

use Saloon\Http\Request;
use Saloon\Traits\Body\HasBody;
use Saloon\Contracts\Body\HasBody as HasBodyContract;

class CreateServerRequest extends Request implements HasBodyContract
{
    use HasBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $provider
    ){}
    
    protected function defaultBody(): string
    {
        return 'Howdy, Partner. I want a ' . $this->ubuntuVersion . ' server through ' . $this->provider . ' provider!';
    }
}
```

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the string request body:

* set(string $value) -> **Overwrite the string body entirely**
* all(): array -> **Get the string body**&#x20;
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->set('Howdy, Partner');

$body = $request->body()->all();

// string: 'plain-text-response-body'

```

### Connector Body

You can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the body on the request will take priority. If you have the body on just the connector but not on the request, the request will inherit the body on the connector.

```php
<?php

use Saloon\Http\Request;
use Saloon\Traits\Body\HasBody;
use Saloon\Contracts\Body\HasBody as HasBodyContract;

class ForgeConnector extends Connector implements HasBodyContract
{
    use HasBody;

    protected function defaultBody(): string
    {
        return 'Howdy, Partner';
    }
}
```


# Stream Body

Similar to the string / plain text body, the stream body allows you to send a stream or a file resource directly to the server. This is useful if the API accepts a plain-text style input but large files in the form of streams.

To get started, make change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client.&#x20;

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasStreamBody;

class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasStreamBody;
</strong>
    protected Method $method = Method::POST;
    
    // Define our own Content-Type...
    
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'text/plain',
        ];
    }
}
</code></pre>

{% hint style="info" %}
Saloon won't add a Content-Type header for you for plain stream bodies so you must define the Content-Type or the API might not accept the data.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasStreamBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasStreamBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected mixed $stream,
    ){}
    
    protected function defaultBody(): mixed
    {
        return $this->stream;
    }
}
```

{% hint style="info" %}
Saloon can accept a PHP resource or a PSR `StreamInterface` Stream implementation as the stream.
{% endhint %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the stream request body:

* set(resource|StreamInterface $value) -> **Set the stream**
* all(): StreamInterface -> **Get the stream**
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->set(fopen('cowboy-hat.png', 'r'));

$body = $request->body()->all(); // Returns PSR StreamInterface

```

### Connector Body

You can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the body on the request will take priority. If you have the body on just the connector but not on the request, the request will inherit the body on the connector.

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasStreamBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasStreamBody;

    protected function defaultBody(): mixed
    {
        return fopen('cowboy-hat.png', 'r');
    }
}
```


# Authentication

There are several ways to authenticate with an API; most of the time, you are expected to provide a header or query parameter to authenticate. Saloon has built helpers for the most common authentication methods to help you, but you can also create custom authenticators for advanced authentication. You can also provide defaults for authentication should you require it.

{% hint style="info" %}
While you may opt-in to Saloon's authentication classes, it's not the only way to authenticate with Saloon. Another good method of authentication is using the `defaultHeaders, defaultConfig` or `defaultQuery` on your connector/request.
{% endhint %}

### Available Authentication Methods

These authentication methods are available on both the connector and the request instances.

1. **withTokenAuth($token, $prefix = 'Bearer')**

   Sends an `Authorization` header e.g `Authorization: Bearer your-api-key`
2. **withBasicAuth($username, $password)**\
   Uses HTTP "basic" authentication
3. **withDigestAuth($username, $password)**\
   Uses HTTP "basic" authentication with a digest
4. **withQueryAuth($parameter, $value)**\
   Uses a query parameter to authenticate. e.g `?api_key=your-api-key`

### Looking for OAuth2 authentication?

Saloon has native support for the client credentials and authorization code grant types for OAuth2. To get setup with this, head on over to the [dedicated OAuth Authentication page](https://docs.saloon.dev/digging-deeper/oauth2-authentication/oauth2-authentication).

### Authenticating all requests

Most of the time, you use one API key for all your API requests to a service, like a personal access token or a username/password combination. Therefore, it's recommended that you use the constructor of your connector and expect an API token to be provided. This is useful if you have a different API key per user that needs to be passed into your connector.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

class ForgeConnector extends Connector
{ 
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    /**
     * Constructor
     *
     * @param string $apiKey
     */
    public function __construct(protected string $apiKey)
    {
        $this->withTokenAuth($this->apiKey);
    }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$forge = new ForgeConnector('my-api-key');

// All API requests will be authenticated with the API key
```

{% endtab %}
{% endtabs %}

### Default authentication

Sometimes you may use a single API key in your .env file/application config, and you don't want to pass it in every time you instantiate; you may use the `defaultAuth` method on your connector, and every request will be authenticated.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;

class ForgeConnector extends Connector
{ 
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator(config('app.services.forge'));
    }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$forge = new ForgeConnector;

// All API requests will be authenticated with the default auth.
```

{% endtab %}
{% endtabs %}

#### Authenticator Classes

| Method         | Class                                     |
| -------------- | ----------------------------------------- |
| withTokenAuth  | use Saloon\Http\Auth\TokenAuthenticator;  |
| withBasicAuth  | use Saloon\Http\Auth\BasicAuthenticator;  |
| withDigestAuth | use Saloon\Http\Auth\DigestAuthenticator; |
| withQueryAuth  | use Saloon\Http\Auth\QueryAuthenticator;  |

### Authentication on the fly

You may want to authenticate a request or a connector on the fly on a per-request or per-connector basis. You can use the authentication methods directly.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

$forge = new ForgeConnector;
$forge->withTokenAuth($user->forge_api_key);

// All API requests sent with this connector instance will be authenticated.
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

$request = new GetServersRequest;
$request->withTokenAuth($user->forge_api_key);

// This single request sent will be authenticated.
```

{% endtab %}
{% endtabs %}

### Custom Authenticators

Sometimes the API integration you are building requires multiple ways to authenticate, like a token and a certificate or perhaps authenticating an OAuth 2 API. When the built-in authentication is insufficient, You can build custom authenticators that can be transported between your application and Saloon's requests.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Contracts\PendingRequest;
use Saloon\Contracts\Authenticator;

class CustomAuthenticator implements Authenticator
{
    public function __construct(
        public string $apiKey,
    ) {
        //
    }

    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('X-API-Key', $this->apiKey);
    }
}
```

{% endtab %}

{% tab title="Usage (Connector)" %}

```php
<?php

$forge = new ForgeConnector;
$forge->authenticate(new CustomAuthenticator('my-api-key'));

// When requests are sent with this connector, the X-API-KEY header is added.

```

{% endtab %}

{% tab title="Usage (Request)" %}

```php
<?php

$request = new GetServersRequest;
$request->authenticate(new CustomAuthenticator('my-api-key'));

// When the request is sent with this connector, the X-API-KEY header is added.
```

{% endtab %}
{% endtabs %}

{% hint style="info" %}
You may use custom authenticators in the same way as other authenticators, so you may use the `defaultAuth` method or even authenticate on the fly.
{% endhint %}

### Authenticating APIs that require per-request authentication

Some APIs that you will integrate with require an authentication token, such as a JWT, per request. Usually, this is quite tricky as it requires logic to wrap around your requests. Still, with Saloon, you can create a custom authenticator which makes another request to get the authentication token.

Let's start by creating a custom authenticator. This authenticator will make another request using the same connector and then authenticate the original request with the authentication token.&#x20;

<pre class="language-php"><code class="lang-php">&#x3C;?php

class ForgeAuthenticator implements Authenticator
{
    public function set(PendingRequest $pendingRequest): void
    {
        // Make sure to ignore the authentication request to prevent loops.

	if ($pendingRequest->getRequest() instanceof AuthRequest) {
	    return;
	}

	// Make a request to the Authentication endpoint using the same connector.

<strong>	$response = $pendingRequest->getConnector()->send(new AuthRequest);
</strong>				
	// Finally, authenticate the previous PendingRequest before it is sent.

<strong>        $pendingRequest->headers()->add('Authorization', 'Bearer ' . $response->json('token'));
</strong>    }
}
</code></pre>

{% hint style="info" %}
Unfortunately, you cannot use the authentication methods like **authenticate** or **withTokenAuth** because you cannot call authenticators inside of each other.&#x20;
{% endhint %}

Next, we will use our authenticator as the default authenticator on the request. If you need to use an API token per user, you should pass a token into the constructor of the connector.

<pre class="language-php"><code class="lang-php">&#x3C;?php

class ForgeConnector extends Connector
{ 
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }

    protected function defaultAuth(): ?Authenticator
    {
<strong>        return new ForgeAuthenticator;
</strong>    }
}
</code></pre>

Now when we make a request, our authenticator will make an additional request to retrieve the authentication token, and then use that token in the previous request. This way you can send your request like normal.

```php
<?php

$forge = new ForgeConnector;
$response = $forge->send(new GetServersRequest);
```


# Sending Requests

### Getting Started

To start sending requests, instantiate your connector class and request class and use the `send` or `sendAsync` methods. When using the `send` method. you will receive a `Response` class.

```php
<?php

$forge = new ForgeConnector('api-token');
$request = new GetServersRequest;

$response = $forge->send($request);
```

### Asynchronous Requests

Saloon supports asynchronous requests out of the box. Use the `sendAsync` method, and you will receive an instance of `PromiseInterface`. Saloon uses Guzzle's Promises library, which uses the A+ standard. [Click here to read more](https://github.com/guzzle/promises).

```php
<?php

$forge = new ForgeConnector('api-token');
$promise = $forge->sendAsync(new GetServersRequest);

$promise
   ->then(function (Response $response) {
      // Handle Response
   })
   ->otherwise(function (RequestException $exception) {
        // Handle Exception
    });
```

{% hint style="info" %}
Saloon supports all the features Guzzle offers for asynchronous requests, including unwrapping promises and request pooling for high-performance API calls. [Click here to learn more.](https://docs.saloon.dev/digging-deeper/concurrency-and-pools)
{% endhint %}

### Sending Solo Requests

Please make sure to read the section on [solo requests](https://docs.saloon.dev/digging-deeper/solo-requests) first to configure your request. You can send solo requests directly.&#x20;

```php
<?php

$request = new GetServersRequest;
$response = $request->send();
```

You have the following methods available on the solo request.

* send(MockClient $mockClient = null)
* sendAsync(MockClient $mockClient = null)
* createPendingRequest
* connector

### Sending requests without instantiating the connector

With previous versions of Saloon, you could send a request directly without having to use a connector to send a request. While sending requests through the connector has many benefits, you may wish to add this feature with the `HasConnector` trait on your request.

#### Adding the trait

Once you have added the trait to your request, make sure to add the `connector` protected property and define your connector class. You may instead extend the `resolveConnector` method if you need a more advanced solution.

{% tabs %}
{% tab title="Connector Property" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;
use App\Http\Integrations\LaravelForge\Forge;

class GetServersRequest extends Request
{
<strong>    use HasConnector;
</strong><strong>    
</strong><strong>    protected string $connector = Forge::class;
</strong>
    protected string $method = 'GET';

    public function resolveEndpoint(): string
    {
        return '/servers';
    }
}
</code></pre>

{% hint style="warning" %}
When defining a connector with a property, you must not have any constructor properties on your connector.
{% endhint %}
{% endtab %}

{% tab title="Using resolveConnector" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;
use App\Http\Integrations\LaravelForge\Forge;

class GetServersRequest extends Request
{
<strong>    use HasConnector;
</strong>
    protected string $method = 'GET';
    
<strong>    protected function resolveConnector(): Connector
</strong><strong>    {
</strong><strong>        return new ForgeConnector;
</strong><strong>    }
</strong>
    public function resolveEndpoint(): string
    {
        return '/servers';
    }
}
</code></pre>

{% endtab %}
{% endtabs %}

#### Sending requests

```php
<?php

$request = new GetServersRequest;
$response = $request->send();
```

{% hint style="info" %}
When sending multiple requests for the same service, use the same connector instance as it has a [significant performance improvement](https://twitter.com/carre_sam/status/1617096982626959361) over using a new connector instance for every request.
{% endhint %}

Now you can use the following methods on your request.

* send(MockClient $mockClient = null)
* sendAsync(MockClient $mockClient = null)
* createPendingRequest
* connector

### Request Properties

You may also overwrite any headers, query parameters, HTTP client config and request body on the connector or request. Read through the sections above for all the methods on the request property methods.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

$forge = new ForgeConnector('api-token');

// All requests sent will have the header and query parameter applied

$forge->headers()->add('X-Custom-Header', 'Hello'!);
$forge->query()->add('page', 5);
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

$forge = new ForgeConnector('api-token');
$request = new GetServersRequest;

// The single request will have the additional header and query parameter.

$request->headers()->add('X-Custom-Header', 'Hello'!);
$request->query()->add('page', 5);

$response = $connector->send($request);
```

{% endtab %}
{% endtabs %}


# Responses

Depending on how you sent your request (synchronous/asynchronous) you will either receive an instance of `Response` or a `PromiseInterface.`

### Handling synchronous responses

By default, Saloon will return an instance of `Saloon\Http\Response`. This response class contains many helpful methods for interacting with your HTTP response. You can see a list of the available methods below.

```php
<?php

$forge = new ForgeConnector('api-token');
$response = $forge->send(new GetServersRequest);

$body = $response->body();
$decodedBody = $response->json();
```

{% hint style="danger" %}
By default, Saloon will not throw an exception if a synchronous request fails. [Refer to the handling failures section for handling errors.](https://docs.saloon.dev/the-basics/handling-failures)
{% endhint %}

### Available Methods

| Method                      | Description                                                                                                                                                             |
| --------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| body                        | Returns the HTTP body as a string                                                                                                                                       |
| stream                      | Returns the HTTP body as a stream                                                                                                                                       |
| headers                     | Returns all the headers. You can interact with them just like you do with requests/connectors.                                                                          |
| header                      | Returns a single header from the response.                                                                                                                              |
| status                      | Return the HTTP status messages                                                                                                                                         |
| getPsrResponse              | Returns a PSR-compatible response                                                                                                                                       |
| getPendingRequest           | Returns the PendingRequest created to send the request, containing everything sent like headers, body, and HTTP client config.                                          |
| getRequest                  | Returns the original request class that was used to send the request.                                                                                                   |
| json                        | Retrieves a JSON response body and json\_decodes it into an array.                                                                                                      |
| object                      | Retrieves a JSON response body and json\_decodes it into an object.                                                                                                     |
| xml                         | Retrieves the response body and creates a SimpleXmlElement.                                                                                                             |
| collect                     | Retrieves a JSON response body and json\_decodes it into a Laravel collection. **Requires illuminate/collections to be installed.**                                     |
| dto                         | Converts the response into a data-transfer object. You must define your DTO first, [click here to read more.](https://docs.saloon.dev/the-basics/data-transfer-objects) |
| successful                  | Returns true if the response status is between 200 and 300.                                                                                                             |
| ok                          | Returns true if the response status is 200.                                                                                                                             |
| redirect                    | Returns true if the response status is between 300 and 400.                                                                                                             |
| serverError                 | Returns true if the response status is >= 500.                                                                                                                          |
| clientError                 | Returns true if the response status is between 400 and 500.                                                                                                             |
| failed                      | Determines if a request has failed. By default, it will return true if the response status is not 2xx.                                                                  |
| onError                     | Allows you to define a callback if the response is considered "failed".                                                                                                 |
| toException                 | Creates an exception if the response is considered "failed. [Click here to read more about handling failures](https://docs.saloon.dev/the-basics/handling-failures)     |
| throw                       | Will throw an exception if the response is considered "failed".                                                                                                         |
| isCached                    | Denotes if the response is cached. Only used when using the Saloon caching plugin.                                                                                      |
| isMocked                    | Denotes if the response has been mocked.                                                                                                                                |
| isSimulated                 | Denotes if the response was "simulated", like when a MockResponse was used.                                                                                             |
| getSimulatedResponsePayload | When the response is simulated, get the underlying SimulatedResponsePayload class.                                                                                      |
| getSenderException          | Get the sender exception if a request failed.                                                                                                                           |
| getRawResponse              | Get the raw response provided from the sender.                                                                                                                          |
| \_\_toString                | Returns the HTTP body as a string                                                                                                                                       |

### Handling asynchronous responses

When using concurrent requests/pooling or `sendAsync` , Saloon will respond with a `GuzzleHttp\Promise\PromiseInterface.` The promise will contain a `Response` a class described above. When the request fails, Saloon will not use the `then` method but return an instance of `RequestException`in the `otherwise` block.

```php
<?php

use Saloon\Contracts\Response;

$forge = new ForgeConnector('api-token');
$promise = $forge->sendAsync(new GetServersRequest);

$promise
    ->then(function (Response $response) {
        // Handle successful response
    })
    ->otherwise(function (Exception $exception) {
        // Handle failed request
    });
```

### Custom responses

Sometimes you may want to use your response class. This is useful if you want to add your methods or overwrite Saloon's response methods. Saloon allows you to overwrite the response at a connector level for all requests or at a per-request level for a granular response.

You may extend the `Saloon\Http\Response` class or provide your own implementation with the `Saloon\Contracts\Response` interface. You may use the `HasResponseHelpers` middleware when making your own implementation to save defining every method.

#### Using the response property

The simplest way of registering a custom response is to use the `$response` property on either the connector or request.

{% tabs %}
{% tab title="Connector" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    // {...}
    
<strong>    protected ?string $response = CustomResponse::class;
</strong>}
</code></pre>

{% endtab %}

{% tab title="Request" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
<strong>    protected ?string $response = CustomResponse::class;
</strong>}
</code></pre>

{% endtab %}
{% endtabs %}

#### Using the resolveResponseClass method

When you need a more advanced way to define a custom response, use the `resolveResponseClass` method on either the connector or request.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    // {...}
    
    public function resolveResponseClass(): string
    {
        return CustomResponse::class;
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;

class GetServersRequest extends Request
{
    // {...}
    
    public function resolveResponseClass(): string
    {
        return CustomResponse::class;
    }
}
```

{% endtab %}
{% endtabs %}


# Data Transfer Objects

When building API integrations, sometimes dealing with a raw response or a JSON response can be tedious and unpredictable. Data transfer objects are a good solution as they allow you to define a structure for a request and response. Saloon supports casting a response from an API request into a DTO.

### Casting responses into DTOs

Firstly, in your request or connector, extend the `createDtoFromResponse` method. Within this method, you get access to the `Response` object containing the response to cast into a data transfer object. In this example, I have created a `fromResponse` method so I can write all the logic

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Response;

class GetServerRequest extends Request
{
    // {...}
    
    public function createDtoFromResponse(Response $response): mixed
    {
        return Server::fromResponse($response);
    }
}
```

I have created a `Server` data transfer object that I will use. This is what the `Server` DTO looks like this:

```php
<?php

use Saloon\Contracts\Response;

class Server
{
    public function __construct(
        public int $id,
        public string $name,
        public string $ipAddress,
    ){}

    public static function fromResponse(Response $response): self
    {
        $data = $response->json();

        return new static($data['id'], $data['name'], $data['ip']);
    }
}
```

### Retrieving your DTO

Now we have defined our DTO on our request or connector, we can use the build in `dto` or `dtoOrFail` methods on our response class.

```php
<?php

$connector = new ForgeConnector;

$response = $connector->send(new GetServerRequest(id: 12345));

// Create a DTO even if the response was a failure
$server = $response->dto();

// Create a DTO or throw an exception if the response was not successful
$server = $response->dtoOrFail();
```

{% hint style="warning" %}
When using the `dto` method, Saloon will attempt to create a DTO from your response no matter the status of the response. This allows you to create "error" data transfer objects. If you don't want to use this functionality, you can use the `dtoOrFail` method which will throw a LogicException if the response was a failure. **You can customise what is considered a failed response** [**here**](https://docs.saloon.dev/handling-failures#customising-when-saloon-thinks-a-request-has-failed)**.**
{% endhint %}

### Accessing the response from your DTO

Sometimes debugging a DTO can be difficult, especially if you have passed the data object through your application and no longer have access to the original `Response` that you created the DTO from. Saloon can inject the response into your data transfer object for you if you use the `HasResponse` trait and the `WithResponse` interface. Let's add it to our existing `Server` DTO.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Contracts\Response;
use Saloon\Traits\Responses\HasResponse;
use Saloon\Contracts\DataObjects\WithResponse;

<strong>class Server implements WithResponse
</strong>{
<strong>    use HasResponse;
</strong>
    public function __construct(
        readonly public int $id,
        readonly public string $name,
        readonly public string $ipAddress,
    ){}

    public static function fromResponse(Response $response): self
    {
        $data = $response->json();

        return new static($data['id'], $data['name'], $data['ip']);
    }
}
</code></pre>

Now whenever we retrieve an instance of our data transfer object, you will be able to access the underlying response that was created with it!

```php
<?php

$server = $response->dto();

$response = $server->getResponse();
```

### Using a DTO to send a request

You may also allow your data transfer objects to go both ways, back into requests. You can do this easily with Saloon, just accept your DTO as an argument in the constructor of your request and then use the DTO to set default properties inside of the request.

{% tabs %}
{% tab title="Definition" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Contracts\Response;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class UpdateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;
    
    public function __construct(readonly protected Server $server)
    {
        //
    }
    
    public function resolveEndpoint(): string
    {
<strong>        return '/servers/' . $this->server->id;
</strong>    }
    
    protected function defaultBody(): array
    {
        return [
<strong>            'name' => $this->server->name,
</strong>        ];
    }
    
    public function createDtoFromResponse(Response $response): mixed
    {
        return Server::fromResponse($response);
    }
}
</code></pre>

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$connector = new ForgeConnector;
$response = $connector->send(new GetServerRequest(id: 12345));
$server = $response->dto();

// Modify the server
$server->name = 'YEE-HAW-2';

// Update the server request

$response = $connector->send(new UpdateServerRequest($server));
```

{% endtab %}
{% endtabs %}


# Handling Failures

Saloon has a powerful exception handler that has lots of exceptions you can use in your application. It can also be customised on a per-connector and per-request basis. This is especially useful when you have an API which returns an OK (200) response with an error in the body.

When you send a request, Saloon will not do anything if the request fails, but by default, it will use the status code to determine if a request is successful or not. The only exception to this is if Saloon cannot connect to an API, which will throw a `FatalRequestException`.

```php
<?php

$forge = new ForgeConnector;
$response = $forge->send(new ErrorRequest);

$response->failed(); // true
$response->status(); // 500
$response->body(); // {"message": "Server Error"}
```

### Always throw exceptions on failed requests

You may wish to always throw an exception if a request fails. You may add the `AlwaysThrowOnErrors` trait on your connector, and every request that fails will throw an exception, just like if you were to use the `throw` method. You may also add this trait to a request.

```php
<?php

use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class ForgeConnector extends Connector
{
    use AlwaysThrowOnErrors;
    
    // {...}
}
```

### Using the throw method

On a per-response basis, you may use the `throw` method after sending your response. This method will throw an exception if the response has a "failed" HTTP status code like 4xx or 5xx.

```php
<?php

$forge = new ForgeConnector;
$response = $forge->send(new ErrorRequest);

// throws InternalServerErrorException (extends ServerException)

$response->throw();
```

### Response Exceptions

Saloon's default exception handler contains the following exceptions based on the status code and severity of the exception. These are thrown depending on the method you use below.

```
SaloonException
├── FatalRequestException (Connection Errors)
└── RequestException (Request Errors)
    ├── ServerException (5xx)
    │   ├── InternalServerErrorException (500)
    │   ├── ServiceUnavailableException (503)
    │   └── GatewayTimeoutException (504)
    └── ClientException (4xx)
        ├── UnauthorizedException (401)
        ├── ForbiddenException (403)
        ├── NotFoundException (404)
        ├── MethodNotAllowedException (405)
        ├── RequestTimeOutException (408)
        ├── UnprocessableEntityException (422)
        └── TooManyRequestsException (429)
```

### Using the onError method

You may wish to write some custom logic in your application if a request fails, but you don't want to throw an exception. You may use the `onError` method from the response and provide a callable to be executed if an error happens.

```php
<?php

use Saloon\Contracts\Response;

$response = $forge->send(new ErrorRequest);

$response->onError(function (Response $response) {
    // Handle any logic when an error happens.
});

// Application logic is continued
```

{% hint style="info" %}
The `onError` method will only work if your HTTP status is either 4xx or 5xx.
{% endhint %}

### Handling failures with promises

When sending requests using `sendAsync` or using request pooling, you will receive a `PromiseInterface` instance. Since this class catches exceptions, Saloon will automatically pass the request exception in the `otherwise` block, and you do not have to use the `throw` method.

```php
<?php

use Saloon\Contracts\Response;

$forge = new ForgeConnector('api-token');
$promise = $forge->sendAsync(new GetServersRequest);

$promise
    ->then(function (Response $response) {
        // Handle successful response
    })
    ->otherwise(function (RequestException $exception) {
        // Handle failed request
    });
```

### Other response methods

Saloon offers some other methods to handle failed responses.

| Method             | Description                                                                                            |
| ------------------ | ------------------------------------------------------------------------------------------------------ |
| failed             | Determines if a request has failed. By default, it will return true if the response status is not 2xx. |
| serverError        | Returns true if the response status is >= 500.                                                         |
| clientError        | Returns true if the response status is between 400 and 500.                                            |
| toException        | Creates an exception if the response is considered "failed".                                           |
| throw              | Will throw an exception if the response is considered "failed".                                        |
| getSenderException | Get the sender exception if a request failed.                                                          |
| onError            | Allows you to define a callback if the response is considered "failed".                                |

### Customising when Saloon thinks a request has failed

By default, Saloon will consider a request as failed if the status code is 4xx or 5xx, for both client and server errors. You may choose to change how Saloon considers a request as failed, For example, you may integrate with an API which still returns a 2xx response status but with an error message in the response body. Just extend the `hasRequestFailed` method on your connector or request.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Response;

class ForgeConnector extends Connector
{
    // {...}
    
    public function hasRequestFailed(Response $response): ?bool
    {
        return str_contains($response->body(), 'Server Error');
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Response;

class ErrorRequest extends Request
{
    // {...}
    
    public function hasRequestFailed(Response $response): ?bool
    {
        return str_contains($response->body(), 'Server Error');
    }
}
```

{% endtab %}
{% endtabs %}

### Customising when exceptions are thrown

If you use the `throw` method or the `AlwaysThrowsOnErrors` trait, Saloon will throw an exception if the status code is 4xx or 5xx. Sometimes you may wish to change this behaviour. You should extend the `shouldThrowRequestException` method to change the default behaviour.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Response;

class ForgeConnector extends Connector
{
    // {...}
    
    public function shouldThrowRequestException(Response $response): bool
    {
        return str_contains($response->body(), 'Server Error');
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Response;

class ErrorRequest extends Request
{
    // {...}
    
    public function shouldThrowRequestException(Response $response): bool
    {
        return str_contains($response->body(), 'Server Error');
    }
}
```

{% endtab %}
{% endtabs %}

### Customising the request exception

By default, Saloon will use the exceptions [listed above](#default-exceptions), but you may choose to return your own exception if a request has failed. Just extend the `getRequestException` method on either your connector or request. You will receive an instance of the response and a sender exception, which may be nullable.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Response;
use \Throwable;

class ForgeConnector extends Connector
{
    // {...}
    
    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return new CustomException('Oh yee-naw!', $response, $senderException);
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Response;
use \Throwable;

class ErrorRequest extends Request
{
    // {...}
    
    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return new CustomException('Oh yee-naw!', $response, $senderException);
    }
}
```

{% endtab %}
{% endtabs %}

{% hint style="info" %}
When the `getRequestException` method is defined on both the connector and the request, the request method will take priority.
{% endhint %}


# Building SDKs

Saloon provides everything you need to build a great SDK or library for an API. It already offers the ability to mock responses, provide authentication, implement OAuth2 boilerplate and even record your API requests in your tests. With Saloon you won't need to write the same boilerplate code over and over again. Saloon comes with just three dependencies making your library or SDK lightweight.

### Example SDK

This documentation will be using PokéAPI as an example API. [Click here to see a full SDK example repository](https://github.com/Sammyjo20/pokeapi-sdk/tree/v2).

### Getting Started

To start building an SDK with Saloon, we recommend that you create a connector as your SDK class. Once you are familiar with connectors, create a class and extend the `Saloon\Http\Connector` class. This class allows you to configure the base URL and any defaults you may need like default headers or authentication.&#x20;

#### Example SDK Connector

This is an example SDK for a fun API the [PokéAPI](https://pokeapi.co/). As you can see, I have defined the API base URL, as well as used the constructor to require the person using the API always provides an authentication token. In the real world, the PokéAPI does not require an API token.

```php
<?php

namespace Pokeapi;

use Generator;
use Saloon\Http\Connector;
use Saloon\Contracts\Request;
use Pokeapi\Responses\PokeapiResponse;

class Pokeapi extends Connector
{
    /**
     * Resolve the base URL of the service.
     *
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return 'https://pokeapi.co/api/v2';
    }

    /**
     * Define default headers
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
    
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(string $token)
    {
        $this->withTokenAuth($token);
    }
}
```

#### Using the SDK connector

Now that we have created the SDK class, all we need to do is instantiate it and provide the API token. We are now ready to send requests through our SDK. You may add your own methods to this class is it is the root class of your SDK.

```php
<?php

use Pokeapi\Pokeapi;

$pokeapi = new Pokeapi('my-api-token');

// Ready to make requests!
```

### Sending Requests

One of the ways that you can build SDKs in Saloon is by creating request classes and then calling them from the SDK connector. This is the simplest way and you will get up and running really quickly with Saloon. Make sure you are familiar with how requests work first.

#### Getting started

Firstly, you will need to [create a request](https://docs.saloon.dev/the-basics/requests), this is exactly the same as making a normal request described in the documentation. Any requirements like data or pagination should just be provided in each request’s constructor.

#### Use your request

When you have created the request, all that developers would need to do is to instantiate and send the request on the connector. This means you only need to have two classes as a minimum for a fully-working SDK!

```php
<?php

use Pokeapi\Pokeapi;

$pokeapi = new Pokeapi('my-api-token');
$request = new GetAllPokemon(page: 1);

// Developers would just send the request.

$response = $pokeapi->send($request);
```

With this method, it’s really simple to build your SDK. All you would need to do is create all the requests and then document them in your README. Developers using your SDK can just instantiate your SDK and then use the `send` methods.

### Sending Requests With Methods

Sometimes you may want to make it easy for the developer to find all the methods that they need to call the API through your SDK. You can create methods on your connector which send an API request or you could write a "resource" class that contains lots of requests

{% tabs %}
{% tab title="Definition" %}

```php
<?php

class Pokeapi extends Connector
{
    // { ... }
    
    public function allPokemon(int $page): Response
    {
        return $this->send(new GetAllPokemon($page));
    }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$pokeapi = new Pokeapi('my-api-token');

$response = $pokeapi->allPokemon(page: 1);
```

{% endtab %}
{% endtabs %}

### Request Resources

The resource pattern can help you combine your SDK requests into simple groups that are easy for the developer to find and consume. The tutorial below will guide you through creating a resource class however you should customise it to your SDK.&#x20;

#### Creating the base resource class

Let's start by creating a `Resource` class. This class should just contain a constructor that passes in an instance of `Saloon\Contracts\Connector`.

```php
<?php

use Saloon\Contracts\Connector;

class Resource
{
    public function __construct(protected Connector $connector)
    {
        //
    }
}
```

#### Creating a resource

Now let's create a resource. For this API, I will create a Pokémon resource which will group all the Pokémon requests together. Imagine a resource like a controller in an MVC framework like Laravel. You should pass any arguments the request needs through the method arguments.

```php
<?php

class PokemonResource extends Resource
{
     public function all(int $page): Response
     {
         return $this->connector->send(new GetAllPokemon($page));
     }
     
     public function get(int $id): Response
     {
         return $this->connector->send(new GetSinglePokemon($id));
     }
}
```

#### Defining a resource on your connector

Now we'll define a method on the connector which returns this resource class. Don't forget to pass the connector's instance (`$this`) into the resource.

```php
<?php

class Pokeapi extends Connector
{
    // { ... }
    
    public function pokemon(): PokemonResource
    {
        return new PokemonResource($this);
    }
}
```

#### Using the resource

Now all our users have to do is access the `pokemon()` method on the SDK class to get access to all the various requests that our SDK has to offer.

```php
<?php

$pokeapi = new Pokeapi('my-api-token');

$allPokemon = $pokeapi->pokemon()->all(page: 1);

$giratina = $pokeapi->pokemon()->get(id: 'giratina');
```

### Custom Responses

You may wish to customise the `Response` class that is returned by Saloon from your SDK connector. [Click here to read more about custom responses.](#custom-responses)

### Additional Features

Please read through the other sections of Saloon's documentation to see the other features that you can offer for your SDK. Every other feature like testing, authentication, failure handling is all supported for SDKs.

### Testing

When building an SDK, it's important to write tests that ensure the SDK calls the correct requests from an API and returns the right response, especially if you're converting the response into a data-transfer-object. [Read through the testing section of the documentation](https://docs.saloon.dev/testing/manual-fake-responses) to get familiar with mocking and recording requests.


# Solo Requests

While Saloon's typical setup of a connector and requests is great, sometimes all you need is to make a single request to a service. For scenarios like these, you may create a "SoloRequest" instead of making a connector and a single request. This saves you from having to create additional classes.

### Setup

Create a class, but instead of extending `Saloon\Http\Request`, you should extend `Saloon\Http\SoloRequest.` Next, just define everything else like you would a normal request. Make sure to include the full URL of the service you are integrating with.

```php
<?php

use Saloon\Http\SoloRequest;
use Saloon\Enums\Method;

class GetAllPokemonRequest extends SoloRequest
{
    protected Method $method = Method::GET;
    
    public function resolveEndpoint(): string
    {
        return 'https://pokeapi.co/api/v2/pokemon';
    }
}
```

### Defaults

Saloon Requests allow you to define all your default headers, config, query parameters and define request body just like you would traditionally with a connector.

### Sending Solo Requests

As you don't have a connector for this request, you can use the `send` or `sendAsync` methods directly on the request instance. This method works exactly the same as it would on the connector.

```php
<?php

$request = new GetAllPokemonRequest;
$response = $request->send();
```


# Pagination v1 (Old)

### 👋 Just a quick note before we begin

In the process of creating Saloon's next major version, version three, a new pagination plugin was developed. The new pagination plugin has been redesigned to be more convenient for developers, and easier to implement and maintain. If you have not used Saloon's pagination feature before, it is recommended that you begin with the [Pagination v2 (Plugin)](https://docs.saloon.dev/digging-deeper/pagination-v2) rather than the built-in pagination. The built-in pagination has been removed from v3, so it is advisable to switch to the plugin version to avoid further upgrades when migrating to Saloon v3.

{% hint style="info" %}
If you are currently using version two's pagination, it is recommended to follow the new documentation and rebuild your paginators. [Click here to get started](https://docs.saloon.dev/digging-deeper/pagination-v2).
{% endhint %}

### Introduction

Most APIs you will integrate with will implement some sort of pagination for their records. Traditionally, traversing through many pages of results can be time-consuming and slow to build. Saloon helps to solve this problem with its pre-build paginators that can be used to iterate through hundreds of pages of results in a fast, beautiful way.

```php
<?php

$connector = new SpotifyConnector;

// Create a paginator and pass in a request class, in this example
// we'll pass in the LikedSongsRequest which will retrieve all
// the liked songs of the authenticated user.

$paginator = $connector->paginate(new LikedSongsRequest);

// Create a Laravel LazyCollection from the paginator and iterate
// over each of the results. Traditionally, the liked songs endpoint
// only lets you get 50 tracks per request, but the paginator will
// automatically grab every page of results and pass it into a 
// single collection! 🔥

$collection = $paginator->collect('items')->map(function ($track) {
    return sprintf('%s - %s', $track['artist'], $track['name']);
});

// Convert the LazyCollection into an array.

$data = $collection->all();
```

Saloon's paginators provide many ways to iterate over responses and even iterate through the results within a response, like the example above. This is incredible because you can retrieve all of the results at once instead of having to write logic to build up an array of results.

Saloon uses a custom-built iterator behind the scenes so memory usage is low and it's incredibly fast to iterate through hundreds of pages, especially when you combine it with asynchronous requests/pooling.

You can see the above example fully in the [Saloon Spotify Example Repository](https://github.com/Sammyjo20/saloon-v2-spotify-example/blob/main/app/Http/Controllers/TracksController.php)

{% hint style="warning" %}
Saloon's pagination currently only works with JSON APIs
{% endhint %}

### Supported Pagination Methods

Saloon has support for the three most common types of pagination. You should use the one that is used by the API you are integrating with. You can typically determine this from the query parameters that are provided.

* Paged Pagination (?page=1)
* Limit / Offset Pagination (?limit=100\&offset=0)
* Cursor Pagination (?cursor=string)

### Getting Started

Before you get started, make sure that the request you are going to paginate through works as expected. Make the request like you normally would and read the JSON response output. Take note of the pagination properties like the "next\_page\_url", the "results" count, and the "limit" and "offset" variables. You will need these to configure the paginator. **It's recommended you use a tool like Postman or Saloon in a test to see what the response looks like.**

Once you have recorded an example response, go to your connector and add the `HasPagination` interface. Next, you should define the `paginate` method which accepts a `Request` and additional arguments.

```php
<?php

use Saloon\Contracts\HasPagination;
use Saloon\Contracts\Paginator;
use Saloon\Contracts\Request;

class SpotifyConnector extends Connector implements HasPagination
{
    // {..}

    public function paginate(Request $request, mixed ...$additionalArguments): Paginator
    {
        //
    }    
}
```

Next, you need to configure a paginator to use. By default, each paginator has its own assumptions for query parameters and response body arguments for the data. You should customise those to suit the API you are integrating with.

{% tabs %}
{% tab title="PagedPaginator" %}
The PagedPaginator requires you to define the request and the per-page/limit. This is required to calculate how many pages the Paginator needs to iterate through. For example, the API may only allow me to paginate over a maximum of 50 results.

```php
<?php

use Saloon\Http\Paginators\PagedPaginator;

public function paginate(Request $request, mixed ...$additionalArguments): PagedPaginator
{
    return new PagedPaginator(connector: $this, originalRequest: $request, perPage: 50, ...$additionalArguments);
} 
```

**Defaults**

The `PagedPaginator` has assumed the following items are present in your API:

* The per-page query parameter is called `limit`
* The page query parameter is called `page`
* The next page URL is called `next_page_url` in the response.
* The results count is called `total`
* The first page is zero

**Configuring the PagedPaginator**

To configure the `PagedPaginator` you can use the following methods

```php
<?php

use Saloon\Http\Paginators\PagedPaginator;

public function paginate(Request $request, mixed ...$additionalArguments): PagedPaginator
{
    $paginator = new PagedPaginator($this, $request, 50, ...$additionalArguments);
    
    $paginator->setLimitKeyName('limit');
    $paginator->setTotalKeyName('count');
    $paginator->setPageKeyName('page');
    
    // You can use "dot" notion for nested data
    
    $paginator->setNextPageKeyName('meta.next');
    $paginator->setCurrentPage(0);
    
    return $paginator;
} 
```

{% endtab %}

{% tab title="OffsetPaginator" %}
The OffsetPaginator requires you to define the request and the limit. This is required to calculate how many pages the Paginator needs to iterate through. For example, the API may only allow me to paginate over a maximum of 50 results.

```php
<?php

use Saloon\Http\Paginators\OffsetPaginator;

public function paginate(Request $request, mixed ...$additionalArguments): OffsetPaginator
{
    return new OffsetPaginator(connector: $this, originalRequest: $request, perPage: 50, ...$additionalArguments);
} 
```

**Defaults**

The `OffsetPaginator`has assumed the following items are present in your API:

* The limit query parameter is called `limit`
* The offset query parameter is called `offset`
* The results count is called `total`
* The default offset is zero

**Configuring the OffsetPaginator**

To configure the `OffsetPaginator` you can use the following methods

```php
<?php

use Saloon\Http\Paginators\OffsetPaginator;

public function paginate(Request $request, mixed ...$additionalArguments): OffsetPaginator
{
    $paginator = new OffsetPaginator($this, $request, 50, ...$additionalArguments);
    
    $paginator->setLimitKeyName('top');
    $paginator->setOffsetKeyName('skip');
    $paginator->setTotalKeyName('count');
    $paginator->setCurrentOffset(1000);
    
    return $paginator;
} 
```

{% endtab %}

{% tab title="CursorPaginator" %}
The CursorPaginator requires you to define the request and the limit. This is required to calculate how many pages the Paginator needs to iterate through. For example, the API may only allow me to paginate over a maximum of 50 results.

```php
<?php

use Saloon\Http\Paginators\CursorPaginator;

public function paginate(Request $request, mixed ...$additionalArguments): CursorPaginator
{
    return new CursorPaginator(connector: $this, originalRequest: $request, perPage: 50, ...$additionalArguments);
} 
```

The cursor paginator works by looking for a `next_page_url` parameter in your response body and will attempt to find a `cursor` query parameter. It will then strip out just the `cursor` query parameter and will use that as the cursor string. If your cursor is implemented differently, you should extend the `CursorPaginator` and change the `getCursor` method.

```php
<?php

class CustomPaginator extends CursorPaginator
{
    public function getCursor(): string|int|null
    {
        return $this->currentResponse->json('meta.cursor');
    }
}
```

**Defaults**

The `CursorPaginator`has assumed the following items are present in your API:

* The limit query parameter is called `limit`
* The cursor query parameter is called `cursor`
* The next page URL is called `next_page_url` in the response
* The results count is called `total`
* The default cursor is `null`

**Configuring the CursorPaginator**

To configure the `CursorPaginator` you can use the following methods

```php
<?php

use Saloon\Http\Paginators\CursorPaginator;

public function paginate(Request $request, mixed ...$additionalArguments): CursorPaginator
{
    $paginator = new CursorPaginator($this, $request, 50, ...$additionalArguments);
    
    $paginator->setLimitKeyName('top');
    $paginator->setCursorKeyName('nextCursor');
    $paginator->setTotalKeyName('count');
    $paginator->setNextPageKeyName('meta.next');
    
    return $paginator;
} 
```

{% endtab %}
{% endtabs %}

### Iterating over responses and results

Now you have tested your request and configured the paginator, you're ready to give it for a spin! Saloon's paginators have many different ways to iterate over the results. The simplest way is using a foreach loop, however, you can use Laravel's Collections which helps you map and filter results really easily.

#### Foreach Loop

This is the simplest way to iterate over results. When you pass the paginator inside of a for-loop you will get each response.

```php
<?php

$connector = new SpotifyConnector;

$paginator = $connector->paginate(new LikedSongsRequest);

foreach($paginator as $response) {
    $pageData = $response->json();
}
```

#### Collection

{% hint style="info" %}
The `collect` method requires Laravel's `illuminate/collections` package.
{% endhint %}

Similar to a for-loop, you can use a collection to wrap around the results.

```php
$connector = new SpotifyConnector;

$paginator = $connector->paginate(new LikedSongsRequest);

$collection = $paginator->collect();

// Supports everything you expect!

$collection->map(...)->filter(...)
```

By default, Saloon's collect method will return a LazyCollection to be as memory efficient as possible. You may choose to use the `lazy: false` argument.

```php
$collection = $paginator->collect(lazy: false);
```

You may choose to iterate over the results inside of the collection instead of the responses. If you would like to do this, provide the JSON key of the results...

```php
$collection = $paginator->collect('results');

$collection->map(function (array $track) {
    // You now get access to the internal results of the collection!
});
```

When you provide a key to the `collect` method, Saloon will automatically collapse the collection. This means it reduces it to individual items. If this causes unexpected behaviour, you can disable collapsing with `collapse: false`.

```php
$collection = $paginator->collect('results', collapse: false);
```

#### JSON method

The JSON method allows you to iterate over the internal results of the paginator, like iterating over each of the tracks in the Spotify request. Just pass in the JSON property of the results.

```php
<?php

$paginator = $connector->paginate(new LikedSongsRequest);

foreach($paginator->json('results') as $tracks) {
    foreach($tracks as $track) {
        //
    }
}
```

### Asynchronous requests and pooling

Saloon also supports asynchronous requests and request concurrency/pooling with paginators. This is really exciting because you can make hundreds of API calls in a fraction of the time compared to sending normally. If you are unsure of how Saloon handles request concurrency, [read here](https://docs.saloon.dev/digging-deeper/concurrency-and-pools).

{% hint style="warning" %}
To use asynchronous requests/pooling, Saloon requires the API to return the "count" or "results" of the API. This is because asynchronous requests are not sent right away, Saloon does not know when to stop sending requests.
{% endhint %}

#### Asynchronous Requests

You may send requests asynchronously with the paginator by using the `async` method. When this is enabled, every response will be an instance of `PromiseInterface`.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$paginator = $connector->paginate(new LikedSongsRequest);
<strong>$paginator->async();
</strong>
foreach ($paginator as $promise) {
    // Handle $promise
}
</code></pre>

#### Request Concurrency/Pooling

You may also use pools with the paginator. This allows you to send requests concurrently. You should provide the concurrency integer and the response handler.

```php
<?php

$paginator = $connector->paginate(new LikedSongsRequest);

$pool = $paginator->pool(concurrency: 5, responseHandler: function (Response $response) use (): void {
    //
})->send()
```

[Click here](https://docs.saloon.dev/digging-deeper/concurrency-and-pools) to read more about request concurrency/pools.


# Pagination v2

### Introduction

When building API integrations, you may encounter a scenario where the server does not provide all the results in a single list. Instead, it divides the results into several pages. This strategy is called pagination, and integrating it into your application can be tedious and repetitive. With Saloon, you can install this pagination plugin to reduce the boilerplate code and iterate through every result across *every* page in one loop. **It's like magic!** Saloon has three paginators out of the box to support the most common types of pagination:

* **Paged pagination** - where you have to specify the next page as a query parameter (?page=1, ?page=2 and so on)
* **Limit/Offset pagination** - where you must specify two query parameters - a limit and an offset.
* **Cursor pagination** - where each page will return a "cursor" (a string) that you use to query the next page.

Saloon's paginators are custom [PHP iterators](https://www.php.net/manual/en/class.iterator.php)*,* meaning they can be used in for-loops. They are also memory efficient, so they only keep one page in memory at a time, this means you can use Saloon's paginators to iterate through **thousands of pages and millions of results without running out of memory**.

Additionally, Saloon's paginators have a few really useful methods to traverse through the results, like mapping them into Laravel's famous "Collection" class or simply an array of results. Here's how the pagination looks in your application:

```php
<?php

$spotifyConnector = new SpotifyConnector;

$likedSongs = $spotifyConnector->paginate(new GetLikedSongsRequest);

foreach($likedSongs->items() as $likedSong) {
    // e.g. Luke Combs - When It Rains It Pours 🎵
}
```

{% hint style="info" %}
Saloon's paginators are versatile and can work with any API format, including JSON, XML, and even multipart responses.
{% endhint %}

### Getting Started

Saloon's paginators are provided through a plugin. First, install the following package with Composer.

```sh
composer require saloonphp/pagination-plugin "^1.0"
```

### Making your requests pageable

Once the pagination plugin is installed, you must implement an interface on the requests that use pagination. This interface enables the paginator to decide whether or not to try pagination. It's important to do this as it avoids developer mistakes and potentially causing infinite loops.

All you need to do is add the `Paginatable` interface to your requests.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
<strong>use Saloon\PaginationPlugin\Contracts\Paginatable;
</strong>
<strong>class GetLikedSongsRequest extends Request implements Paginatable
</strong>{
    // ...
}
</code></pre>

After that, you need to add the `HasPagination` interface to your connector. This will require you to define a `paginate` method which we will implement next.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Connector;
use Saloon\PaginationPlugin\Paginator;
use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
<strong>    public function paginate(Request $request): Paginator
</strong>    {
        //
    }
}
</code></pre>

{% hint style="warning" %}
Take care when importing the pagination plugin classes when using Saloon v2. You'll want to make sure you are using the **Saloon\PaginationPlugin** namespace.
{% endhint %}

Now you need to choose a paginator that corresponds to the pagination type used by the third-party API. This documentation is divided into three sections based on Saloon's three pre-installed paginators.

{% content-ref url="pagination-v2/paged-pagination" %}
[paged-pagination](https://docs.saloon.dev/digging-deeper/pagination-v2/paged-pagination)
{% endcontent-ref %}

{% content-ref url="pagination-v2/limit-offset-pagination" %}
[limit-offset-pagination](https://docs.saloon.dev/digging-deeper/pagination-v2/limit-offset-pagination)
{% endcontent-ref %}

{% content-ref url="pagination-v2/cursor-pagination" %}
[cursor-pagination](https://docs.saloon.dev/digging-deeper/pagination-v2/cursor-pagination)
{% endcontent-ref %}

### Building Your Own Paginators

Sometimes an API may implement their own kind of pagination and Saloon might not have a pagination supported out of the box for it. You can create your own paginators really easily. Follow the link below for building your own paginators.

{% content-ref url="pagination-v2/custom-pagination" %}
[custom-pagination](https://docs.saloon.dev/digging-deeper/pagination-v2/custom-pagination)
{% endcontent-ref %}

### Using The Paginator

After you have configured your paginator, it's time to start using it! There are various ways to use the paginator, so we'll cover them in this part of the guide. First, let's take a look at how we instantiate our paginator. When we instantiate the paginator - Saloon won't make any requests. The requests are only made when we start iterating over the paginator.

```php
<?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);
```

As you can see, instantiating the paginator is as simple as running the method and passing in the paginatable request. We are now ready to start using the paginator.

#### Iterating Over Responses

The simplest way to start using the paginator is to use it inside of a `foreach` loop. This will return each page as a Saloon `Response` for you to access. Saloon will only keep one response in memory at a time so you won't have to worry about running out of memory.

```php
<?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);

foreach($paginator as $response) {
    $status = $response->status();
    $data = $response->body();
}
```

#### Iterating Over Items

One of the more exciting methods Saloon provides is the `items` method. This cool method will give you each item across multiple pages. With this, you can handle the individual items. This uses the `getPageItems` method which you defined earlier in your paginator.

```php
<?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);

foreach($paginator->items() as $song) {
    $songName = $song['name'];
    $songArtists = $song['artists'];
}
```

#### Using Laravel Collections (LazyCollection)

Another really cool method Saloon provides is the `collect` method. If you are using Laravel with Saloon, this method will work out of the box, but if you aren't using Laravel - you can add the support for this method by installing the "collections" library via Composer.

```bash
composer require illuminate/collections
```

This method will return a `LazyCollection` class. This class extends Laravel's base `Collection` class but works with custom iterators and keeps memory consumption low. The `LazyCollection` is really powerful when it comes to processing results from a paginated API. You can use the collection to filter, map, sort and much more all within just one iteration!

```php
<?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);
$collection = $paginator->collect();

$results = $collection
    ->filter(function (array $song) {
        return count($song['artists']) > 1;
    })
    ->map(function (array $song) {
        return $song['name'];
    })
    ->sort();
    
foreach($results as $song) {
    // $song: "When It Rains It Pours"
}
```

Even though in this example, we are doing multiple operations (filter, map, sort) the transformation will only happen when we iterate through the `LazyCollection` making it very performant and easy on system memory.

By default, Saloon will iterate through each result within each API page, but if you would rather have a collection that iterates through `Response` classes, you may do this too. Just set the `throughItems` argument to false.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);

<strong>$collection = $paginator->collect(throughItems: false);
</strong></code></pre>

{% hint style="info" %}
To see a full list of available methods on the `LazyCollection` you can [click here to read](https://laravel.com/docs/collections) the Laravel Documentation on Collections.
{% endhint %}

### Advanced Features

Saloon's paginators are a fantastic way of iterating through many page-separated results quickly and easily without writing boilerplate code in your application. So far we've covered the basics of pagination, but there's still plenty you can do. Within this section of the guide, we'll go through some more advanced features.

#### Casting Items Into DTOs

Saloon has a great way of casting responses into DTOs, but what if instead of getting the raw array or string item for each item in the paginator you wanted to resolve your DTO? You can simply use your `dto` or `dtoOrFail` method inside of the `getPageItems` method of your paginator. By using this method, you already have configured your DTO building logic on your connector. If you're not sure how to do this, [click here](https://docs.saloon.dev/the-basics/data-transfer-objects).

{% tabs %}
{% tab title="Paginator Config" %}

<pre class="language-php"><code class="lang-php">class SpotifyConnector extends Connector implements HasPagination
{
    public function paginate(Request $request): PagedPaginator
    {
        return new class(connector: $this, request: $request) extends PagedPaginator
        {
            // ...
            
            protected function getPageItems(Response $response, Request $request): array
            {
<strong>                return $response->dto();
</strong>            }
        }
    }
}
</code></pre>

{% endtab %}

{% tab title="Request" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

class GetLikedSongsRequest extends Request
{
    // {...}
    
    public function createDtoFromResponse(Response $response): array
    {
<strong>        return array_map(function (array $song) {
</strong><strong>            return Song::fromArray($song);
</strong><strong>        }, $response->json('items'));
</strong>    }
}
</code></pre>

{% endtab %}
{% endtabs %}

{% hint style="info" %}
The `createDtoFromResponse` method on your request/connector must return an array.
{% endhint %}

You can also implement the DTO conversion logic inside of the `getPageItems` method like below.

```php
protected function getPageItems(Response $response, Request $request): array
{
    return array_map(function (array $song) {
        return Song::fromArray($song);
    }, $response->json('items'));
}
```

#### Asynchronous Pagination / Pooling

You may prefer to send your requests asynchronously or use a request pool for better performance. To get started with this, you must implement a new method on your paginator called `getTotalPages`. This method is invoked after the first request and is used to determine how many additional requests Saloon needs to send.

Let's start with defining our `getTotalPages` method. Some APIs may not return this information, and unfortunately, if they do not return this information then you may not be able to do asynchronous pagination.

<pre class="language-php"><code class="lang-php">&#x3C;?php

class SpotifyConnector extends Connector implements HasPagination
{
    public function paginate(Request $request): PagedPaginator
    {
        return new class(connector: $this, request: $request) extends PagedPaginator
        {
            // ...
            
<strong>            protected function getTotalPages(Response $response): int
</strong>            {
<strong>                return $response->json('total_results');
</strong>            }
        }
    }
}
</code></pre>

Next, instantiate your paginator and use the `async` method to enable asynchronous pagination. Now when you iterate through results, you will get a `Promise` instance instead of a Saloon Response.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);

<strong>$paginator->async();
</strong>
<strong>foreach($paginator as $promise) {
</strong>    // $promise->then()
}
</code></pre>

You can also use a paginator pool. If you are unfamiliar with how asynchronous request pools work, [check out this section of the documentation](https://docs.saloon.dev/digging-deeper/concurrency-and-pools) first.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);

<strong>$pool = $paginator->pool(concurrency: 10);
</strong>
$pool->withResponseHandler(function (Response $response) { 
    // Handle each response

    $status = $response->status();
    $data = $response->body(); 
});

<strong>$pool->wait();
</strong></code></pre>

#### Custom Per-Request Pagination

Sometimes you may wish to use a different paginator on your request than your connector. For example, an API might not use the same pagination across all its endpoints. To get started with this - first, implement the `HasRequestPagination` interface on your request. This interface will expect you to define a `paginate` method on your request.

```php
<?php

use Saloon\PaginationPlugin\Contracts\HasRequestPagination;

class GetLikedSongsRequest extends Request implements HasRequestPagination
{
    // {...}
    
    public function paginate(Connector $connector): Paginator;
    {
        //
    }
}
```

This method works just like the `paginate` method on your connector, so follow the steps above to configure your paginator. The main difference is that this method expects a connector to be passed into the constructor and not a request.

```php
<?php

use Saloon\PaginationPlugin\Contracts\HasRequestPagination;

class GetLikedSongsRequest extends Request implements HasRequestPagination
{
    // {...}
    
    public function paginate(Connector $connector): Paginator;
    {
        return new class(connector: $connector, request: $this) extends PagedPaginator
        {
            protected function isLastPage(Response $response): bool
            {
                return is_null($response->json('next_page_url'));
            }
            
            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('items');
            }
        }
    }
}
```

Next, you can pass the connector into your request to instantiate the paginator.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$spotifyConnector = new SpotifyConnector;
$request = new GetLikedSongsRequest;

<strong>$paginator = $request->paginate($spotifyConnector);
</strong></code></pre>

#### Counting results

You may wish to count the pages of the paginator. You can use the `count()` PHP method to get the total number of pages from a paginator.&#x20;

<pre class="language-php"><code class="lang-php">&#x3C;?php

$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate(new GetLikedSongsRequest);

<strong>$pages = count($paginator);
</strong></code></pre>

{% hint style="warning" %}
You should use the `count()` method and not `iterator_count()` on the paginator because `iterator_count()` is not supported and you may get inaccurate results.
{% endhint %}

#### Handling Infinite Loops

While building your paginators, sometimes, you might run into a situation when the paginator is attempting to get the same page over and over again, causing an infinite loop. This is usually caused by mis-configuring the `isLastPage` method. To try to mitigate infinite loops, the paginator has a built-in safety feature that will throw an exception if the last five responses have exactly the same body.&#x20;

Saloon will throw a `PaginationException` with a message like:

> Potential infinite loop detected! The last 5 requests have had exactly the same body.

If you would like to disable this check, you can set the `detectInfiniteLoop` property on your paginator to false.

<pre class="language-php"><code class="lang-php">public function paginate(Connector $connector): Paginator;
{
    return new class(connector: $connector, request: $this) extends PagedPaginator
    {
<strong>        protected bool $detectInfiniteLoop = false;
</strong>    }
}
</code></pre>

{% hint style="warning" %}
Detecting infinite loops with asynchronous pagination is not supported.
{% endhint %}


# Paged Pagination

First, you will want to import the `PagedPaginator` class and return a new anonymous class that extends Saloon's `PagedPaginator`. This class expects the connector and request to be passed in via the constructor arguments.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
<strong>use Saloon\PaginationPlugin\PagedPaginator;
</strong>use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): PagedPaginator
    {
<strong>        return new class(connector: $this, request: $request) extends PagedPaginator
</strong>        {
            //
        };
    }
}
</code></pre>

{% hint style="info" %}
You don't need to use an anonymous class if it doesn't fit your code style. It is recommended to reduce the number of classes, but you can create your own pagination class that extends the base paginator if you prefer.
{% endhint %}

After you have defined your paginator class, you will be required to define two protected methods which are used to power the paginator. These methods are:

* **isLastPage** - This method is used to tell the paginator when to stop processing. Here you can use the response class provided to determine if you are on the last page. Some APIs may provide metadata like remaining results or next page URLs which you can use to check if you are on the last page. Additionally, Saloon has a few properties that can be used to determine if you are on the last page.
* **getPageItems** - This method is used to return the array of results inside of each page. This is used when using the `items` or `collect` method on your paginator class.

Let's implement these two methods on our paginator and dive into how it works.

```php
<?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
use Saloon\PaginationPlugin\PagedPaginator;
use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): PagedPaginator
    {
        return new class(connector: $this, request: $request) extends PagedPaginator
        {
            protected function isLastPage(Response $response): bool
            {
                return is_null($response->json('next_page_url'));
            }
            
            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('items');
            }
        };
    }
}
```

Let's assume in this hypothetical example that the API provides some useful information to see if we're on the last page. In this example, we know we're on the last page if the `next_page_url` JSON property on the body is null. There are various other ways of knowing if you are on the last page, so it's best to fully understand your API's pagination.&#x20;

To get the page items, we'll use the `json` method on the response to access the `items` array from the body.

### Specifying a default per-page on the paginator

The third-party API you are integrating with may require you to define a page size (per page) on your requests, or you may want to set a default page size used for every request. You can set this default page size as a property on the paginator class.&#x20;

<pre class="language-php"><code class="lang-php">public function paginate(Request $request): PagedPaginator
{
    return new class(connector: $this, request: $request) extends PagedPaginator
    {
<strong>        protected ?int $perPageLimit = 100;
</strong>        
        // ...
    }
}
</code></pre>

You may also use the `setPerPageLimit` method on an instantiated paginator if you want to configure it on the fly. **You must set this before iterating over the paginator.**&#x20;

```php
$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate($request);

$paginator->setPerPageLimit(250);

// foreach($paginator as $response) { ... }
```

### **Assumptions made with the PagedPaginator**

The PagedPaginator will apply pagination by sending two **query parameters**:

* page
* per\_page

This assumption might not be the way your third-party API works. You can really easily change this by extending the `applyPagination` method. For example, let's say our API expects a "currentPage" and a "pageSize" instead. We can simply extend the `applyPagination` method and change the keys. You can apply the pagination in whichever the API requires.

<pre class="language-php"><code class="lang-php">public function paginate(Request $request): PagedPaginator
{
    return new class(connector: $this, request: $request) extends PagedPaginator
    {
        protected function isLastPage(Response $response): bool
        {
            return is_null($response->json('next_page_url'));
        }
        
        protected function getPageItems(Response $response, Request $request): array
        {
            return $response->json('items');
        }
        
        protected function applyPagination(Request $request): Request
        {
<strong>            $request->query()->add('currentPage', $this->currentPage);
</strong>    
            if (isset($this->perPageLimit)) {
<strong>                $request->query()->add('pageSize', $this->perPageLimit);
</strong>            }
    
            return $request;
        }
    };
}
</code></pre>

### **Useful Properties On The PagedPaginator**

We previously mentioned that the PagedPaginator class provides a few methods that can be used to help with last-page calculation. These methods are:

* **totalResults** - An integer which will return the total number of items returned. This can be used to check if it's equal to the number of total results in the list.
* **currentPage** - An integer which will return the current page that the paginator is currently on.

### Next Steps

After configuring your paginator, head back to the [Using The Paginator](https://docs.saloon.dev/digging-deeper/pagination-v2/..#using-the-paginator) section of the documentation.


# Limit/Offset Pagination

First, you will want to import the `OffsetPaginator` class and return a new anonymous class that extends Saloon's `OffsetPaginator`. This class expects the connector and request to be passed in via the constructor arguments.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
<strong>use Saloon\PaginationPlugin\OffsetPaginator;
</strong>use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): OffsetPaginator
    {
<strong>        return new class(connector: $this, request: $request) extends OffsetPaginator
</strong>        {
            //
        };
    }
}
</code></pre>

{% hint style="info" %}
You don't need to use an anonymous class if it doesn't fit your code style. It is recommended to reduce the number of classes, but you can create your own pagination class that extends the base paginator if you prefer.
{% endhint %}

After you have defined your paginator class, you will be required to define a single property and two protected methods which are used to power the paginator. These properties/methods are:

* **perPageLimit** - This property is required as this is the default limit of the paginator. Since this paginator requires limit/offset - the limit is always required. If you need to change the limit on a per-request basis you can change it on the paginator instance.
* **isLastPage** - This method is used to tell the paginator when to stop processing. Here, you can use the response class provided to determine if you are on the last page. Some APIs may provide metadata like remaining results or next-page URLs, which you can use to check if you are on the last page. Additionally, Saloon has a few properties that can be used to determine if you are on the last page.
* **getPageItems** - This method is used to return the array of results inside of each page. This is used when using the `items` or `collect` method on your paginator class.

Let's implement the property and the two methods on our paginator and dive into how it works.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
use Saloon\PaginationPlugin\OffsetPaginator;
use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): OffsetPaginator
    {
        return new class(connector: $this, request: $request) extends OffsetPaginator
        {
<strong>            protected ?int $perPageLimit = 100;
</strong>        
<strong>            protected function isLastPage(Response $response): bool
</strong>            {
                return $this->getOffset() >= (int)$response->json('total');
            }
            
<strong>            protected function getPageItems(Response $response, Request $request): array
</strong>            {
                return $response->json('items');
            }
        };
    }
}
</code></pre>

First, we'll define a `perPageLimit` of 100. This means that every request will attempt to use a "limit" of 100.&#x20;

Next, let's assume in this hypothetical example that the API provides some useful information that helps us calculate if we're on the last page. For example, our API returns a `total` integer with every page - this number is simply the total results across all pages. We can use this to compare it with the current offset, and if the offset is greater than or equal to the total, we will stop. There are other ways of knowing if you are on the last page, so it's best to understand your API's pagination fully.

To get the page items, we'll use the `json` method on the response to access the `items` array from the body.

### Changing the per-page limit on the paginator

As mentioned previously, you must specify a "per-page limit" on your offset paginator. You may also use the `setPerPageLimit` method on an instantiated paginator if you want to change it on the fly. **You must set this before iterating over the paginator.**&#x20;

```php
$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate($request);

$paginator->setPerPageLimit(250);

// foreach($paginator as $response) { ... }
```

### **Assumptions made with the OffsetPaginator**

The OffsetPaginator will apply pagination by sending two **query parameters**:

* limit
* offset

This assumption might not be the way your third-party API works. You can really easily change this by extending the `applyPagination` method. For example, let's say our API expects the "top" and a "skip" query parameters instead. We can simply extend the `applyPagination` method and change the keys. You can apply the pagination in whichever the API requires.

<pre class="language-php"><code class="lang-php">public function paginate(Request $request): OffsetPaginator
{
    return new class(connector: $this, request: $request) extends OffsetPaginator
    {
        protected ?int $perPageLimit = 100;
    
        protected function isLastPage(Response $response): bool
        {
            return $this->getOffset() >= (int)$response->json('total');
        }
        
        protected function getPageItems(Response $response, Request $request): array
        {
            return $response->json('items');
        }
        
<strong>        protected function applyPagination(Request $request): Request
</strong>        {
            $request->query()->merge([
<strong>                'top' => $this->perPageLimit,
</strong><strong>                'skip' => $this->getOffset(),
</strong>            ]);
    
            return $request;
        }
    };
}
</code></pre>

{% hint style="info" %}
The `getOffset()` method is a useful helper that calculates the current offset for you based on current interation and the per-page limit.
{% endhint %}

### **Useful Methods/Properties On The OffsetPaginator**

We previously mentioned that the `OffsetPaginator` class provides a few methods that can be used to help with last-page calculation. These methods are:

* **totalResults** - An integer which will return the total number of items returned. This can be used to check if it's equal to the number of total results in the list.
* **page** - An integer which will return the current page that the paginator is currently on.
* **getOffset()** - A helpful method that calculates the current offset for you based on current interation and the per-page limit.

### Next Steps

After configuring your paginator, head back to the [Using The Paginator](https://docs.saloon.dev/digging-deeper/pagination-v2/..#using-the-paginator) section of the documentation.


# Cursor Pagination

First, you will want to import the `CursorPaginator` class and return a new anonymous class that extends Saloon's `CursorPaginator`. This class expects the connector and request to be passed in via the constructor arguments.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
<strong>use Saloon\PaginationPlugin\CursorPaginator;
</strong>use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): CursorPaginator
    {
<strong>        return new class(connector: $this, request: $request) extends CursorPaginator
</strong>        {
            //
        };
    }
}
</code></pre>

{% hint style="info" %}
You don't need to use an anonymous class if it doesn't fit your code style. It is recommended to reduce the number of classes, but you can create your own pagination class that extends the base paginator if you prefer.
{% endhint %}

After you have defined your paginator class, you will be required to define three protected methods which are used to power the paginator. These methods are:

* **getNextCursor** - This method is used to find the next cursor from the response. This method must return either an integer or a string. The next cursor is used in the next request to get a different page.
* **isLastPage** - This method is used to tell the paginator when to stop processing. Here you can use the response class provided to determine if you are on the last page. Some APIs may provide metadata like remaining results or next page URLs which you can use to check if you are on the last page. Additionally, Saloon has a few properties that can be used to determine if you are on the last page.
* **getPageItems** - This method is used to return the array of results inside of each page. This is used when using the `items` or `collect` method on your paginator class.

Let's implement these three methods on our paginator and dive into how it works.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
use Saloon\PaginationPlugin\CursorPaginator;
use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): CursorPaginator
    {
        return new class(connector: $this, request: $request) extends CursorPaginator
        {
<strong>            protected function getNextCursor(Response $response): int|string
</strong>            {
                return $response->json('next_cursor');
            }
        
<strong>            protected function isLastPage(Response $response): bool
</strong>            {
                return is_null($response->json('next_cursor'));
            }
            
<strong>            protected function getPageItems(Response $response, Request $request): array
</strong>            {
                return $response->json('items');
            }
        };
    }
}
</code></pre>

Let's assume in this hypothetical example that the API provides a `next_cursor` property in the JSON response. We'll return the value of this property in the `getNextCursor` method. After that, we need to tell Saloon when to stop paginating. We'll use the same `next_cursor` property again. If it's null, we know that there isn't another page to look up. There are various other ways of knowing if you are on the last page, so it's best to fully understand your API's pagination.

To get the page items, we'll use the `json` method on the response to access the `items` array from the body.

### Specifying a default per-page on the paginator

The third-party API you are integrating with may require you to define a page size (per page) on your requests, or you may want to set a default page size used for every request. You can set this default page size as a property on the paginator class.&#x20;

<pre class="language-php"><code class="lang-php">public function paginate(Request $request): CursorPaginator
{
    return new class(connector: $this, request: $request) extends CursorPaginator
    {
<strong>        protected ?int $perPageLimit = 100;
</strong>        
        // ...
    }
}
</code></pre>

You may also use the `setPerPageLimit` method on an instantiated paginator if you want to configure it on the fly. **You must set this before iterating over the paginator.**&#x20;

```php
$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate($request);

$paginator->setPerPageLimit(250);

// foreach($paginator as $response) { ... }
```

### **Assumptions made with the CursorPaginator**

The CursorPaginator will apply pagination by sending two **query parameters**:

* cursor
* per\_page

This assumption might not be the way your third-party API works. You can really easily change this by extending the `applyPagination` method. For example, let's say our API expects a "pageCursor" and a "pageSize" instead. We can simply extend the `applyPagination` method and change the keys. You can apply the pagination in whichever the API requires.

<pre class="language-php"><code class="lang-php">public function paginate(Request $request): CursorPaginator
{
    return new class(connector: $this, request: $request) extends CursorPaginator
    {
        protected function getNextCursor(Response $response): int|string
        {
            return $response->json('next_cursor');
        }
    
        protected function isLastPage(Response $response): bool
        {
            return is_null($response->json('next_cursor'));
        }
        
        protected function getPageItems(Response $response, Request $request): array
        {
            return $response->json('items');
        }
        
        protected function applyPagination(Request $request): Request
        {
            if ($this->currentResponse instanceof Response) {
<strong>                $request->query()->add('pageCursor', $this->getNextCursor($this->currentResponse));
</strong>            }
    
            if (isset($this->perPageLimit)) {
<strong>                $request->query()->add('pageSize', $this->perPageLimit);
</strong>            }
    
            return $request;
        }
    };
}
</code></pre>

### **Useful Properties On The CursorPaginator**

We previously mentioned that the CursorPaginator class provides a few methods that can be used to help with last-page calculation. These methods are:

* **totalResults** - An integer which will return the total number of items returned. This can be used to check if it's equal to the number of total results in the list.
* **page** - An integer which will return the current page that the paginator is currently on.

### Next Steps

After configuring your paginator, head back to the [Using The Paginator](https://docs.saloon.dev/digging-deeper/pagination-v2/..#using-the-paginator) section of the documentation.


# Custom Pagination

### Introduction

If you are building an integration with a third-party API that does not use paged, limit/offset or cursor pagination, you can build your own paginator. Saloon's base paginator class has been designed to be flexible and easy to build against.

### Getting Started

First, you will want to import the `Paginator` abstract class and return a new anonymous class that extends Saloon's `Paginator`. This class expects the connector and request to be passed in via the constructor arguments.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
<strong>use Saloon\PaginationPlugin\Paginator;
</strong>use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends Paginator
        {
            //
        };
    }
}
</code></pre>

{% hint style="info" %}
You don't need to use an anonymous class if it doesn't fit your code style. It is recommended to reduce the number of classes, but you can create your own pagination class that extends the base paginator if you prefer.
{% endhint %}

After you have defined your paginator class, you will be required to define three protected methods which are used to power the paginator. These methods are:

* **isLastPage** - This method is used to tell the paginator when to stop processing. Here you can use the response class provided to determine if you are on the last page. Some APIs may provide metadata like remaining results or next page URLs which you can use to check if you are on the last page. Additionally, Saloon has a few properties that can be used to determine if you are on the last page.
* **getPageItems** - This method is used to return the array of results inside of each page. This is used when using the `items` or `collect` method on your paginator class.
* **applyPagination** - This method is used to change each request to apply the pagination for the next page.

Let's implement these three methods on our paginator and dive into how it works.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Http\Connector;
<strong>use Saloon\PaginationPlugin\Paginator;
</strong>use Saloon\PaginationPlugin\Contracts\HasPagination;

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends Paginator
        {
            protected function isLastPage(Response $response): bool
            {
<strong>                return is_null($response->json('next_page_url'));
</strong>            }
            
            protected function getPageItems(Response $response, Request $request): array
            {
<strong>                return $response->json('items');
</strong>            }
            
            protected function applyPagination(Request $request): Request
            {
<strong>                $request->header()->add('X-Page', $this->currentPage);
</strong><strong>                
</strong><strong>                $request->header()->add('X-Per-Page', $this->perPageLimit);
</strong>            }
        };
    }
}
</code></pre>

Let's assume in this hypothetical example that the API provides some useful information to see if we're on the last page. In this example, we know we're on the last page if the `next_page_url` JSON property on the body is null. There are various other ways of knowing if you are on the last page, so it's best to fully understand your API's pagination.&#x20;

To get the page items, we'll use the `json` method on the response to access the `items` array from the body.

After that, to apply the pagination, our API expects us to send a `X-Page` header with the current page on the paginator. We'll use the `$this->currentPage` property on the paginator as it acts like an index for the current iteration of the paginator. We will also make sure to send the `perPageLimit` to our API with the `X-Per-Page` header. This header is optional depending on the third-party API you are integrating with.

### Specifying a default per-page on the paginator

The third-party API you are integrating with may require you to define a page size (per page) on your requests, or you may want to set a default page size used for every request. You can set this default page size as a property on the paginator class.&#x20;

<pre class="language-php"><code class="lang-php">public function paginate(Request $request): Paginator
{
    return new class(connector: $this, request: $request) extends Paginator
    {
<strong>        protected ?int $perPageLimit = 100;
</strong>        
        // ...
    };
}
</code></pre>

You may also use the `setPerPageLimit` method on an instantiated paginator if you want to configure it on the fly. **You must set this before iterating over the paginator.**&#x20;

```php
$spotifyConnector = new SpotifyConnector;

$paginator = $spotifyConnector->paginate($request);

$paginator->setPerPageLimit(250);

// foreach($paginator as $response) { ... }
```

{% hint style="warning" %}
For this feature to work, you must implement sending the `perPageLimit` inside of your `applyPagination` method in whichever way your API may expect.
{% endhint %}

### Next Steps

After configuring your paginator, head back to the [Using The Paginator](https://docs.saloon.dev/digging-deeper/pagination-v2/..#using-the-paginator) section of the documentation.


# Retrying Requests

Sometimes you may deal with APIs that fail frequently or require you to retry multiple times before a request is successful. Saloon has a useful built-in feature that allows you to send a request and retry multiple times.&#x20;

This method has been heavily inspired by Laravel's [excellent retrying functionality](https://laravel.com/docs/9.x/http-client#retries) for their built-in HTTP client.

### Getting Started

Saloon has a built-in method on the connector called `sendAndRetry` . This method accepts the maximum number of attempts Saloon should make and an optional interval between requests.

```php
<?php

$forge = new ForgeConnector;

$response = $forge->sendAndRetry(new GetServersRequest, 3, 100);
```

If a request fails, it will be attempted again - if it reaches the maximum number of errors, a `RequestException` will be thrown with the last exception that happened. If a connection error happens, it will throw a `FatalRequestException`. If a request is successful at any point, it will return a `Response` instance.

{% hint style="info" %}
Currently the `sendAndRetry` the method only works with synchronous requests because it requires a response to come back to determine if it was successful.&#x20;
{% endhint %}

### Customising when a retry is attempted

By default, Saloon uses the `throw` method on the response. If it throws an exception, it will be retried. Read more about how this method works [here](https://docs.saloon.dev/the-basics/handling-failures). You may choose to use the fourth argument `handleRetry` to provide a closure that should return a boolean if true, Saloon will make the next attempt. For example, you may only want to retry if a `FatalRequestException` happens, which means it likely couldn't connect to the service.

```php
<?php

$forge->sendAndRetry(new GetServersRequest, 3, 100, function ($exception) {
    return $exception instanceof FatalRequestException;
});
```

If a request fails, you may also choose to use this method to change the next request that is sent. For example, it may have failed because of an expired authentication token. The second argument of the `handleRetry` method provides you with the next `PendingRequest` instance.

```php
<?php

$forge->sendAndRetry(new GetServersRequest, 3, 100, function ($exception, $pendingRequest) {
    if (! $exception instanceof RequestException || $exception->getResponse()->status() !== 401) {
        return false;
    }
    
    $pendingRequest->authenticate($this->refreshAccessToken());
    
    return true;
});
```

{% hint style="danger" %}
When modifying the PendingRequest,  Request middleware cannot be added to a because the PendingRequest has already run the middleware pipeline. Additionally, you should take care when debugging because the debugger will log the PendingRequest before it is modified by the sendAndRetry method.
{% endhint %}

### Disabling throwing exceptions

By default, Saloon will throw an exception if all the attempts are made and every attempt was unsuccessful. You may choose to disable this functionality and always return a failed response by using the `throw` argument.

```php
<?php

$response = $forge->sendAndRetry(new GetServersRequest, 3, 100, throw: false);
```

{% hint style="info" %}
Note that if a connection failure happens, Saloon will still throw a `FatalRequestException` as there will be no response to return.
{% endhint %}


# Concurrency & Pools

Saloon supports request concurrency and requests pools out of the box. This allows you to make multiple API calls to the same service while keeping the CURL connection open. Under the hood, it uses curl's multi-handler to keep the connection open, this results in huge speed benefits when making lots of API calls. Saloon's concurrency is powered by [Guzzle's implementation](https://docs.guzzlephp.org/en/stable/quickstart.html?highlight=pool#concurrent-requests) behind the scenes.

One of Laravel's core members, Nuno Maduro [wrote a great blog post](https://nunomaduro.com/speed_up_your_php_http_guzzle_requests_with_concurrency) about request concurrency and its performance with Guzzle directly. The same performance can be shared with Saloon's implementation as Saloon uses Guzzle behind the scenes.

{% hint style="info" %}
Concurrency is only supported with the `GuzzleSender` and `HttpSender` senders for Saloon. The default sender out of the box with Saloon is the GuzzleSender.
{% endhint %}

### Getting Started

Saloon's pooling has been designed specifically to be easy to use. Simply instantiate your connector class and use the `pool` method to create a pool. This method doesn't require any arguments, but you may provide requests, concurrency and handlers right from this method.

```php
<?php

$forge = new ForgeConnector;

// Pool has various optional parameters if you want to set them here...

$pool = $forge->pool(
    requests: [],
    concurrency: 5,
    responseHandler: function () { ... },
    exceptionHandler: function () { ... },
);

// Initiate the transfer of requests

$promise = $pool->send();

// Force all the requests to be fulfilled

$promise->wait();
```

### Available Methods

* setRequests(callable|iterable $requests)
* getRequests()
* withResponseHandler(callable $callable)
* withExceptionHandler(callable $callable)
* send()

### Providing Requests

The pool class accepts many types of requests:

* Array of requests
* PHP generator providing requests
* Closure or Invokable class returning an array of requests
* Closure or Invokable class returning a generator

You may provide these requests into the pool either as the first argument of the `pool` method or using the `setRequests` method. The requests can be instances of `Saloon\Contacts\Request` or `Saloon\Contracts\PendingRequest`.

#### Array of requests&#x20;

The simplest way to provide requests to the pool is an array.&#x20;

```php
<?php

$forge = new ForgeConnector;

$forge->pool([
    new GetServersRequest,
    new GetSitesRequest,
    new GetUserRequest,
]);

// Or 

$pool = $forge->pool();

$pool->setRequests([
    new GetServersRequest,
    new GetSitesRequest,
    new GetUserRequest,
]);
```

#### Using a PHP generator

If you are going to send lots of requests you may wish to provide a generator into the pool. This allows you to keep memory consumption low and potentially send hundreds of requests.&#x20;

```php
<?php

$generatorCallback = function (): Generator {
    for ($i = 0; $i < 3; $i++) {
        yield $i => new UserRequest;
    }
};

$forge = new ForgeConnector;
$pool = $forge->pool($generatorCallback);

// or

$pool->setRequests($generatorCallback);
```

#### Using a callable or invokable class

You may wish to use a callable method to return an array of requests or a generator, this is useful if you have any additional logic that needs to execute just before the request pool begins.

{% tabs %}
{% tab title="Callable" %}

```php
<?php

$callback = function () {
    return [
        new GetServersRequest,
        new GetSitesRequest,
        new GetUserRequest,
    ];
};

$forge = new Forge;
$pool = $forge->pool($callback);

// or

$pool->setRequests($callback);
```

{% endtab %}

{% tab title="Invokable Class" %}

```php
<?php

class PoolClass {
    public function __invoke() {
        return [
            new GetServersRequest,
            new GetSitesRequest,
            new GetUserRequest,
        ];
    }
}

$forge = new ForgeConnector;
$pool = $forge->pool(new PoolClass);
```

{% endtab %}
{% endtabs %}

### Concurrency

By default, Saloon will send up to 5 requests concurrently. You can customise the concurrency that is used by pools with the `setConcurrency` method. It accepts an integer or a callable like a method or an invokable class in case you want to write advanced logic to determine the concurrency.&#x20;

```php
<?php

$forge = new ForgeConnector;
$pool = $forge->pool(...);

$pool->setConcurrency(10);

// or

$pool->setConcurrency(function () {
    return 10;
});
```

### Response Handlers

When you send requests with pooling, each request is asynchronous, so you cannot guarantee when they are returned. In order to handle the response, Saloon has provided you with two handlers to handle successful requests and failed requests.

#### Handling Successful Requests

Any responses that are successful will be handled by the response handler. You may add this with the constructor of your pool or add it once it is created. You can only have one handler. You will get a response instance.

```php
<?php

use Saloon\Contracts\Response;

$pool = $forge->pool(
    requests: [],
    concurrency: 5,
    responseHandler: function (Response $response) {
        // Handle Response
    },
);

// Or

$pool->withResponseHandler(function (Response $response) {
    // Handle Response
});
```

#### Handling Failed Requests

When requests fail, they will always be caught with the error handler, even if you don't throw on requests. When a failed request happens you can handle the exception with the `withExceptionHandler` method.

```php
<?php

use Saloon\Contracts\Response;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Exceptions\Request\FatalRequestException;

$pool = $forge->pool(
    requests: [],
    concurrency: 5,
    exceptionHandler: function (FatalRequestException|RequestException $exception) {
        // Handle Exception
    },
);

// Or

$pool->withExceptionHandler(function (FatalRequestException|RequestException $exception) {
    // Handle Exception
});
```

### Named/Keyed Requests

Saloon also supports keyed responses to help you easily track exact requests that have been sent. This is especially useful if you are sending requests to different endpoints. You may used keying with arrays or even with a generator.

```php
<?php

$pool = $forge->pool([
    'servers' => new GetServersRequest,
    'sites' => new GetSitesRequest,
    'user' => new GetUserRequest,
]);

// You may access the key in the response and error handlers

$pool->withResponseHandler(function (Response $response, string $key) {
    match($key) {
        'servers' => $this->updateServersList($response),
        'sites' => $this->updateSitesList($response),
        'user' => $this->updateUser($response),
    }
});
```

### Sending

Once you have provided the pool with requests, you are ready to send them. Just use the `send` method on the pool. This will return an instance of `PromiseInterface` . Requests will be handled asynchronously but you can force them to complete with the `wait` method.

```php
<?php

$forge = new ForgeConnector;

$pool = $forge->pool([
    new GetServersRequest,
    new GetSitesRequest,
    new GetUserRequest,
]);

$pool->withResponseHandler(function (Response $response) {
    // Handle Response
});

$pool->withExceptionHandler(function (FatalRequestException|RequestException $exception) {
    // Handle Exception
});

// Initiate the transfer of requests

$promise = $pool->send();

// Force all the requests to be fulfilled

$promise->wait();
```


# OAuth2 Authentication

Saloon supports some OAuth2 authentication grants out of the box which you can use in your application. They come in the form of traits that can be easily added to your connector and then configured to your needs. Read more below by finding the grant type you would like to use

### Authorization Code Grant

This flow is the typical process where your application redirects your users to a third party to approve your authentication request. After they have approved the authentication request, they will be redirected back to your application where you can generate access tokens.

{% content-ref url="oauth2-authentication/oauth2-authentication" %}
[oauth2-authentication](https://docs.saloon.dev/digging-deeper/oauth2-authentication/oauth2-authentication)
{% endcontent-ref %}

### Client Credentials Grant

This flow is often used for internal server-to-server authentication. Like a shopping website communicating with an internal warehouse API. You are often given a single access token which can be used to generate access tokens.

{% content-ref url="oauth2-authentication/client-credentials-grant" %}
[client-credentials-grant](https://docs.saloon.dev/digging-deeper/oauth2-authentication/client-credentials-grant)
{% endcontent-ref %}


# Authorization Code Grant

Some API providers implement the OAuth 2 *Authorization Code Flow* for authentication. Implementing this grant type every time you create a new API integration can be tedious and time-consuming. Saloon offers a simple, extendable OAuth2 trait to help you get up and running quickly.

### Prerequisites

This section of the documentation assumes that you are familiar with OAuth2 and specifically the *Authorization Code Grant*. If you are not familiar with how this grant type works, [Auth0 has a great explanation on its website.](https://auth0.com/docs/get-started/authentication-and-authorization-flow/authorization-code-flow)

### Flow Example

Saloon has provided methods for the full Authorization Code grant.

```php
$connector = new SpotifyConnector;

// 1. Redirect the user to the authorization URL...

$authorizationUrl = $connector->getAuthorizationUrl($scopes, $state);

// 2. Handle the callback from the API provider and create an access token...

$authenticator = $connector->getAccessToken($code, $state);

// 3. Authenticate the connector

$connector->authenticate($authenticator);

// 4. Send your requests

$connector->send(new GetTracksRequest);

// 5. Refresh your access tokens...

$newAuthenticator = $connector->refreshAccessTokens($authenticator);
```

### Getting Started

Let’s start with preparing our connector to support the Authorization Code Flow. All we have to do is add the `AuthorizationCodeGrant` trait to our connector.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyConnector extends Connector;
{
    use AuthorizationCodeGrant;
}
```

After you have added the trait, you will need to tell Saloon how to authenticate with your API. First, extend the `defaultOauthConfig` method and use the methods to define your client ID, secret and redirect URI. Saloon also has sensible defaults set for the authorization and token endpoints, but you may customize them if you need to. For example, Spotify has a different base URL than the connector's base URL, so we have overwritten it in this example.

You can also provide default scopes and even provide a callback to modify the OAuth2 requests being sent.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Request;
use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyConnector extends Connector
{
    use AuthorizationCodeGrant;

    public function resolveBaseUrl(): string
    {
        // Spotify's API has a different base URL for OAuth2 auth.
    
        return 'https://api.spotify.com/v1';
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId('my-client-id')
            ->setClientSecret('my-client-secret')
	    ->setDefaultScopes(['user-read-currently-playing'])
            ->setRedirectUri('https://my-app.saloon.dev/auth/callback')
	    ->setAuthorizeEndpoint('https://accounts.spotify.com/authorize')
            ->setTokenEndpoint('https://accounts.spotify.com/api/token')
            ->setUserEndpoint('/me')
            ->setRequestModifier(function (Request $request) {
                // Optional: Modify the requests being sent.
            })
    }
}
```

{% hint style="info" %}
Each of the endpoint methods, like `setAuthorizeEndpoint`on the OAuthConfig class support full URLs if you need to overwrite the base URL on the connector however you may just use the endpoint if the base URL is the same.
{% endhint %}

#### Overwriting the OAuth2 config

Sometimes, you may have a different OAuth2 client ID and secret for each user of your application. If your OAuth2 config is dependent on a per-user/tenant basis, it's recommended that you pass in the credentials as constructor arguments of your connector and then set the `oauthConfig` inside the constructor.

In the following example, I will pass in the `$clientId` and the `$clientSecret` as constructor arguments and overwrite the OAuth2 config.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyConnector extends Connector
{
    use AuthorizationCodeGrant;
    
<strong>    public function __construct(string $clientId, string $clientSecret)
</strong>    {
        $this->oauthConfig()->setClientId($clientId);
        $this->oauthConfig()->setClientSecret($clientSecret);
    }
    
    // ...
}
</code></pre>

### Creating an Authorization URL

Now we have setup our connector to support the authorization code grant, we are ready to start the OAuth2 process. Usually, the first stage is to generate a URL to redirect our application users to. To generate an authorization URL, you can use the `getAuthorizationUrl` method on the connector.

```php
<?php

$connector = new SpotifyConnector;
$authorizationUrl = $connector->getAuthorizationUrl();
```

You can also pass in scopes which will be merged with the default scopes if you provided them in the OAuth config. Saloon will separate scopes with spaces but if your API integration requires scopes to be separated any other way, you can specify this with the `scopeSeparator` argument.

```php
$authorizationUrl = $connector->getAuthorizationUrl(
    scopes: ['user-library-read'],
    scopeSeparator: '+',
);
```

You can also provide additional query parameters if you need to with the `additionalQueryParameters` argument. This should be a key-value array where the key is the query parameter name and the value is the value of the query parameter.

```php
$authorizationUrl = $authConnector->getAuthorizationUrl(
    additionalQueryParameters: [
        'username' => 'JohnWayne'
    ],
);
```

#### State

To help prevent CSRF attacks or send additional data during authentication, you can pass an additional unique string in your authorization URL that the API's OAuth2 server will send back to you after your user has approved or denied access to your OAuth2 app.

```php
$authorizationUrl = $authConnector->getAuthorizationUrl(
    state: 'application-user-id',
);
```

If you do not provide your own state, Saloon will automatically generate a unique, 32-character string. Once you have generated the authorization URL, you can then use the `getState` method on your connector to get the state back. You should store this string in your application's session or cache to be verified later.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$connector = new SpotifyConnector;
$authorizationUrl = $connector->getAuthorizationUrl();

<strong>$state = $authConnector->getState(); // '8484b43fdjfdnfdj3llls...'
</strong></code></pre>

### Creating Access Tokens

After the user has approved your application, the API provider will redirect you back to your application with an authorization code and state. This data usually sent in the form of query parameters should be passed into your `getAccessToken` method on your connector. If successful, the method will return an `AccessTokenAuthenticator`. The access token, refresh token and expiry are wrapped up in a [Saloon Authenticator](https://docs.saloon.dev/the-basics/authentication#custom-authenticators) class that can be used to authenticate your connector/requests. It acts like a DTO that can be easily serialized and transported around your application.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$connector = new SpotifyConnector;
<strong>$authenticator = $authConnector->getAccessToken($code);
</strong>
// Use authenticator to authenticate your connector instance

$connector->authenticate($authenticator);

// Any request sent through this connector will now have authentication applied

$connector->send(new GetTracksRequest);
</code></pre>

{% hint style="info" %}
Once you have received the authenticator instance, you should cache it securely in your application for future use. Read further to see how you can do this.
{% endhint %}

#### Verifying State

If you stored the state that was generated during creating an authorization URL, you should pass this expected state alongside the state sent back by the API provider's OAuth2 server. This will be used to verify the state provided back by the application is valid. If the state does not match the expected state, Saloon will throw an exception.

```php
<?php

$authConnector = new SpotifyConnector;

// It will throw an exception if the state and expected state don't match
// however both must be present

$authenticator = $authConnector->getAccessToken($code, $state, $expectedState);
```

### Storing Authentication For Later

You will likely need to store the authenticator securely so you can use it for future requests. You may serialize and unserialize the authenticator class using the helper methods below, then you can store the string wherever you like, usually encrypted in the database if it's against a user. Then, you can retrieve this authenticator and use it to authenticate your connector.

```php
<?php

$connector = new SpotifyConnector;
$authenticator = $connector->getAccessToken($code);

// Securely store this against your user.

$serialized = $authenticator->serialize(); 

// Unserialize the authenticator when retrieving it

$authenticator = AccessTokenAuthenticator::unserialize($serialized);
```

### Authenticator Methods

The authenticator returned by Saloon when using the `getAccessToken` or `refreshAccessToken` methods will contain the Access Token, Refresh Token and Expiry Date that was returned by the OAuth2 server. You can access these properties with the following methods. You can also check if the authenticator has expired, which will come in handy when refreshing access tokens.

```php
<?php

$authenticator->getAccessToken();
$authenticator->getRefreshToken();
$authenticator->getExpiry();

$authenticator->hasExpired();
$authenticator->hasNotExpired();
```

### Refreshing Access Tokens

When retrieving your authenticator out of storage, you should always check if the access token has expired and if it needs refreshing. If the authenticator's access token has expired, you can call the `refreshAccessToken` method which will create a fresh authenticator.

In this example, `$user` is the user of my application and I have written methods to get and store the authenticators.

```php
<?php

// $user in this example is my application's user

$authenticator = $user->getCachedAuthenticator();
$connector = new SpotifyConnector;

if ($authenticator->hasExpired()) {
    // We'll refresh the access token which will return a new authenticator
    // which we can store against our user in our application.

    $authenticator = $connector->refreshAccessToken($authenticator);    
    $user->updateAuthenticator($authenticator);
}

// Authenticate our connector and send the request

$connector->authenticate($authenticator);

$response = $connector->send(new GetTracksRequest);
```

{% hint style="info" %}
If you are using Laravel and the Saloon Laravel library, you can use built-in `EncryptedOAuthAuthenticatorCast` **/** `OAuthAuthenticatorCast` Eloquent casts to automatically cast the authenticator for storing in your database.
{% endhint %}

### Customising The Authenticator

Sometimes the API provider you are authenticating with may require additional information to be used in the authenticator. You can customise how the authenticator will be created by extending the `createAccessTokenAuthenticator` method on your connector.

```php
<?php

protected function createOAuthAuthenticator(string $accessToken, string $refreshToken, DateTimeImmutable $expiresAt): OAuthAuthenticatorInterface
{
    return new SpotifyAuthenticator($accessToken, $refreshToken, $expiresAt);
}
```

### Customising How The Authenticator Is Created

Sometimes the API provider you are authenticating with may have a different way that they respond with their tokens. If you need to customise the way Saloon creates the authenticator you can extend the `createOAuthAuthenticatorFromResponse` method.

```php
<?php

protected function createOAuthAuthenticatorFromResponse(SaloonResponse $response, string $fallbackRefreshToken = null): OAuthAuthenticatorInterface
{
    $responseData = $response->object();

    $accessToken = $responseData->access_token;
    $refreshToken = $responseData->refresh_token ?? $fallbackRefreshToken;
    $expiresAt = new DateTimeImmutable('+' . $responseData->expires_in . ' seconds');

    return $this->createOAuthAuthenticator($accessToken, $refreshToken, $expiresAt);
}
```

### Customising The Requests

Sometimes you might integrate with an API that requires additional query parameters or headers to be sent with the OAuth2 flow. You may use the `requestModifier` property on the methods or use the `setRequestModifier` method within the `OAuthConfig` to add a callable that is invoked before a request is sent.

{% tabs %}
{% tab title="Per Request" %}

```php
<?php

$connector->getAccessToken($code, requestModifier: function (Request $request) {
    $request->query()->add('access_type', 'offline');
});

$connector->getRefreshToken($code, requestModifier: function (Request $request) {
    $request->headers()->add('X-App-Key', $appKey);
});

$connector->getUser($code, requestModifier: function (Request $request) {
    $request->headers('Accept', 'text/plain');
});
```

{% endtab %}

{% tab title="All Requests" %}

```php
<?php

use Saloon\Contracts\Request;
use Saloon\Http\OAuth2\GetUserRequest;
use Saloon\Http\OAuth2\GetAccessTokenRequest;
use Saloon\Http\OAuth2\GetRefreshTokenRequest;

protected function defaultOauthConfig(): OAuthConfig
{
    return OAuthConfig::make()
        ->setClientId('my-client-id')
        ->setClientSecret('my-client-secret')
        ->setRedirectUri('https://my-app.saloon.dev/auth/callback')
	->setRequestModifier(function (Request $request) {
	     // This callback is invoked on every request, so you 
	     // may want to use if-statements or a match statement
	     // to apply conditions based on request.
	
             if ($request instanceof GetAccessTokenRequest) {
                 $request->query()->add('access_type', 'offline');
             }
             
             if ($request instanceof GetRefreshTokenRequest) {
                 $request->headers()->add('X-App-Key', $appKey);
             }
             
             if ($request instanceof GetUserRequest) {
                 $request->headers('Accept', 'text/plain');
             }
        }),
}
```

{% endtab %}
{% endtabs %}

### Using your own request classes

There are situations where Saloon's own request classes for getting the access token, refreshing the access token or getting the user might not suit the API you are integrating with. For example, if an API uses JSON encoding instead of form encoding. You may use the following methods on your connector to overwrite the instantiation process of the request classes.

```php
<?php

class SpotifyConnector extends Connector
{
    // ...

    protected function resolveAccessTokenRequest(string $code, OAuthConfig $oauthConfig): Request
    {
        return new CustomGetAccessTokenRequest($code, $oauthConfig);
    }
    
    protected function resolveRefreshTokenRequest(OAuthConfig $oauthConfig, string $refreshToken): Request
    {
        return new CustomGetRefreshTokenRequest($oauthConfig, $refreshToken);
    }
    
    protected function resolveUserRequest(OAuthConfig $oauthConfig): Request
    {
        return new CustomGetUserRequest($oauthConfig);
    }
}
```

### Returning Responses

If you prefer, you may request Saloon to return a `Saloon\Http\Resonse` instance instead of a `AccessTokenAuthenticator` when creating or refreshing access tokens. To use responses, just provide the `returnResponse` argument when creating or refreshing access tokens.

```php
<?php

$connector = new SpotifyConnector;

$response = $connector->getAccessToken(
    returnResponse: true,
);

$response = $connector->refreshAccessToken(
    returnResponse: true,
);
```

### Real-world example

If you would like to see an example integration using the OAuth2 methods mentioned above, you can see the following Laravel application.

<https://github.com/Sammyjo20/saloon-v2-spotify-example>


# Client Credentials Grant

Some API providers implement the OAuth2 *Client Credentials Grant* for authentication. Implementing this grant type every time you create a new API integration can be tedious and time-consuming. Saloon offers a simple, extendable OAuth2 trait to help you get up and running quickly.

### Prerequisites

This section of the documentation assumes that you are familiar with OAuth2 and specifically the *Client Credentials Grant*. If you are not familiar with how this grant type works, [Auth0 has a great explanation on its website.](https://auth0.com/docs/get-started/authentication-and-authorization-flow/client-credentials-flow)

### Flow Example

Saloon has provided methods for the full client credentials grant.

```php
$connector = new WarehouseConnector;

// 1. Create an access token authenticator

$authenticator = $connector->getAccessToken($scopes);

// 2. Authenticate the connector

$connector->authenticate($authenticator);

// 3. Send your requests

$connector->send(new GetInventoryRequest);
```

### Getting Started

Let's start with preparing our connector to support the client credentials grant. All we have to do is add the `ClientCredentialsGrant` trait to our connector.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\ClientCredentialsGrant;

class WarehouseConnector extends Connector;
{
    use ClientCredentialsGrant;
}
```

After you have added the trait, you will need to tell Saloon how to authenticate with your API. First, extend the `defaultOauthConfig` method and use the methods to define your client ID and secret. Saloon also has sensible defaults set for the token endpoint, but you may customize it if you need to. For example, some APIs have a different base URL than the connector's base URL. You can also provide default scopes and even provide a callback to modify the OAuth2 requests being sent.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\ClientCredentialsGrant;

class WarehouseConnector extends Connector;
{
    use ClientCredentialsGrant;
    
    public function resolveBaseUrl(): string
    {
        return 'https://local-warehouse.app';
    }
    
    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId('my-client-id')
            ->setClientSecret('my-client-secret')
	    ->setDefaultScopes(['inventory.read'])
            ->setTokenEndpoint('/oauth/token')
            ->setRequestModifier(function (Request $request) {
                // Optional: Modify the requests being sent.
            })
    }
}
```

{% hint style="info" %}
The endpoint method, like `setTokenEndpoint` on the OAuthConfig class support full URLs if you need to overwrite the base URL on the connector however you may just use the endpoint if the base URL is the same.
{% endhint %}

#### Overwriting the OAuth2 config

Sometimes, you may have a different OAuth2 client ID and secret for each user of your application. If your OAuth2 config is dependent on a per-user/tenant basis, it's recommended that you pass in the credentials as constructor arguments of your connector and then set the `oauthConfig` inside the constructor.&#x20;

In the following example, I will pass in the `$clientId` and the `$clientSecret` as constructor arguments and overwrite the OAuth2 config.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\ClientCredentialsGrant;

class WarehouseConnector extends Connector
{
    use ClientCredentialsGrant;
    
    public function __construct(string $clientId, string $clientSecret)
    {
        $this->oauthConfig()->setClientId($clientId);
        $this->oauthConfig()->setClientSecret($clientSecret);
    }
    
    // ...
}
```

### Creating Access Tokens

You are now ready to create access tokens. You should use the `getAccessToken` method on your connector. If successful, the method will return an `AccessTokenAuthenticator`.  The access token and expiry (if provided) are wrapped up in a [Saloon Authenticator](https://docs.saloon.dev/the-basics/authentication#custom-authenticators) class that can be used to authenticate your connector/requests. It acts like a DTO that can be easily serialized and transported around your application.&#x20;

```php
<?php

$connector = new WarehouseConnector;
$authenticator = $authConnector->getAccessToken();

// Use authenticator to authenticate your connector instance

$connector->authenticate($authenticator);

// Any request sent through this connector will now have authentication applied

$connector->send(new GetInventoryRequest);
```

{% hint style="info" %}
Once you have received the authenticator instance, you should cache it securely in your application for future use. Read further to see how you can do this.
{% endhint %}

#### Custom Scopes

Sometimes you may need to provide an additional "scope" to declare the level of access that your token needs. You may provide default scopes in the OauthConfig class on your connector, but you can also provide additional scopes when creating access tokens. Saloon will separate scopes with spaces but if your API integration requires scopes to be separated any other way, you can specify this with the `scopeSeparator` argument.

```php
<?php

$connector = new WarehouseConnector;

$authenticator = $authConnector->getAccessToken(
    scopes: ['inventory.update', 'inventory.delete'],
    scopeSeparator: '+',
);
```

#### Returning Responses

If you prefer, you may request Saloon to return a `Saloon\Http\Resonse` instance instead of a `AccessTokenAuthenticator` when creating access tokens. To use responses, just provide the `returnResponse` argument when creating access tokens.

```php
<?php

$connector = new WarehouseConnector;

$response = $authConnector->getAccessToken(
    returnResponse: true,
);
```

### Authenticator Methods

The authenticator returned by Saloon when using the `getAccessToken`  method will contain the Access Token, and optionally an expiry date that was returned by the OAuth2 server. You can access these properties with the following methods. You can also check if the authenticator has expired, which will come in handy when refreshing access tokens.

```php
<?php

$authenticator->getAccessToken();
$authenticator->getExpiry();

$authenticator->hasExpired();
$authenticator->hasNotExpired();
```

### Customising The Authenticator

Sometimes the API provider you are authenticating with may require additional information to be used in the authenticator. You can customise how the authenticator will be created by extending the `createAccessTokenAuthenticator` method on your connector.

```php
<?php

protected function createOAuthAuthenticator(string $accessToken, ?DateTimeImmutable $expiresAt = null): OAuthAuthenticatorInterface
{
    return new WarehouseAuthenticator($accessToken, $expiresAt);
}
```

### Customising How The Authenticator Is Created

Sometimes the API provider you are authenticating with may have a different way that they respond with their tokens. If you need to customise the way Saloon creates the authenticator you can extend the `createOAuthAuthenticatorFromResponse` method.

```php
<?php

protected function createOAuthAuthenticatorFromResponse(SaloonResponse $response): OAuthAuthenticatorInterface
{
    $responseData = $response->object();

    $accessToken = $responseData->access_token;
    $expiresAt = new DateTimeImmutable('+' . $responseData->expires_in . ' seconds');

    return $this->createOAuthAuthenticator($accessToken, $expiresAt);
}
```

### Customising The Requests

Sometimes you might integrate with an API that requires additional query parameters or headers to be sent with the OAuth2 flow. You may use the `requestModifier` method on the `getAccessToken` method or use the `setRequestModifier` method within the `OAuthConfig` to add a callable that is invoked before a request is sent.

{% tabs %}
{% tab title="Per Request" %}

```php
<?php

$connector->getAccessToken(requestModifier: function (Request $request) {
    $request->query()->add('access_type', 'offline');
});
```

{% endtab %}

{% tab title="Untitled" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Contracts\Request;
use Saloon\Http\OAuth2\GetClientCredentialsTokenRequest;

protected function defaultOauthConfig(): OAuthConfig
{
    return OAuthConfig::make()
        ->setClientId('my-client-id')
        ->setClientSecret('my-client-secret')
<strong>	->setRequestModifier(function (GetClientCredentialsTokenRequest $request) {
</strong><strong>	     //
</strong><strong>        )},
</strong>}
</code></pre>

{% endtab %}
{% endtabs %}

### Using your own request classes

There are situations where Saloon's own request classes for getting the access token might not suit the API you are integrating with. For example, if an API uses JSON encoding instead of form encoding. You may use the following method on your connector to overwrite the instantiation process of the request class.

```php
<?php

class SpotifyConnector extends Connector
{
    // ...

    protected function resolveAccessTokenRequest(OAuthConfig $oauthConfig, array $scopes = [], string $scopeSeparator = ' '): Request
    {
        return new GetClientCredentialsTokenRequest($oauthConfig, $scopes, $scopeSeparator);
    }
}
```


# Middleware

Saloon has a powerful middleware system that allows you to tap into the request and response lifecycle and make any changes you need before the request is sent or the response is given back to the user. This is useful if you want to build your own advanced Saloon integrations or write more advanced logic like generating a unique reference for every request.

### The Boot Method

Before we get into Saloon's middleware, there is a useful built-in method on every connector and request that you can utilize. This is the `boot` method. It gets executed every time you send a request. You will get access to the underlying `PendingRequest` instance that the sender will provide to the HTTP client. The boot method is a great way to quickly tap into a pending request and change something like add a header, modify the request body or even trigger events.

You may extend the public boot method on either your connector or your request. Remember if you use the connector's boot method, every request with that connector will use that boot method.

You may register middleware inside of the boot method too, which will be used in the rest of the `PendingRequest` lifecycle.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\PendingRequest;

class ForgeConnector extends Connector
{
    // { ... }
    
    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('X-Current-Time', new DateTime);
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\PendingRequest;

class GetServersRequest extends Request
{
    // { ... }
    
    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('X-Current-Time', new DateTime);
    }
}
```

{% endtab %}
{% endtabs %}

### The PendingRequest

The `PendingRequest` class is an intermediary class that Saloon uses to populate everything when you send a request. Every time you send a request, a new PendingRequest instance is created which prevents your connector or request from being mutated every time a request is sent. The PendingRequest class has many of the methods that you are used to seeing on the request/connector for managing headers, query parameters, config, and request body.

The PendingRequest class is used for boot methods, middleware and [plugins](https://docs.saloon.dev/digging-deeper/traits).

### Request Middleware

You may also at any point tap into the request lifecycle by using request middleware. Request middleware is useful to change something on the PendingRequest instance before the request is sent like authenticating the&#x20;

Simply on your connector or request, you can call the `middleware()` method and use the `onRequest()` method. You should provide a callable, like a closure or invokable class. You get access to the `PendingRequest` instance.

{% hint style="info" %}
Return values are not required, but you may either return an instance of `PendingRequest` or a `MockResponse` class for an early fake response.
{% endhint %}

#### Anonymous Functions

You can use a regular closure/anonymous function to create a middleware on the fly.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Contracts\PendingRequest;

$forge = new ForgeConnector;

$forge->middleware()->onRequest(function (PendingRequest $pendingRequest) {
    $pendingRequest->headers()->add('Authorization', 'Bearer ' . $token);
});
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Contracts\PendingRequest;

$request = new GetServersRequest;

$request->middleware()->onRequest(function (PendingRequest $pendingRequest) {
    $pendingRequest->headers()->add('Authorization', 'Bearer ' . $token);
});
```

{% endtab %}
{% endtabs %}

#### Invokable Classes

You may also use invokable classes to keep your middleware classes tidy. If you are using invokable middleware classes, you should implement the `RequestMiddleware` interface.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Contracts\PendingRequest;
use Saloon\Contracts\RequestMiddleware;

class AuthenticateRequest implements RequestMiddleware
{
    public function __invoke(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', 'Bearer ' . $token);
    }
}
```

{% endtab %}

{% tab title="Usage (Connector)" %}

```php
<?php

use Saloon\Contracts\PendingRequest;

$forge = new ForgeConnector;
$forge->middleware()->onRequest(new AuthenticateRequest);
```

{% endtab %}

{% tab title="Usage (Request)" %}

```php
<?php

use Saloon\Contracts\PendingRequest;

$request = new GetServersRequest;
$request->middleware()->onRequest(new AuthenticateRequest);
```

{% endtab %}
{% endtabs %}

### Early Fake Responses

You may also choose to tap into Saloon's MockResponse functionality by creating your own fake responses. Behind the scenes, Saloon's MockResponse extends the SimulatedResponsePayload class which can be returned within a request middleware. When you return a SimulatedResponsePayload or MockResponse, the rest of the request middleware will still be processed but the fake response will be stored on the PendingRequest.&#x20;

If this fake response is present before Saloon sends the request, it will use the `SimulatedSender` instead of the default sender you have provided. This is super handy if you want to build your own middleware that stops Saloon from sending real requests, like for caching.&#x20;

```php
<?php

use Saloon\Contracts\PendingRequest;

$request = new GetServersRequest;

$request->middleware()
    ->onRequest(function (PendingRequest $pendingRequest) {
        return new MockResponse(
            data: ['data' => 'Fake Data!'], 
            statusCode: 200, 
            headers: []
        );
    });
```

{% hint style="warning" %}
Even though you are returning a MockResponse, the next middleware will still receive the PendingRequest instance. Additionally, if another middleware also returns a fake response, the latest will be preferred.
{% endhint %}

### Response Middleware

Once you have sent your request, even if it's a mocked response, Saloon will send the response down the response middleware pipeline. You may add your own response middleware to change the response class or log responses.&#x20;

On your connector or request, you can call the `middleware()` method and use the `onResponse()` method. You should provide a callable, like a closure or invokable class. You get access to the `Response` instance.

{% hint style="info" %}
Return values are not required, but you may return an instance of `Saloon\Contracts\Response` to overwrite the response class in the middleware.
{% endhint %}

#### Anonymous Functions

You can use a regular closure/anonymous function to create a middleware on the fly.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Contracts\Response;

$forge = new ForgeConnector;

$forge->middleware()->onResponse(function (Response $response) {
    Logger::recordResponse($response);
});
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Contracts\Response;

$request = new GetServersRequest;

$request->middleware()->onResponse(function (Response $response) {
    Logger::recordResponse($response);
});
```

{% endtab %}
{% endtabs %}

#### Invokable classes

Similar to request middleware, you can also create invokable middleware classes for response middleware. You should use the `ResponseMiddleware` contract to ensure your invokable class has the correct signature.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Contracts\Response;
use Saloon\Contracts\ResponseMiddleware;

class LogResponse implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        Logger::recordResponse($response);
    }
}
```

{% endtab %}

{% tab title="Usage (Connector)" %}

```php
<?php

use Saloon\Contracts\PendingRequest;

$forge = new ForgeConnector;
$forge->middleware()->onResponse(new LogResponse);
```

{% endtab %}

{% tab title="Usage (Request)" %}

```php
<?php

use Saloon\Contracts\PendingRequest;

$request = new GetServersRequest;
$request->middleware()->onResponse(new LogResponse);
```

{% endtab %}
{% endtabs %}

### Using Constructors

While registering middleware on the fly is really useful, it often leads to repeated code. If you would like your connector to always have a specific request or response middleware you should use the `boot` method described above, or use the constructor of your connector or request.

```php
<?php

class ForgeConnector extends Connector
{
    // {...}
    
    public function __construct()
    {
        $this->middleware()->onRequest(new AuthenticateRequest);
        $this->middleware()->onResponse(new LogResponse);
    }
}
```

{% hint style="danger" %}
Be cautious using anonymous closures inside the constructor/boot method. This may cause issues like Saloon not being able to close connections properly. [Click here to read more.](https://docs.saloon.dev/conclusion/known-issues#usage-of-anonymous-functions-with-long-running-processes-like-laravel-queues)
{% endhint %}

### Using Plugins

Plugins are another useful feature for Saloon that allows you to extend Saloon and tap into the middleware from traits. This is especially useful if you intend to use the trait on multiple requests or connectors.

[Read through the plugins page for more information.](https://docs.saloon.dev/digging-deeper/traits)

### Middleware Naming

You can choose to name your middleware. Each name must be unique to the given pipeline. For example, you cannot have two middleware with the same name on the request pipeline, but you could have the same name used once for the request pipeline and once for the response pipeline. Use the second argument to specify a name.

```php
<?php

$request = new GetServersRequest;

$request->middleware()->onResponse(new LogResponse, 'logResponse');
```

### Global Middleware

Saloon also supports adding global middleware. You most likely won't need this level of granularity but if you do, you may use the `Config` class. When using global middleware, you should make sure to name your middleware so it isn't accidentally registered twice.

```php
<?php

use Saloon\Helpers\Config;

Config::middleware()->onResponse(new LogResponse, 'logResponse');
```

{% hint style="danger" %}
Be cautious with global middleware. Since it uses a static property behind the scenes, the value is kept between tests when running a full test suite. You can use the `Config::resetMiddleware` method to get around this issue.
{% endhint %}

### Middleware Execution Order

The following image illustrates the order that middleware is executed in.

<figure><img src="https://2652606861-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F29ap30WDt824nN7WNz2U%2Fuploads%2FrNCc4rgQ16j3w7BaIeXc%2FMiddleware%20Order.png?alt=media&#x26;token=66efaf2e-d9ea-429e-a52d-72b38888d1b0" alt=""><figcaption></figcaption></figure>

### Prepending Middleware

You may choose to "prepend" middleware which will put a given middleware at the top of the execution chain. For example if I added a middleware in the boot methods, as described above, it would usually run after plugin and request/connector middleware - but if I used the `prepend` method, it will run at the very beginning (assuming nothing else has been prepended after your middleware)

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\PendingRequest;

class GetServersRequest extends Request
{
    // { ... }
    
    public function boot(PendingRequest $pendingRequest): void
    {
        $request->middleware()->onResponse(new LogResponse, prepend: true);
    }
}
```

### Middleware Caviets

Here are some known caveats that you should know about when using Saloon's middleware.

* **You cannot add request middleware from inside of another request middleware,** but you can add response middleware inside of the onRequest() middleware method.
* **You cannot add response middleware from inside of another response middleware.**
* You may return a simulated response payload or fake response in request middleware but you will always get a PendingRequest back

### Guzzle Handlers / Middleware

With previous versions of Saloon, you could add Guzzle middleware or "handlers" directly to the connector or request. Version two is now sender agnostic, so the `addHandler` method has been removed but you may still add Guzzle middleware if you are using the `GuzzleSender` (the default sender with Saloon)

### Adding Guzzle Middleware

You can add middleware to the Guzzle client by using the `sender` method on the connector. You must only add Guzzle middleware directly on your connector with your constructor method. This is because Saloon only instantiates the sender once, so in order to prevent middleware from being registered multiple times, it should be placed in the constructor.

```php
<?php

class ForgeConnector extends Connector
{
    // {...}
    
    public function __construct()
    {
        $this->sender()->addMiddleware(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request->withHeader('X-Custom-Header', 'Hello');
                
                return $handler($request, $options);             
            };
        })
    }
}
```

{% hint style="info" %}
To read more about Guzzle's middleware and handlers [click here](https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html).
{% endhint %}

### Accessing The Underlying Guzzle Instance

You may need to modify the Guzzle client or the handler stack. If you need to do this, you can use the `getGuzzleClient` or `getHandlerStack` methods.

```php
<?php

class ForgeConnector extends Connector
{
    // {...}
    
    public function __construct()
    {
        $guzzleClient = $this->sender()->getGuzzleClient();
        
        $handlerStack = $this->sender()->getHandlerStack();
    }
}
```


# Request Delay

Sometimes in your application, you may want to introduce a delay in your requests. This may be to avoid hitting rate limits or to avoid overloading a development environment. With Saloon, you may use the `delay` method on either your connector or request. With this method, you can set a delay in milliseconds.&#x20;

{% tabs %}
{% tab title="Connector" %}

```php
<?php

$forge = new ForgeConnector;

// Delay every request sent with the ForgeConnector by 500ms 

$forge->delay()->set(500);
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

$request = new GetServersRequest;

// Delay just this request instance by 500ms

$request->delay()->set(500);
```

{% endtab %}
{% endtabs %}

{% hint style="warning" %}
If you have defined the delay on both the connector and the request, the request delay will take priority.&#x20;
{% endhint %}

### Default Delay

You may also wish to define a default delay on your connector or request. You can do this by extending the `defaultDelay` method.&#x20;

{% tabs %}
{% tab title="Connector" %}

```php
<?php

class ForgeConnector extends Connector
{
    // {...}

    // Every request sent through this connector will have a 500ms delay.

    protected function defaultDelay(): ?int
    {
        return 500;
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

class GetServersRequest extends Request
{
    // {...}

    // Every time this request is sent, a 500ms delay is added

    protected function defaultDelay(): ?int
    {
        return 500;
    }
}
```

{% endtab %}
{% endtabs %}


# Senders

### Introduction

Under the hood, Saloon uses a "Sender" to send a fully built pending request to the web. Saloon uses the GuzzleSender as its default sender as Guzzle provides lots of functionality out of the box and is well-tested and used among the PHP community.

#### Example Sender

Click here to see the [GuzzleSender](https://github.com/Sammyjo20/Saloon/blob/v2/src/Http/Senders/GuzzleSender.php) code as an example of a sender.

### Creating your own sender

While Saloon's default sender is the GuzzleSender, Saloon is completely sender agnostic and different senders/HTTP clients can be built to be used by it. If you would like to build a sender for a different HTTP client or customise the Guzzle sender read through the documentation below.

#### Sender Contract

Saloon has a `Sender` interface which defines the structure that a sender must have. You must implement this contract for Saloon to accept the sender that you are creating. The contract contains just one method: `sendRequest`. This method will receive an instance of `PendingRequest` which is the object that contains everything ready to be sent. At this point, plugins and request middleware has been run and the request is ready to be sent.

The `sendRequest` method must either return a `Saloon\Contracts\Response` or an instance of `GuzzleHttp\Promise\PromiseInterface`. These response types will change depending if the request was sent asynchronously or not.

```php
<?php

declare(strict_types=1);

namespace Saloon\Contracts;

use GuzzleHttp\Promise\PromiseInterface;

interface Sender
{
    /**
     * Send the request.
     *
     * @param PendingRequest $pendingRequest
     * @param bool $asynchronous
     * @return Response|PromiseInterface
     */
    public function sendRequest(PendingRequest $pendingRequest, bool $asynchronous = false): Response|PromiseInterface;
}
```

#### HTTP Client Instances

The sender instance is kept alive for the entire connector's lifetime so that the same sender is used to send all requests. This is required because Guzzle's HTTP client cannot be destructed if you are using request concurrency. It's recommended that your HTTP client is created when the sender is constructed and kept as a property on the sender.&#x20;

#### Headers, Query Parameters and HTTP Config

You will be able to access all the headers, query parameters and HTTP config from the pending request instance you are given. You can use the methods provided on these resources to send the headers, query parameters and config to your HTTP client.

```php
<?php

$pendingRequest->headers();
$pendingRequest->query();
$pendingRequest->config();
```

#### Request Body

Saloon's pending request class contains a `body()` method which can return a different `BodyRepository` depending on the request body that was used. Saloon provides a `__toString()` method on the repository to make it easy to send it, however, Saloon does not send a Content-Type, so it will be either up to your sender or the developer to provide this as a header.&#x20;

Saloon also does not provide a `__toString()` method for multipart requests. You must handle multipart value objects yourself and convert them into a multipart body.

#### Responses

When creating your response, you should use the `$pendingRequest->getResponseClass()` method as this contains the response class that needs to be constructed. You should create the response with the `$responseClass::fromPsrResponse()` method as this is a common static method used by all responses, even if someone has made a custom response.

### Using your custom sender

Once you have created a custom sender, you must overwrite the protected `defaultSender` method on your connector. This method must return an instance of your sender. Now when you send a request using this connector it will use your custom sender.

```php
<?php

use Saloon\Contracts\Sender;

class ForgeConnector extends Connector
{
    // {...}
    
    protected function defaultSender(): Sender
    {
        return new CustomSender;
    }
}
```


# Plugins

Saloon has plugins which make it easy for you to add logic to your connector or requests in a reusable and elegant way. Plugins are traits that can be added to either a request or a connector and have a special "boot" method which is invoked during the request lifecycle.

### Getting Started

It's easy to create your own plugin for Saloon, just create a trait and add it to either your connector or request. In this example, we will create a logging plugin that when added will log the request being sent to [Ray](https://myray.app/).

Our plugin will be called `HasLogging` . In order for Saloon to execute this plugin, we must create a public method that starts with `boot` followed by the name of the trait. For example: `bootHasLogging`. The method is given an instance of `PendingRequest`.

{% tabs %}
{% tab title="Definition (Trait)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

trait HasLogging
{
<strong>    public function bootHasLogging(PendingRequest $pendingRequest): void
</strong>    {
        ray($pendingRequest);
    }
}
</code></pre>

{% hint style="warning" %}
You must not use `$this` inside of the trait, as mutating the original request or connector is discouraged. The PendingRequest is unique to just the request being sent.
{% endhint %}
{% endtab %}

{% tab title="Usage (Connector)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

class ForgeConnector extends Connector
{
<strong>    use HasLogging;
</strong>}
</code></pre>

{% endtab %}
{% endtabs %}

When you send your request, plugins are the first things that are invoked, even before the `boot` method. This is to allow maximum compatibility with [middleware ](https://docs.saloon.dev/digging-deeper/middleware)and [authenticators](https://docs.saloon.dev/the-basics/authentication). This also means that because it's the first process in the chain, other steps like middleware and the boot method will be able to overwrite anything added by a plugin.

<figure><img src="https://2652606861-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F29ap30WDt824nN7WNz2U%2Fuploads%2FUrPyAEiUXjqGNGFzP8dC%2FSaloon%20v2.png?alt=media&#x26;token=feafc855-0a62-4888-81e9-bf43af26a276" alt=""><figcaption></figcaption></figure>

Because we added the trait to our connector, every request will use the `HasLogging` plugin. If you would like the plugin to be applied to just one request, you can add the plugin to the request only.&#x20;

{% hint style="warning" %}
Be careful when adding the plugin to both the connector and the request at the same time, they will both be executed separately.
{% endhint %}

### Built-in Plugins

Saloon comes with a few plugins out of the box, you may have already used them.&#x20;

#### AcceptsJson

This plugin will add the `Accept: application/json` header to your pending request. This is useful when dealing with JSON APIs.

#### AlwaysThrowOnErrors

This plugin will call the `$response->throw()` method. This method will throw an exception if the response has failed, rather than just returning a failed response.

#### HasTimeout

This plugin allows you to define a `connectTimeout` and `requestTimeout` property on your request or connector.


# Laravel Helpers

Saloon has been built to integrate beautifully with [Laravel](https://laravel.com). The separate Laravel plugin comes with a facade that helps with mocking and recording requests, Artisan console commands to really easily build your API integration, and even a separate default sender that uses Laravel's HTTP Client.

### Installation

You can install the separate package with Composer. You must already have`saloonphp/saloon` as a required dependency in your `composer.json` file.

```bash
composer require saloonphp/laravel-plugin "^2.0"
```

> Requires Laravel 9+

Next, publish the configuration file with the following Artisan command

```bash
php artisan vendor:publish --tag=saloon-config
```

#### Laravel Zero

If you are using **Laravel Zero**, then the `SaloonServiceProvider` that registers the `Saloon` facade as well as some default middleware might not be registered. You can register Saloon's service provider in your `AppServiceProvider.php`'s `register()` method definition.

```php
<?php

use Saloon\Laravel\SaloonServiceProvider;

public function register()
{
    $this->app->register(SaloonServiceProvider::class);
}
```

### Available Laravel Commands

Each of the commands will create files within the `App\Http\Integrations` namespace. Each integration name is required for its own namespace. For example: `App\Http\Integrations\Forge`.

| Command                                                      | Description                    |
| ------------------------------------------------------------ | ------------------------------ |
| saloon:connector \<Integration Name> \<Connector Name>       | Creates a new connector        |
| saloon:request \<Integration Name> \<Request Name>           | Creates a new request          |
| saloon:response \<Integration Name> \<Response Name>         | Creates a custom response      |
| saloon:plugin \<Integration Name> \<Plugin Name>             | Creates a plugin               |
| saloon:auth \<Integration Name> \<Authenticator Name>        | Creates a custom authenticator |
| saloon:oauth-connector \<Integration Name> \<Connector Name> | Creates a new OAuth2 connector |

You can use the `saloon:list` command to get information about Saloon usage within your application. This includes details about your integrations, as well as associated requests, connectors, plugins, responses, and authenticators.

### Laravel HTTP Client Sender

Saloon comes with a sender built just for Laravel. The HTTP sender uses Laravel's [HTTP client](https://laravel.com/docs/9.x/http-client#main-content) under the hood, which allows your requests to be handled by Laravel just like using the HTTP client directly. This means Saloon's requests can be recorded in Telescope and also picked up by Laravel's event system.

#### Installation

The HTTP client sender comes in as a separate library. This is to keep its versioning separate from Saloon and the Laravel Integration. You can install it with Composer.

```bash
composer require saloonphp/laravel-http-sender
```

#### Configuration

Next, in your `config/saloon.php` file, change the default sender to `HttpSender::class`. Now every connector in your Laravel app will automatically use the HTTP sender. No more configuration is required, Saloon should work exactly the same as before, just now with full HTTP client support.

```php
<?php

declare(strict_types=1);

use Saloon\HttpSender\HttpSender;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Saloon Sender
    |--------------------------------------------------------------------------
    |
    | This value specifies the "sender" class that Saloon should use by
    | default on all connectors. You can change this sender if you
    | would like to use your own. You may also specify your own
    | sender on a per-connector basis.
    |
    */

    'default_sender' => HttpSender::class,

];
```

Now when you send requests, they will be sent through Laravel's HTTP client - if you have Laravel Telescope installed, you should see the requests appearing under the "HTTP Client" tab of Telescope.

### Events

With the Laravel plugin installed, Saloon will start sending events when requests are being sent. These events are:

* SendingSaloonRequest
* SentSaloonRequest

These events can be added to your `EventServiceProvider` and you can create listeners to handle when these happen.

### Mocking Requests

Typically when mocking requests in Saloon, you are only limited to the current test you are in, without having to pass your `MockClient` down into every call. With the Laravel plugin installed, you may use the `Saloon::fake` method to configure mocking globally across your application. This is super handy if you want to test your API requests nested deep in your application.

[Click here](https://docs.saloon.dev/testing/manual-fake-responses) to read more about mocking requests.


# Caching Responses

There are scenarios where you may want to cache a response from an API, like retrieving a static list or retrieving data that you know won't change for a specified amount of time. Caching can be incredibly powerful and can speed up an application by relying less on a third-party integration. Saloon has a [first-party plugin](https://github.com/Sammyjo20/saloon-cache-plugin) that you can install to enable caching support.

### Installation

To install the caching functionality into Saloon, install the plugin using Composer.

```bash
composer require saloonphp/cache-plugin "^2.0"
```

### Configuration

Next, add the `Cacheable` interface and `HasCaching` trait to your request or connector. You must define the two methods.

* resolveCacheDriver
* cacheExpiryInSeconds

```php
<?php

use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\CachePlugin\Contracts\Cacheable;

class GetServersRequest extends Request implements Cacheable
{
    use HasCaching;

    // ...
    
    public function resolveCacheDriver(): Driver
    {
        //
    }
    
    public function cacheExpiryInSeconds(): int
    {
        //
    }
}
```

{% hint style="warning" %}
When you add the `HasCaching` trait onto your connector, every request through the connector will be cached.
{% endhint %}

### Cache Drivers

Saloon's caching plugin works with different caching drivers that you can use on your request or connector. The plugin comes with the following cache drivers:

* PsrCacheDriver (Supports PSR-16 Cache Implementations)
* FlysystemDriver (Requires `league/flysystem` version 3)
* LaravelCacheDriver (Supports any of Laravel's cache disks, requires Laravel)

{% tabs %}
{% tab title="PsrCacheDriver" %}

```php
use Saloon\CachePlugin\Drivers\PsrCacheDriver;

public function resolveCacheDriver(): Driver
{
    // This example uses the PhpArrayAdapter from 
    // the symfony/cache library.
    
    return new PsrCacheDriver(new PhpArrayAdapter);
}
```

{% endtab %}

{% tab title="FlysystemDriver" %}

```php
use Saloon\CachePlugin\Drivers\FlysystemDriver;

public function resolveCacheDriver(): Driver
{
    // This example uses the "AwsS3V3Adapter" driver
    // that is provided by Flysystem.

    return new FlysystemDriver(
        new Filesystem(new AwsS3V3Adapter($s3Client, 'bucket-name'))
    );
}
```

{% endtab %}

{% tab title="LaravelCacheDriver" %}

```php
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;

public function resolveCacheDriver(): Driver
{
    // This example uses Redis cache store that
    // Laravel provides.

    return new LaravelCacheDriver(Cache::store('redis'));
}
```

{% endtab %}
{% endtabs %}

### Expiry

When Saloon caches a request, it will last a specified amount of time in seconds. You should specify this time in the `cacheExpiryInSeconds` method.

```php
public function cacheExpiryInSeconds(): int
{
    return 3600; // One Hour
}
```

### Caching Requests

Once you have configured the cache driver and expiry, Saloon will automatically cache requests and store them in your requested cache-store. The next time you send a request, the cached response will be automatically swapped out and no real request will be sent.

#### Checking if a response is cached

You may use the `isCached()` method to determine if a response is cached.

```php
<?php

$forge = new ForgeConnector;

$response = $forge->send(new GetServersRequest);
$response->isCached(); // false

// Next time it is sent...

$response = $forge->send(new GetServersRequest);
$response->isCached(); // true
```

#### When will Saloon cache a request?

Saloon will only cache a **successful** request when the method is either GET or OPTIONS. You can customise this by extending the `getCacheableMethods` method where you added the `HasCaching` trait.

```php
<?php

use Saloon\Enums\Method;

class GetServersRequest extends Request implements Cacheable
{
    use HasCaching;
    
    protected function getCacheableMethods(): array
    {
        return [Method::GET, Method::OPTIONS, Method::POST];
    }
}
```

You may customise when Saloon considers a request as successful. To read more about this, [click here.](https://docs.saloon.dev/the-basics/handling-failures#customising-when-saloon-thinks-a-request-has-failed)

### Customising the cache key

By default, the cache key is created from the full request URL, the headers that are sent and the query parameters that are used. You may choose to define your own custom cache key. Just extend the protected `cacheKey` method where you added the `HasCaching` trait. You will get access to the `PendingRequest` instance that contains all the request properties.

```php
<?php

use Saloon\Enums\Method;

class GetServersRequest extends Request implements Cacheable
{
    use HasCaching;
    
    protected function cacheKey(PendingRequest $pendingRequest): ?string
    {
        return 'custom-cache-key';
    }
}
```

### Invalidating the current cache

You may want to make a request and purge the existing cache before making the request. You can use the `invalidateCache` method on the request before sending the request and Saloon will delete any existing cache for that request.

```php
<?php

$forge = new ForgeConnector;

$request = new GetServersRequest;
$request->invalidateCache();

$response = $forge->send($request);
```

### Temporarily Disabling Caching

Sometimes you may wish to disable the caching on a per-request basis for debugging or to bypass caching. You can do this by using the `disableCaching` method on the request.

```php
<?php

$forge = new ForgeConnector;

$request = new GetServersRequest;
$request->disableCaching();

$response = $forge->send($request);
```

### Source Code

To view the source code of this plugin, [click here](https://github.com/Sammyjo20/saloon-cache-plugin).


# Handling Rate Limits

Handling rate limits with API integrations can be hard. Saloon has a first-party plugin that provides you with the tools you need to prevent rate limits and handle what happens if a rate limit is exceeded.

With this plugin, you are able to define various limits on a per-connector or request basis. You can also control if Saloon should throw an exception or sleep if a limit is reached. Saloon will keep track of how many requests are made and when a rate limit is hit, Saloon will prevent further requests on the connector/request until the rate limit has been lifted.

Saloon will even listen out for "429 Too Many Requests" responses and will automatically throw exceptions before your future requests are sent and the limit will be lifted based on the `Retry-After` header.

This plugin also comes with a Laravel Job middleware which you can use inside of your jobs to automatically release them back onto the queue if a rate limit has been reached.

```php
<?php

use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Limit;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\RateLimitPlugin\Stores\RedisStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;

class SpotifyConnector extends Connector
{
    use AcceptsJson;
    use HasRateLimits;

    public function resolveBaseUrl(): string
    {
        return 'https://api.spotify.com/v1';
    }

    protected function resolveLimits(): array
    {
        return [
            Limit::allow(100)->everyMinute(),
            Limit::allow(1000)->everyDay(),
            Limit::allow(5000)->everyMonth(),
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        $redis = new Redis;
        $redis->connect('127.0.0.1');
    
        return new RedisStore($redis);
    }
}
   
```

### Installation

You can install this plugin via Composer.

```
composer require saloonphp/rate-limit-plugin "^1.0"
```

### Getting Started

To use the plugin, add the `HasRateLimit` trait to your connector or request. If you have a connector, you should put it on the connector, but it can be put on an individual request if a specific endpoint has a different rate limit or if you are using solo requests. If you are using the trait on both your connector and request, the rate limits are combined together.

```php
use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

class SpotifyConnector extends Connector
{
    use HasRateLimits;
}
```

Next, you will be required to implement two methods: `resolveLimits` and `resolveRateLimitStore`. These methods allow you to define the limits that Saloon will keep track of and the store where the limit "hits" will be kept.

```php
use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Stores\MemoryStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

class SpotifyConnector extends Connector
{
    use HasRateLimits;
    
    protected function resolveLimits(): array
    {
        return [];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new MemoryStore;
    }
}
```

### Stores

Here are the various stores that the rate-limiting plugin supports. You may also [create your own stores](https://github.com/Sammyjo20/saloon-docs/blob/v2/digging-deeper/broken-reference/README.md) if this library does not come with the one you need. Stores are used to keep track of how many requests have been sent through a given connector/request.

#### Available Stores

* In-Memory (Array)
* File
* Redis
* Predis
* PSR Cache Store
* Laravel Cache Store

#### Memory Store

The simplest store. This store is persisted on the current instance of the connector/request and all information is lost when the connector is destructed.

```php
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Stores\MemoryStore;

protected function resolveRateLimitStore(): RateLimitStore
{
    return new MemoryStore;
}
```

#### File Store

This store will use the local filesystem to store the limits. The only requirement for this store is for you to define the absolute path to a directory where you would like the limits to be stored.

```php
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Stores\FileStore;

protected function resolveRateLimitStore(): RateLimitStore
{
    return new FileStore('some/application/directory');
}
```

#### Redis Store

This store will use PHP's `Redis` extension to store the limits on a Redis database. You should pass the Redis configuration into this store.

```php
use Redis;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Stores\RedisStore;

protected function resolveRateLimitStore(): RateLimitStore
{
    $client = new Redis;
    $client->connect('127.0.0.1');

    return new RedisStore($client);
}
```

#### Predis Store

Similar to the `RedisStore`, the `PredisStore` allows you to connect to Redis through the `predis/predis` PHP library.

```php
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Stores\PredisStore;

protected function resolveRateLimitStore(): RateLimitStore
{
    $client = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => '10.0.0.1',
        'port'   => 6379,
    ]);

    return new PredisStore($client);
}
```

#### PSR Cache Store

This store supports any PSR-16 cache store provided by the `psr/simple-cache` library.

```php
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Stores\PsrStore;

protected function resolveRateLimitStore(): RateLimitStore
{
    return new PsrStore(new SomePsr16Store);
}
```

#### Laravel Cache Store

This store can only be used in a Laravel environment but allows you to use any of Laravel's cache disks.

```php
use Illuminate\Support\Facades\Cache;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;

protected function resolveRateLimitStore(): RateLimitStore
{
    return new LaravelCacheStore(Cache::store('redis'));
}
```

### Limits

While this plugin can detect if a 429 status occurs from a response, it's better to prevent your application from hitting rate limits than let them happen. This plugin provides an expressive `Limit` class which can be used to define different limits. You can define as many limits as you like, with various intervals.

#### Configuring Limits

Here is a simple example of a limit for an API which only allows 60 requests per minute, but has a daily limit of 1,000 API calls. There are many different limit intervals, as well as different ways you can instruct Saloon to handle the limit. There is no restriction on the number of limits you can have.

```php
use Saloon\RateLimitPlugin\Limit;

protected function resolveLimits(): array
{
    return [
        Limit::allow(60)->everyMinute(),
        Limit::allow(1000)->everyDay(),
    ];
}
```

#### Limit Intervals

There are various limit intervals which you can use on your limiter, ranging for seconds to up to the end of the month.

```php
use Saloon\RateLimitPlugin\Limit;

Limit::allow(60)->everySeconds(seconds: 5);
Limit::allow(60)->everyMinute();
Limit::allow(60)->everyFiveMinutes();
Limit::allow(60)->everyThirtyMinutes();
Limit::allow(60)->everyHour();
Limit::allow(60)->everySixHours();
Limit::allow(60)->everyTwelveHours();
Limit::allow(60)->everyDay();
Limit::allow(60)->everyDayUntil('8pm');
Limit::allow(60)->untilMidnightTonight();
Limit::allow(60)->untilEndOfMonth();
```

#### Custom Names

Sometimes you may need to add a name to your limiter. A good example of this is if you have separate API keys per user and therefore require a different API rate limit per user. You can add the `name()` method to your limit to specify a custom name for your limiter. Each limiter's name must be unique.

```php
protected function resolveLimits(): array
{
    return [
        Limit::allow(60)->everyMinute()->name('spotify-limit-user-' . $this->userId),
    ];
}
```

#### Custom Prefixes

Saloon will append the class name of the connector or a request as the prefix to the limiter name. For example SpotifyConnector:30\_every\_60. You may customise the prefix by extending the `getLimiterPrefix` method on your connector or request.

```php
protected function getLimiterPrefix(): ?string
{
    return 'spotify-user-' . $this->userId;
}
```

#### Custom Thresholds

You may want to specify the percentage threshold that Saloon should accept as the number of "hits" on a given limit. This is useful if you want to stay just under the real API limit while still defining the limit in the connector/request.

The threshold must be a number between 0 and 1 (e.g 0.8 = 80%)

```php
use Saloon\RateLimitPlugin\Limit;

protected function resolveLimits(): array
{
    return [
        Limit::allow(60, threshold: 0.8)->everyMinute(), // Will fail when at 80% capacity
    ];
}
```

#### Sleep

If would rather Saloon didn't throw an exception, you can use the `sleep` method when defining a limit. When using the sleep method, an exception won't be thrown. Instead, Saloon will wait the remaining number of seconds before a request is attempted again.

```php
use Saloon\RateLimitPlugin\Limit;

protected function resolveLimits(): array
{
    return [
        Limit::allow(60)->sleep(),
    ];
}
```

#### "429: Too Many Attempts" Detection

While it's recommended that you should define your limits above, Saloon will try to catch 429 "Too Many Attempts" errors from an API and will automatically mark a limit as "exceeded" if it sees this status. By default, Saloon will attempt to parse the `Retry-After` header to work out when a limit has been exceeded. If Saloon cannot calculate this, the limit will be released after 60 seconds.

You can customise this behaviour by overwriting the `handleTooManyAttempts` method.

```php
protected function handleTooManyAttempts(Response $response, Limit $limit): void
{
    if ($response->status() !== 429) {
        return;
    }

    $limit->exceeded(
        releaseInSeconds: RetryAfterHelper::parse($response->header('Retry-After')),
    );
}
```

Alternatively, you may choose to disable this functionality. You can do this by setting the `detectTooManyAttempts` property to `false` in your connector/request's constructor.

<pre class="language-php"><code class="lang-php">use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Stores\MemoryStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

class SpotifyConnector extends Connector
{
    use HasRateLimits;
    
    public function __construct()
    {
<strong>        $this->detectTooManyAttempts = false;
</strong>    }
}
</code></pre>

### Handling Rate Limits Being Exceeded

When a rate limit has been reached, Saloon will throw a `RateLimitReachedException`. This exception contains a `getLimit` method which may be used to see the limit that has thrown the exception and see the number of seconds to wait before a request can be sent again. This can be done with a simple try-catch approach or if you are using the provided Laravel job middleware, then you can instruct your jobs to wait until the limit has been lifted.

#### Try/Catch

As mentioned above, Saloon will throw an exception if a rate limit is reached or if the API returns a 429: Too Many Requests response. You could use a try/catch block to catch the exception and do something with the limit. For example, you may return an error to your users to let them know how long they need to wait - or you might retry the request later. If you are using Saloon in a context of a queued process, then you may want to retry the queued job in the future, from the remaining seconds.

```php
use Saloon\RateLimitPlugin\Exceptions\RateLimitReachedException;

$spotify = new SpotifyConnector;

try {
   $response = $spotify->send(new GetPlaylistRequest);
} catch (RateLimitReachedException $exception) {
    $seconds = $exception->getLimit()->getRemainingSeconds();
    
    // Return our users back to our application with a custom response that could be 
    // shown on the front end.

    return response("Too many requests to Spotify's API. Please try again in ${$seconds} seconds.");
}
```

#### Sleep

If would rather Saloon didn't throw an exception, you can use the `sleep` method when defining a limit. When using the sleep method, an exception won't be thrown. Instead, Saloon will wait the remaining number of seconds before a request is attempted again.

```php
use Saloon\RateLimitPlugin\Limit;

protected function resolveLimits(): array
{
    return [
        Limit::allow(60)->sleep(),
    ];
}
```

#### Laravel Job Middleware

If you are using Laravel, then this library comes with a [job middleware](https://laravel.com/docs/queues#job-middleware) that you can use. This job middleware will catch the `RateLimitReachedException` and automatically release your job back onto the queue with the remaining seconds added. Add this to the `middleware` method on your Laravel Job.

```php
use Saloon\RateLimitPlugin\Helpers\ApiRateLimited;
 
public function middleware(): array
{
    return [new ApiRateLimited];
}
```

{% hint style="info" %}
You may also wish to increase your job's tries when using this middleware in case the job needs to be retried multiple times.
{% endhint %}

### Creating your own store

You may create your own rate limit store by implementing the `RateLimitStore` interface.

```php
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;

class CustomStore implements RateLimitStore
{
    /**
     * Get a rate limit from the store
     */
    public function get(string $key): ?string
    {
        //
    }

    /**
     * Set the rate limit in the store
     */
    public function set(string $key, string $value, int $ttl): bool
    {
        //
    }
}
```

### Disabling Rate Limiting

Sometimes you might want to disable rate limiting by default or even disable it on a per-connector basis. You can either use a property to disable the rate-limiting functionality by default, or you can use the `useRateLimitPlugin()` method to disable it on a per-instance basis.

<pre class="language-php"><code class="lang-php">use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

class SpotifyConnector extends Connector
{
    use HasRateLimits;
    
    public function __construct()
    {
<strong>        $this->rateLimitingEnabled = false;
</strong>    }
}

// Or...

$connector = new SpotifyConnector;
$connector->useRateLimitPlugin(false);
</code></pre>


# SDK Generator

The Saloon SDK Generator is a third-party Saloon plugin written and maintained by [Crescat](https://crescat.io/) ([Helge Sverre](https://twitter.com/HelgeSverre)) and allows people to easily generate Saloon SDKs from an OpenAPI file or Postman collection. The generator can automatically create connectors, requests and responses for you to help save you time when starting a new SDK.&#x20;

{% hint style="info" %}
Note: This tool helps you set up the foundation for your SDK, but it might not create a complete, ready-to-use solution.
{% endhint %}

<figure><img src="https://2652606861-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F29ap30WDt824nN7WNz2U%2Fuploads%2FwxtOPbR6uETVrzzeV2Vy%2Fheader.png?alt=media&#x26;token=cd8d05a8-ff97-41b4-b486-b0f8382f15dd" alt=""><figcaption></figcaption></figure>

### Installation

You can install the Saloon SDK Generator through Composer. You can install it globally on your machine and have a simple CLI to generate SDKs.

```sh
composer global require crescat-io/saloon-sdk-generator
```

{% hint style="info" %}
The library requires PHP 8.1 and above
{% endhint %}

### Basic Usage

To generate the PHP SDK from an API specification file, run the following command:

```sh
sdkgenerator generate:sdk API_SPEC_FILE.{json|yaml|yml}
     --type={postman|openapi} 
     [--name=SDK_NAME] 
     [--output=OUTPUT_PATH] 
     [--namespace=Company\\Integration] 
     [--force] 
     [--dry] 
     [--zip]
```

Replace the placeholders with the appropriate values:

* `API_SPEC_FILE`: Path to the API specification file (JSON or YAML format).
* `--type`: Specify the type of API specification (`postman` or `openapi`).
* `--name`: (Optional) Specify the name of the generated SDK (default: Unnamed).
* `--namespace`: (Optional) Specify the root namespace for the SDK (default: `App\\Sdk`).
* `--output`: (Optional) Specify the output path where the generated code will be created (default: ./Generated).
* `--force`: (Optional) Force overwriting existing files.
* `--dry`: (Optional) Perform a dry run. It will not save generated files, only show a list of them.
* `--zip`: (Optional) Use this flag to generate a zip archive containing all the generated files.

### Full Documentation

The full documentation can be found by visiting the Github repository here:

{% embed url="<https://github.com/crescat-io/saloon-sdk-generator>" %}

### Issue Tracking

As this is not directly affiliated with Saloon, please report any issues you have directly on the repository linked above.


# Recording Responses

When writing tests for an API integration, it is best to simulate a real request as much as possible. With Saloon's MockResponse class, you can build up example responses manually. This is useful, but it can be time-consuming, especially if an API returns a huge amount of data, it would take a long time to manually write MockResponses and keep it maintained.

Saloon has a feature called fixture recording, this feature will allow you to make a real request to the API you are integrating with and then it will store that response in a file for later. This is a common practice for people writing integrations for APIs, but Saloon makes it effortless.

### Registering a MockClient

Request recording starts with a `MockClient`. This class can be applied directly to a connector instance to be used across all requests, or it can be applied on a per-request basis.

{% tabs %}
{% tab title="All Requests (Connector)" %}

```php
<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    MockResponse::fixture('servers.index'),
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);
```

{% endtab %}

{% tab title="Individual Request" %}

```php
<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    MockResponse::fixture('servers.index'),
]);

$forge = new ForgeConnector;
$request = new GetAllServersRequest;

// Send a request with a MockClient

$forge->send($request, $mockClient);
```

{% endtab %}
{% endtabs %}

### Using Laravel?

If you are using Laravel, as well as the Saloon Laravel helper library, you don't have to use `withMockClient` on every instance of your connector. You may use the `Saloon` facade `fake` method to define your mock responses. This is a built-in global MockClient that when used will be applied to *all* Saloon requests sent in your application.

```php
<?php

use Saloon\Laravel\Facades\Saloon;

Saloon::fake([
    MockResponse::fixture('servers.index'),
]);
```

### Setup

Getting started with fixture recording is easy. When defining your mock responses, instead of defining a MockResponse with headers, data and config - use the fixture static property. This property will accept a single argument, the fixture name.

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);
```

{% endtab %}
{% endtabs %}

{% hint style="info" %}
The above example will configure a fixture to be used every time the **UserRequest** is called during mocking. You may also use a sequence of fixtures, connector mocking, or use a fixture on a specific URL path. Read the mocking pages for more information.
{% endhint %}

### How does it work?

Once you have defined a fixture to be used for a particular request pattern, you can make a request just like you normally would. Saloon will check if the fixture already exists, and if it doesn't - it will make the real API request and store the response for next time.

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

// The initial request will check if a fixture called "singleServer" 
// exists. Because it doesn't exist yet, the real request will be
// sent and the response will be recorded.

$request = new GetForgeServerRequest(12345);
$response = $forge->send($request);

// However, the next time the request is made, the fixture will 
// exist, and Saloon will not make the request again.

$request = new GetForgeServerRequest(12345);
$response = $forge->send($request);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    GetForgeServerRequest::class => MockResponse::fixture('singleServer')
]);

$forge = new ForgeConnector;

// The initial request will check if a fixture called "singleServer" 
// exists. Because it doesn't exist yet, the real request will be
// sent and the response will be recorded.

$request = new GetForgeServerRequest(12345);
$response = $forge->send($request);

// However, the next time the request is made, the fixture will 
// exist, and Saloon will not make the request again.

$request = new GetForgeServerRequest(12345);
$response = $forge->send($request);
```

{% endtab %}
{% endtabs %}

### Namespacing

Depending on the size of your application and the number of API integrations you have, you may want to namespace the fixtures into their own folders, for example, I may have a "forge" namespace and a "digitalOcean" namespace.

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::fixture('forge/singleServer'),
    GetDigitalOceanServerRequest::class => MockResponse::fixture('digitalOcean/singleServer'),
]);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    GetForgeServerRequest::class => MockResponse::fixture('forge/singleServer'),
    GetDigitalOceanServerRequest::class => MockResponse::fixture('digitalOcean/singleServer'),
]);
```

{% endtab %}
{% endtabs %}

### Configuration

#### Fixture Path

Ordinarily, Saloon will store all fixtures in a **tests/Fixtures/Saloon** directory. If you would like to customise this, you may use the MockConfig class in your tests or in your setUp methods.

```php
<?php

MockConfig::setFixturePath('tests/other-directory')
```

#### Preventing Unwanted Requests

Once you have written all of your tests, you might want to prevent accidental API requests in the future for fixtures that don't exist. If you would like Saloon to throw exceptions if a fixture does not exist, you may do this with the MockConfig class.

```php
<?php

MockConfig::throwOnMissingFixtures()
```

### Redacting Fixture Information

When using fixtures to record real responses from an API - sometimes the API will return some sensitive information that you shouldn't store in your application's repository, like names of real people, financial data or emails. With Saloon, you can create a custom fixture class and provide a few methods to obscure the information when the data is stored. You can even provide closures for the data replacement so you can use tools like Faker to replace data like-for-like.

{% hint style="info" %}
The first time the request is made and the fixture is stored, the original response won't be redacted. Only future requests made with the fixture will use the redacted recording.
{% endhint %}

Firstly, create a new class in your tests directory and give the class a name. We'll call this class `SingleServerFixture`. Then make sure to extend the base `Fixture` class provided by Saloon.&#x20;

<pre class="language-php"><code class="lang-php">&#x3C;?php

namespace Tests\Fixtures\Forge\SingleServerFixture;

<strong>use Saloon\Http\Faking\Fixture;
</strong>
<strong>class ForgeSingleServerFixture extends Fixture
</strong>{
    //
}
</code></pre>

Next, we need to give the fixture a name. Just extend the `defineName` method and give the fixture a name. You can still use slashes in this directory to denote folders.

```php
<?php

namespace Tests\Fixtures\Forge\SingleServerFixture;

use Saloon\Http\Faking\Fixture;

class SingleServerFixture extends Fixture
{
    protected function defineName(): string
    {
        return 'forge/singleServer';
    }
}
```

Now you can use a few different methods to redact your fixture data. You can use the `defineSensitiveHeaders` method to swap any headers out with sensitive data, the `defineSensitiveJsonParameters` for JSON responses or `defineSensitiveRegexPatterns` to define regex patterns for Saloon to find.&#x20;

```php
<?php

namespace Tests\Fixtures\Forge\SingleServerFixture;

use Saloon\Http\Faking\Fixture;

class SingleServerFixture extends Fixture
{
    protected function defineName(): string
    {
        return 'forge/singleServer';
    }
    
    protected function defineSensitiveHeaders(): array
    {
        return [];
    }

    protected function defineSensitiveJsonParameters(): array
    {
        return [];
    }
    
    protected function defineSensitiveRegexPatterns(): array
    {
        return [];
    } 
}
```

#### Replacing Sensitive Headers

If a particular header contains sensitive information, you may use this method to define the headers that are sensitive and what to replace them with. You may use a value or a closure for a custom replacement based on the value

{% tabs %}
{% tab title="Simple - Using Arrays" %}

```php
protected function defineSensitiveHeaders(): array
{
    return [
        // Key = Header Name
        // Value = Replacement Value
    
        'Content-Type' => 'REDACTED',
    ];
}
```

{% endtab %}

{% tab title="Advanced - Using Closures" %}

```php
protected function defineSensitiveHeaders(): array
{
    return [
        // Key = Header Name
        // Value = Replacement Value / Closure
    
        'Content-Type' => static function (string $value) {
            return substr_replace($value, 'xxx', 1);
        },
    ];
}
```

{% endtab %}
{% endtabs %}

#### Replacing Sensitive JSON Parameters

If the response you are dealing with is JSON - you can define sensitive JSON keys that should be replaced. For example, if there is a "name" key - anytime this is found the value will be replaced with the replacement you define. The keys provided are recursive, so if "name" is within a nested JSON array, it will still be replaced.

{% tabs %}
{% tab title="Simple - Using Arrays" %}

```php
protected function defineSensitiveJsonParameters(): array
{
    return [
        // Key = JSON Key
        // Value = Replacement Value
    
        'name' => 'REDACTED',
    ];
}
```

{% endtab %}

{% tab title="Advanced - Using Closures" %}

```php
protected function defineSensitiveJsonParameters(): array
{
    return [
        // Key = JSON Key
        // Value = Replacement Value / Closure
    
        'name' => static function (string $value) {
            // Example: You could use something like Faker to generate
            // fake information and provide a similar structure.
        
            return faker()->firstName();
        },
    ];
}
```

{% endtab %}
{% endtabs %}

#### Finding and Replacing From Regex Patterns

When the API response you are given is not JSON - it can be difficult to replace the information. When you encounter APIs like these, you can use the `defineSensitiveRegexPatterns` method to find and replace regex patterns in an all-string response body. The key of the array is the regex pattern and the value is the replacement.

{% tabs %}
{% tab title="Simple - Using Arrays" %}

```php
protected function defineSensitiveRegexPatterns(): array
{
    return [
        // Key = Regex Pattern
        // Value = Replacement Value
        
        '/@[a-z0-9_]{0,100}/' => 'REDACTED-TWITTER-HANDLE',
    ];
}
```

{% endtab %}

{% tab title="Advanced - Using Closures" %}

```php
protected function defineSensitiveRegexPatterns(): array
{
    return [
        // Key = Regex Pattern
        // Value = Replacement Value
        
        '/@[a-z0-9_]{0,100}/' => static function (string $value) {
            // Example: You could use something like Faker to generate
            // fake information and provide a similar structure.
        
            return faker()->userName();
        },
    ];
}
```

{% endtab %}
{% endtabs %}

#### Using your custom fixtures

Once you have created your custom fixture class with the redaction configuration, you can simply use it instead of `MockResponse::fixture()` in your tests. All detection methods in the mock client work with this type of fixture too.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$mockClient = new MockClient([
<strong>    GetServerRequest::class => new SingleServerFixture;
</strong>]);
</code></pre>

### Preventing Stray API Requests

Once you have written your tests - it's a good idea to ensure that no real API requests are made in the future while running those tests. This is because you could be making requests when you don't intend to which could incur charges or worse, make real changes to data you don't expect. With Saloon, you can prevent stray API requests with the global `Config` helper. Simply in your tests, call the `Config::preventStrayRequests()` method and you should be good to go!&#x20;

It's recommended that you place this in your `Pest.php` file or in your `setUp()` method to make sure it's used on every test.

{% tabs %}
{% tab title="Pest.php (PEST)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

<strong>use Saloon\Helpers\Config;
</strong>
beforeEach(function () {
    Config::preventStrayRequests();
});
</code></pre>

{% endtab %}

{% tab title="TestCase.php (PHPUnit)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

<strong>use Saloon\Helpers\Config;
</strong>
class TestCase {

    public function setUp()
    {
<strong>        Config::preventStrayRequests();
</strong>    }

}
</code></pre>

{% endtab %}
{% endtabs %}

### Advanced Usage

You may want to return custom fixtures based on the request without specifying exact names of fixtures. For example, I might want to build a fixture name based on the name of the request being sent. You may use a closure inside the mock client and write the custom logic to meet these needs.

{% tabs %}
{% tab title="Non-Laravel" %}

```php
<?php

$mockClient = new MockClient([
    '*' => function (PendingRequest $request) {
        $reflection = new ReflectionClass($request->getRequest());

        return MockResponse::fixture($reflection->getShortName());
    },
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

// This will create a fixture called "GetForgeServerRequest"

$request = new GetForgeServerRequest(12345);
$response = $forge->send($request);

// This will create a fixture called "GetAllForgeServersRequest"

$request = new GetAllForgeServersRequest($data);
$response = $forge->send($request);
```

{% endtab %}

{% tab title="Laravel" %}

```php
<?php

Saloon::fake([
    '*' => function (PendingRequest $request) {
        $reflection = new ReflectionClass($request->getRequest());

        return MockResponse::fixture($reflection->getShortName());
    },
]);

$forge = new ForgeConnector;

// This will create a fixture called "GetForgeServerRequest"

$request = new GetForgeServerRequest(12345);
$response = $forge->send($request);

// This will create a fixture called "GetAllForgeServersRequest"

$request = new GetAllForgeServersRequest($data);
$response = $forge->send($request);
```

{% endtab %}
{% endtabs %}

Another example from [Astrotomic's Steam SDK](https://github.com/Astrotomic/steam-sdk) allows you to create a directory for each request. This is really useful for organising your mock fixtures.

```php
<?php

$mockClient = new MockClient([
    '*' => function (PendingRequest $pendingRequest) {
        $name = implode('/', array_filter([
             parse_url($pendingRequest->getUrl(), PHP_URL_HOST),
             mb_strtoupper($pendingRequest->getMethod() ?? 'GET'),
             parse_url($pendingRequest->getUrl(), PHP_URL_PATH),
             http_build_query(array_diff_key($pendingRequest->query()->all(), array_flip(['key', 'format']))),
        ]));
          
        return MockResponse::fixture($name);
    },
]);
```


# Mock Responses

Saloon makes it easy to fake API integrations in your tests. In your tests, you will need to create an instance of `MockClient` . The `MockClient` accepts an array of `MockResponses` which when used on a request, will respond with a fake response without actually sending a real request to the web. This helps speed up tests massively and can help you test your application for different API response scenarios, like a 404 error or 500 error.

### Registering a MockClient

Request mocking starts with a `MockClient`. This class can be applied directly to a connector instance to be used across all requests, or it can be applied on a per-request basis.

{% tabs %}
{% tab title="All Requests (Connector)" %}

```php
<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    MockResponse::make(['name' => 'Sam'], 200),
    MockResponse::make(['name' => 'Alex'], 200),
    MockResponse::make(['error' => 'Server Unavailable'], 500),
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

// All requests sent with the $forge instance will use the Mock Client
```

{% endtab %}

{% tab title="Individual Request" %}

```php
<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    MockResponse::make(['name' => 'Sam'], 200),
    MockResponse::make(['name' => 'Alex'], 200),
    MockResponse::make(['error' => 'Server Unavailable'], 500),
]);

$forge = new ForgeConnector;
$request = new GetAllServersRequest;

// Send a request with a MockClient

$forge->send($request, $mockClient);
```

{% endtab %}
{% endtabs %}

### Using Laravel?

If you are using Laravel, as well as the Saloon Laravel helper library, you don't have to use `withMockClient` on every instance of your connector. You may use the `Saloon` facade `fake` method to define your mock responses. This is a built-in global MockClient that when used will be applied to *all* Saloon requests sent in your application.

```php
<?php

use Saloon\Laravel\Saloon;

Saloon::fake([
    MockResponse::make(['name' => 'Sam'], 200),
    MockResponse::make(['name' => 'Alex'], 200),
    MockResponse::make(['error' => 'Server Unavailable'], 500),
]);

// All requests sent with the $forge instance will use the global MockClient

$forge = new ForgeConnector;
$forge->send(new GetUserRequest) // Will return with `['name' => 'Sam']` and status `200`
```

### The MockResponse class

The **MockResponse** class is used to create fake responses Saloon understands. It can accept a body, status, and headers. These properties will be populated in the fake response. The response body accepts an array for a JSON body or plain strings to simulate other responses, like XML.

```php
use Saloon\Http\Faking\MockResponse;

MockResponse::make(['name' => 'Sam'], 200, ['Content-Type' => 'application/json']);
```

### Basic Usage (Sequence Mocking)

Basic sequence testing allows you to define a number of fake responses. When your application uses Saloon, it will pull out the next response in the sequence, removing it from the sequence. Each response can only be consumed once.

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    MockResponse::make(['name' => 'Sam'], 200),
    MockResponse::make(['name' => 'Alex'], 200),
    MockResponse::make(['error' => 'Server Unavailable'], 500),
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

$otherService = new OtherServiceConnector;
$otherService->withMockClient($mockClient);

$forge->send(new GetUserRequest) // Will return with `['name' => 'Sam']` and status `200`
$forge->send(new GetUserRequest) // Will return with `['name' => 'Alex']` and status `200`
$otherService->send(new GetUserRequest) // Will return with `['error' => 'Server Unavailable']` and status `500`
```

### Connector Mocking

You may also explicitly define the connector to target with mock responses. Unlike sequence tests, these are always used and are never consumed.

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    ForgeConnector::class => MockResponse::make(['name' => 'Sam'], 200),
    OtherServiceConnector::class => MockResponse::make(['name' => 'Alex'], 200),
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

$otherService = new OtherServiceConnector;
$otherService->withMockClient($mockClient);

$forge->send(new GetUserRequest) // Will return with `['name' => 'Sam']` and status `200`
$forge->send(new GetUserRequest) // Will return with `['name' => 'Sam']` and status `200`
$otherService->send(new OtherServiceRequest) // Will return with `['name' => 'Alex']` and status `200`
```

### Request Mocking

You may also explicitly define the request to target with mock responses that are used. Unlike sequence tests, these are always used and are never consumed.

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    GetForgeServerRequest::class => MockResponse::make(['name' => 'Sam'], 200),
    OtherServiceRequest::class => MockResponse::make(['name' => 'Alex'], 200),
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

$otherService = new OtherServiceConnector;
$otherService->withMockClient($mockClient);

$forge->send(new GetUserRequest) // Will return with `['name' => 'Sam']` and status `200`
$otherService->send(new OtherServiceRequest) // Will return with `['name' => 'Alex']` and status `200`
```

### URL Mocking

You can also define fake responses for particular URL patterns. Whenever a request is made for a particular pattern, Saloon will respond to that request.

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    'forge.laravel.com/api/*' => MockResponse::make(['name' => 'Sam'], 200),
    'samcarre.dev/*' => MockResponse::make(['name' => 'Alex'], 200),
    'samcarre.dev/exact' => MockResponse::make(['name' => 'Taylor'], 200), // Exact requests
    '*' => MockResponse::make(['name' => 'Wildcard'], 200), // Any other requests
]);

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

$otherService = new OtherServiceConnector;
$otherService->withMockClient($mockClient);

$forge->send(new GetForgeServerRequest) // Will return with `['name' => 'Sam']` and status `200`
$otherService->send(new OtherServiceRequest) // Will return with `['name' => 'Alex']` and status `200`
$forge->send(new ExactRequest) // Will return with `['name' => 'Taylor']` and status `200`
$forge->send(new WildcardServiceRequest) // Will return with `['name' => 'Wildcard']` and status `200`
```

### Adding Expectations

When using faking responses, it's important to be able to check that a specific make request was sent and with the correct data, headers, and config. Saloon provides you with various ways to add expectations to your tests.

#### Available Expectations

* AssertSent
* AssertNotSent
* AssertSentJson
* AssertNothingSent
* AssertSentCount

To use one of the expectations, you can simply call the method on your **Mock Client**.

```php
<?php

// ...

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

$response = $forge->send(new GetForgeServerRequest(123456));

$mockClient->assertSent(GetForgeServerRequest::class);
```

The **AssertSent / AssertNotSent** are the two most powerful expectation methods. They can accept a Saloon request, a URL pattern or even a closure where you define if a request/response is what you expect.

```php
<?php

use Saloon\Contracts\Request;
use Saloon\Contracts\Response;

// ...

$forge = new ForgeConnector;
$forge->withMockClient($mockClient);

$response = $forge->send(new GetForgeServerRequest(123456));

$mockClient->assertSent(GetForgeServerRequest::class);

$mockClient->assertSent('/servers/*');

$mockClient->assertSent(function (Request $request, Response $response) {
    return $request instanceof GetForgeServerRequest
        && $request->serverId === 123456;
});
```

### Mocking Exceptions

Your test may require you to mock your own exceptions that might get thrown. To mock an exception, chain the `throw` method after you have defined your mock response.

```php
<?php

$mockClient = new MockClient([
    MockResponse::make(['name' => 'Sam'], 200)->throw(new MyException('Something bad!'))
]);

// ...
```

### Using closures for mocking

Sometimes, you may need to return a custom mock response based on the request that is currently trying to be sent. With closure/callable mocking, you can do this. Just provide an anonymous function or an invokable class when defining the mock response, and you will get access to the current `PendingRequest` before it is converted into a mock response. This is great if you have stored fixtures based on the request and need to load the fixture data up. This will work with all of the methods above of mocking the request.

```php
<?php

use Saloon\Contracts\PendingRequest;

$mockClient = new MockClient([
    function (PendingRequest $request): MockResponse {
        // Write some custom logic here...

        return new MockResponse([...]);
    },
]);
```

### Preventing Stray API Requests

Once you have written your tests - it's a good idea to ensure that no real API requests are made in the future while running those tests. This is because you could be making requests when you don't intend to which could incur charges or worse, make real changes to data you don't expect. With Saloon, you can prevent stray API requests with the global `Config` helper. Simply in your tests, call the `Config::preventStrayRequests()` method and you should be good to go!&#x20;

It's recommended that you place this in your `Pest.php` file or in your `setUp()` method to make sure it's used on every test.

{% tabs %}
{% tab title="Pest.php (PEST)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

<strong>use Saloon\Helpers\Config;
</strong>
beforeEach(function () {
    Config::preventStrayRequests();
});
</code></pre>

{% endtab %}

{% tab title="TestCase.php (PHPUnit)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

<strong>use Saloon\Helpers\Config;
</strong>
class TestCase {

    public function setUp()
    {
<strong>        Config::preventStrayRequests();
</strong>    }

}
</code></pre>

{% endtab %}
{% endtabs %}


# Cookbook

Here you can find common scenarios encountered when building API integrations with Saloon. If you have any suggestions, please contribute by editing this page!

### Authenticating before every request&#x20;

Some API integrations require you to make a request to create an access token that a subsequent request will use. When building this, it can add a level of complexity to your application because you have to make two requests for every single resource. Let's say you have two requests for an API. You have:

* GetAccessTokenRequest&#x20;
* GetSongsByArtistRequest

But in order to make an API call `GetSongsByArtistRequest` you must obtain an access token by calling `GetAccessTokenRequest` first. Let's also assume that the `GetAccessTokenRequest` requires a username and password. Let's see how this may be implemented currently in your application.

```php
<?php

$api = new MusicApi();
$authResponse = $api->send(new GetAccessTokenRequest(username: 'Sam', password: 'yee-haw'));
$token = $authResponse->json()['token'];

$songsRequest = new GetSongsByArtistRequest('Luke Combs');
$songsRequest->withTokenAuth($token);

$response = $api->send($songsRequest);
```

This is pretty cumbersome. Every time you have to make a request, you have to write all these lines of code. One way you can get around this is by utilizing the `boot` method on your connector. Let's try refactoring this.

In our connector, we'll extend the `boot()` method which is a method that runs before every request that is sent through that connector, and you get access to the special `PendingRequest` class which is a class that is used to store all the information of a request to be sent in a temporary way. We'll move our logic into here.&#x20;

```php
<?php

use Saloon\Contracts\PendingRequest;

class MusicApi extends Connector
{
    // You should move any authentication requirements into the constructor
    // of the connector so your users have to provide the authentication
    // requirements before instantiating the connector.

    public function __construct(
        protected string $username,
        protected string $password,
    )
    {
        //
    }
    
    public function boot(PendingRequest $pendingRequest): void
    {
        // Let's start by returning early if the request being sent is the
        // GetAccessTokenRequest. We don't want to create an infinite loop
    
        if ($pendingRequest->getRequest() instanceof GetAccessTokenRequest) {
            return;
        }
        
        // Now let's make our authentication request. Since we are in the
        // context of the connector, we can just simply call $this and
        // make another request!
        
        $authResponse = $this->send(new GetAccessTokenRequest($this->username, $this->password));
        
        // Now we'll take the token from the auth response and then pass it
        // into the $pendingRequest which is the original GetSongsByArtistRequest.
        
        $pendingRequest->withTokenAuth($authResponse->json()['token']);
    }
}
```

Now when we send any request using this connector, the authentication will be handled every time! We do have to make sure to pass in our authentication requirements (username, password) into the connector, but this is way better than repeating the same code. Saloon is all about standardisation!

```php
<?php

$api = new MusicApi(
    username: 'Sam',
    password: 'yee-haw',
);

$response = $api->send(new GetSongsByArtistRequest('Luke Combs'));

// 200 body: [{name: 'When it rains, it pours'}]
```

You may also write additional logic inside of the boot method, like caching access tokens and reusing them.&#x20;

### Speeding Up Requests By Utilizing Laravel's Container

If you are using Laravel, then you are probably aware of its powerful container that keeps objects open in the background while you make requests or process jobs. Saloon uses Guzzle under the hood, which keeps connections open for as long as it can - which is why subsequent requests using the same connector is usually faster. Typically though, the Guzzle HTTP client will be destructed and connections closed between requests/jobs. This results in increased request times if you are doing lots of API calls across jobs or requests because it is establishing a new connection each time.

If you are using Laravel, you can bind an instance of Saloon's `GuzzleSender` inside of its container and it will keep connections open between queued jobs and requests (if you are using Octane). This can provide a nice speed boost if you are using the same API service, especially if you are frequently accessing an API, like migrating data from an external API into your application.

First, you should create a new singleton in one of your service provider's register methods, and return a new instance of `GuzzleSender`. The `AppServiceProvider` is a good place to put this, or you could create a new `HttpServiceProvider` for this.

```php
<?php

use Saloon\Http\Senders\GuzzleSender;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GuzzleSender::class, fn () => new GuzzleSender);
    }
}
```

Now, inside your Saloon connectors, you should overwrite the `defaultSender` method and resolve the sender which you have bound to the container.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Sender;
use Saloon\Http\Senders\GuzzleSender;

class SpotifyConnector extends Connector
{
    protected function defaultSender(): Sender
    {
        return resolve(GuzzleSender::class);
    }
}
```

Now when you make requests with this connector, it will re-use the same Guzzle sender and the same Guzzle client under the hood!


# Tutorials & Blog Posts

### Video Tutorials

#### Saloon - A PHP package to simplify API integrations

{% embed url="<https://www.youtube.com/watch?v=r3AU6uYQGt4>" %}
Created by Andrew Schmelyun
{% endembed %}

#### Demystifying Saloon: A Step-by-Step Guide

{% embed url="<https://www.youtube.com/watch?v=Pl51BkVTn8M>" %}
Created by Ruslan Steiger
{% endembed %}

#### Integrate an API with Laravel and Saloon (FR)

{% embed url="<https://www.youtube.com/watch?v=8rPXqmBG99c>" %}
Created by Nord Coders
{% endembed %}

## API Integrations using Saloon in Laravel

Steve McDougall wrote a super in-depth tutorial for building a GitHub integration in Laravel using Saloon that enabled you to see a status of repository workflows. It's a great example of using Saloon and is good to follow if you would like to get started

<https://laravel-news.com/api-integrations-using-saloon-in-laravel>

### Podcast

Want to \*hear\* more about Saloon? The creator of Saloon, Sam Carré and Jake Bennet recorded an episode all about how Saloon works on the North Meets South podcast. Click on the link below to listen to the podcast episode.

#### [Listen on their website](https://www.northmeetssouth.audio/112)

[**Listen on Apple Podcasts**](https://podcasts.apple.com/us/podcast/north-meets-south-web-podcast/id1123508667?i=1000551046555)

[**Listen on Spotify**](https://open.spotify.com/episode/1oT4LC4bQSx2PNNMQDAkIc?si=b695129aa0d34327\&nd=1)


# Known Issues

### Usage of anonymous functions with long-running processes like Laravel Queues

There appears to be an issue with PHP which means any object that has an anonymous function inside of one of its properties will result in it not properly being destructed or running the `__destruct()` method. With Saloon, take care when using anonymous functions inside your connector or solo request because we have found that if you have an anonymous function/closure inside of the connector and when running it from within a long-running process like Laravel Queues, it won't close connections.

This is because as mentioned above, the connector isn't able to be appropriately destructed which results in the sender and the sender's HTTP client like Guzzle not being able to be destructed and finally resulting in a "leak" of connections. This can cause further issues like the operating system running out of file handles and causing errors like "Too many files open".&#x20;

You can workaround this by changing the way you define your anonymous closure. You can:

* Move the logic into an invokable class
* Make the closure *static*

```php
<?php

class SDK extends Connector
{
    protected Closure $callable;

    public function __constuct()
    {  
        // ❌ This will cause connection leaks
        $this->middleware()->onRequest(function () {
            //
        });
        
        // ✅ Using an invokable class will work
        $this->middleware()->onRequest(new MyRequestMiddleware);

        // ✅ Using static will work
        $this->middleware()->onRequest(static function () {
            //
        });
    }
}
```


# Credits

### Development

* [Juse Less](https://github.com/juse-less) for contributing to many core features of Saloon v2 but mainly pagination and debugging.
* [Tom Witkowski](https://github.com/Gummibeer) for contributing to Saloon's fixture recording and helping decide many core parts of version two like data transfer objects and the error handler.

### Artwork

Special thanks to [Caneco](https://twitter.com/caneco) for the logo and branding ✨

### Thank you.

Special thank you for using Saloon. It's an honour to build something that people enjoy and actively use. I hope it will be useful for many years ahead.

<https://www.youtube.com/watch?v=vPZ9DLBNQLY>


# Showcase

{% hint style="info" %}
Want to add your application or SDK to Saloon's showcase page? Edit this page on Github! 🤠
{% endhint %}

<table data-card-size="large" data-view="cards"><thead><tr><th></th><th></th><th data-hidden></th><th data-hidden></th><th data-hidden data-card-cover data-type="files"></th><th data-hidden data-card-target data-type="content-ref"></th></tr></thead><tbody><tr><td>🌲</td><td>astrotomic/ecologi-sdk</td><td>1</td><td></td><td></td><td><a href="https://github.com/Astrotomic/ecologi-sdk">https://github.com/Astrotomic/ecologi-sdk</a></td></tr><tr><td>🪐</td><td>astrotomic/tmdb-sdk</td><td>2</td><td></td><td></td><td><a href="https://github.com/Astrotomic/tmdb-sdk">https://github.com/Astrotomic/tmdb-sdk</a></td></tr><tr><td>💰</td><td>myoutdeskllc/salesforce-php</td><td>3</td><td></td><td></td><td><a href="https://github.com/MyOutDeskLLC/SalesforcePhp">https://github.com/MyOutDeskLLC/SalesforcePhp</a></td></tr><tr><td>🤑</td><td>sawirricardo/midtrans-api</td><td></td><td></td><td></td><td><a href="https://github.com/sawirricardo/midtrans-api">https://github.com/sawirricardo/midtrans-api</a></td></tr><tr><td>📃</td><td>carboneio/carbone-sdk-php</td><td></td><td></td><td></td><td><a href="https://github.com/carboneio/carbone-sdk-php">https://github.com/carboneio/carbone-sdk-php</a></td></tr><tr><td>📞</td><td>samuelmwangiw/africastalking-laravel</td><td></td><td></td><td></td><td><a href="https://github.com/SamuelMwangiW/africastalking-laravel">https://github.com/SamuelMwangiW/africastalking-laravel</a></td></tr><tr><td>💻</td><td>samuelmwangiw/linode</td><td></td><td></td><td></td><td><a href="https://github.com/SamuelMwangiW/linode">https://github.com/SamuelMwangiW/linode</a></td></tr><tr><td>🌱</td><td>alphaolomi/json-placeholder</td><td></td><td></td><td></td><td><a href="https://github.com/alphaolomi/json-placeholder">https://github.com/alphaolomi/json-placeholder</a></td></tr><tr><td>💬</td><td>flourishlabs/saloon-slack</td><td></td><td></td><td></td><td><a href="https://github.com/flourishlabs/saloon-slack">https://github.com/flourishlabs/saloon-slack</a></td></tr><tr><td>📦</td><td>alphaolomi/laravel-repman</td><td></td><td></td><td></td><td><a href="https://github.com/alphaolomi/laravel-repman">https://github.com/alphaolomi/laravel-repman</a></td></tr><tr><td>📊</td><td>njoguamos/laravel-plausible</td><td></td><td></td><td></td><td><a href="https://github.com/njoguamos/laravel-plausible">https://github.com/njoguamos/laravel-plausible</a></td></tr><tr><td>🧠</td><td>probots-io/pinecone-php</td><td></td><td></td><td></td><td><a href="https://github.com/probots-io/pinecone-php">https://github.com/probots-io/pinecone-php</a></td></tr><tr><td>🇨🇴</td><td>felipeva/api-colombia-php</td><td></td><td></td><td></td><td><a href="https://github.com/felipeva/api-colombia-php">https://github.com/felipeva/api-colombia-php</a></td></tr></tbody></table>


# Saloon

Build beautiful API integrations and SDKs with Saloon

Saloon is a PHP library that helps you rapidly build third party API integrations or SDKs. It's configured to be production-ready by default, offers a simple, standardised development flow for your whole team and has utilities to help test your API integrations before you ship to production. Saloon is loved by thousands of developers and downloaded over 2 million times.

[![Build Status](https://github.com/saloonphp/saloon/actions/workflows/tests.yml/badge.svg)](https://img.shields.io/github/actions/workflow/status/saloonphp/saloon/tests.yml?label=tests)![](https://img.shields.io/packagist/dm/saloonphp/saloon)![](https://img.shields.io/github/stars/saloonphp/saloon)

<figure><img src="https://2077574264-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2FAnnOFZ5KwPjnXI3FPOQw%2Fuploads%2FHWiSnEu4W13lqJJ6oG7W%2FIntroducing%20Saloon%20v3.png?alt=media&#x26;token=582f2f0b-59b3-4dc5-b860-fec4b1e24190" alt=""><figcaption><p>Introducing Saloon v3</p></figcaption></figure>

### Key Features

* Lightweight, only depends on one library - Guzzle.
* Offers "Connector" and "Request" classes to encapsulate API integrations into files
* Allows you to record real requests in your tests so you have accurate testing data for your assertions.
* Works in any PHP framework, or just regular PHP applications (with Composer)
* Comes with powerful plugins to handle caching, rate limits, data transfer objects, XML, and more

{% content-ref url="the-basics/installation" %}
[installation](https://docs.saloon.dev/the-basics/installation)
{% endcontent-ref %}


# What's new in v3

We are excited to announce the release of Saloon version three, which is designed to provide an even better developer experience than before. This version comes with numerous internal changes that enhance maintainability, performance, and code readability. We have taken into account the feedback we received from users of Saloon v2 and made intentional modifications to improve your experience.

To begin it's worth mentioning that Saloon has become a lot more lightweight in version three!

> **333 changed files** with <mark style="color:green;">**4,063 additions**</mark> and <mark style="color:red;">**7,576 deletions**</mark>

### Changes in version three

#### Improved Pagination

One of the biggest changes is how pagination in Saloon works. After seeing a few people use it - we felt that the developer experience could be better, so we revisited the whole feature. With Saloon v3, it no longer comes bundled into Saloon which reduces code but the pagination plugin provides a beautiful, expressive way to build and customise paginators.

One of the motivations for rebuilding the pagination was customisation. Previously, you would have to extend the paginator class you are already using and guess which methods needed to be changed. The new pagination has been massively simplified and each of the paginators use the same methods to apply their pagination. This makes them much easier to build and maintain. Here is how the pagination definition looks in version three:

```php
<?php

class SpotifyConnector extends Connector implements HasPagination
{
    // ...
    
    public function paginate(Request $request): PagedPaginator
    {
        // You define your own pagination class that extends one of Saloon's
        // base pagination classes. This makes overwriting super easy.
    
        return new class(connector: $this, request: $request) extends PagedPaginator
        {
            // Determine if we are on the last page...
        
            protected function isLastPage(Response $response): bool
            {
                return is_null($response->json('next_page_url'));
            }
            
            // Get page items...
            
            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('items');
            }
        };
    }
}
```

#### PSR Support

Another important change to Saloon v3 was to lean more into the building and use of PSR-7 requests. Previously, Saloon would use Guzzle's configuration options to build up query parameters, request body and the URI. Now, Saloon builds all PSR requests in-house and passes that request to the sender. This provides a few key benefits over letting Guzzle handle it:

* **Better developer experience** - with less hidden "magic" going on behind the scenes, you'll be able to debug and dump the PSR request Saloon creates to see how your request will be sent and what you're sending.
* **Decoupled further from Guzzle** - Saloon now only uses Guzzle to send your requests which means Saloon is much less dependent on Guzzle.
* **Lower-level customization** - You will be able to modify the PSR-7 request before it is sent, which gives you much better control than before.
* **New PSR methods -** You will now be able to create a PSR-7 request from a `PendingRequest` class.

{% hint style="info" %}
This is an internal change only, and you won't need to change how your implementation works. You'll still use connectors and requests.
{% endhint %}

#### Global Retry System

Another really exciting feature in version three is the ability to configure retrying functionality at a connector level for all of your requests. Previously, the only way to configure the retry functionality was to do it in-line like this:

```php
$response = $connector->sendAndRetry($request, 5);
```

This worked well, but it doesn't really follow one of Saloon's core values - to have beautiful, reusable code. Now you can define retrying at a class level. You just have to add the `$tries` property to either your connector or request to get it to work. After this, if you use `$connector->send()` and the request fails, it will be retried automatically. This is especially useful for SDK development or building against unstable APIs that need retrying at a connector level.

```php
class ForgeConnector extends Connector
{
    /**
     * The number of times a request should be retried if a failure response is returned.
     *
     * Set to null to disable the retry functionality.
     */
    public ?int $tries = 5;
}
```

#### New Response Body Methods

Saloon version three has a few new methods for the `Response` class to make it even easier to use in your application.

* **saveBodyToFile($pathOrResource)** - This method allows you to save the raw body straight to a file or into an open file resource while keeping memory usage low.
* **getRawStream()** - This method is similar to the existing `stream()` method but will return a file `resource` instead of a `StreamInterface`. This method is really useful when passing the stream into other libraries like League's Flysystem library.
* **getConnector()** - This method is a nice shortcut to get the connector from the response.
* **getPsrRequest()** - This method gives you the PSR request that was built when sending the request - which is the lowest level request class before going through the HTTP client.

#### Removed Interfaces For Maintainability

With Saloon v2 - almost every class had an interface that acted as a blueprint for how the class should be structured. This was a decision made in version two to help make Saloon as flexible as possible. With version three, the decision was made to go back on this flexibility. While the added flexibility of giving the developer the freedom to make their own implementations - it added unnecessary complication and blocked Saloon from nice features and methods that could have been added before version three.

We believe that most Saloon users would just use Saloon's implementation and adequate customization can be achieved with inheritance.&#x20;

Many interfaces (contracts) were removed with version three but some of the major ones include:

* **Saloon\Contracts\Connector**
* **Saloon\Contracts\Request**
* **Saloon\Contracts\PendingRequest**
* **Saloon\Contracts\Response**

The abstract classes still exist, but without the interfaces - the maintainability of Saloon v3 has improved massively. This means that the current version can be maintained for longer and new features can be released faster and without major version changes.

#### Minimum TLS 1.2 Security

Saloon version three requires Guzzle `^7.6` which introduced support for a minimum TLS version. Since TLS 1.1 has been deprecated for almost three years, Saloon has made the minimum version **TLS 1.2**. This is to promote more secure API integrations and follow best practices however it can be changed using configuration if required.

#### Better Middleware Order

The previous version introduced support for middleware in Saloon and allowed users to add their own middleware. This also introduced some challenges with how some of Saloon's other features worked like mocking and authenticators. Often when the user adds their own middleware, they may not see headers from authenticators or mock clients being applied to the `PendingRequest` class. Saloon v3's middleware order has been improved for the best feature compatibility.

If you are curious, this is how the new middleware order looks:

1. Plugins are "booted"
2. Request and connector properties (Headers, Query Parameters, Config, Middleware) are merged
3. Request and connector body is merged
4. Request and connector delay is merged
5. Authenticators are invoked and their logic applied to the `PendingRequest`
6. Request and connector is "booted"
7. Headers are validated
8. Delay is invoked (if applied)
9. Middleware is run in the following order:
   1. Global middleware
   2. Mock client finds a fake response (if present)
   3. Plugin middleware
   4. **User-added middleware**
   5. Debugging middleware is run (for the final object)

#### Simplified Debugging

Saloon v2 shipped with some useful debugging functionality. It came with numerous drivers for different outputs and worked nicely. With version three, it was decided that debugging should be simpler. From version three, if you need to debug your request or response, you can now use the `debug` method and pass in a closure to see either the `PendingRequest` , PSR request or `Response` class while it is being sent.&#x20;

Additionally, changes were made to the middleware pipeline to allow the debugging middleware to always run at the end of the pipeline, ensuring you'll always see the final result.

```php
$connector->debugRequest(static function (PendingRequest $pendingRequest, Requestinterface $psrRequest) {
    dd($pendingRequest->headers());
});

$connector->debugResponse(static function (Response $response) {
    dd($response->body());
});
```

#### Better Asynchronous Promise Handling

With Saloon v3, promises are handled better. Previously, the `PendingRequest` class would be built up, and middleware run and then the `Promise` was created. With version three, a promise is created straight away and the `PendingRequest` and middleware is only invoked when the promise is being sent. This is good for plugins that need to run when a request is being sent like the rate limit plugin.

#### SDK Helpers

Last, but not least - Saloon v3 has shipped with a `BaseResource` class which is a class that was recommended in the [Building SDKs](https://docs.saloon.dev/digging-deeper/building-sdks#request-resources) chapter of the documentation. Since this class is only small and provides value to the SDK developer, this class has now been bundled.


# Upgrading from v2

Saloon version three comes with many improvements over version two, but most of the improvements were internal changes, so the upgrade to v3 from v2 shouldn't be too cumbersome. If you haven't already, it's recommended to read through ["What's new in v3"](https://docs.saloon.dev/upgrade/whats-new-in-v3) to get an idea of what has changed before making the upgrade.

### Upgrade Saloon's Packages

First of all, you should bump Saloon to the next major version in your `composer.json` file. You should also update any of the following plugins if you have them installed.&#x20;

* `saloonphp/saloon` to `^3.0`
* `saloonphp/laravel-plugin` to `^3.0`
* `saloonphp/cache-plugin` to `^3.0`&#x20;
* `saloonphp/laravel-http-sender` to `^2.0`
* `saloonphp/rate-limit-plugin` to `^2.0`
* `saloonphp/pagination-plugin` to `^2.0`

After that, make sure to run the following command to update your `composer.lock` file and your `vendor` directory.&#x20;

```bash
composer update "saloonphp/*"
```

{% hint style="info" %}
The above command will just update the `saloonphp` libraries. If you have other libraries using Saloon you should make sure that they are updated as well.&#x20;
{% endhint %}

### <mark style="color:red;">High</mark> Impact Changes

High-impact changes will most likely be things that everyone needs to update.

#### Minimum TLS 1.2 version enforced

From version three, Saloon uses Guzzle `^7.6` which introduced a new option to configure a minimum TLS version. With Saloon v3, Saloon has set this default to **TLS 1.2.** If any of your API integrations require a lower level of TLS security, you can change the Saloon config while your application is loading.

```php
<?php

use Saloon\Config;

Config::$defaultTlsMethod = STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
```

You can [visit this page on the PHP documentation](https://www.php.net/manual/en/function.stream-socket-enable-crypto.php) to see the different TLS options available.&#x20;

{% hint style="info" %}
**TLS 1.1** has been deprecated since 2021 so it is unlikely that APIs are still using it, but older systems may have not upgraded yet.
{% endhint %}

#### New Paginators

With version three, paginators are no longer included in the core Saloon library and have been moved to a plugin. Additionally, the paginators have been completely rebuilt. Review if you have used any of the pagination functionality and follow the new [pagination guide](https://docs.saloon.dev/installable-plugins/pagination) to rebuild them in the new way.

#### Config and MockConfig classes have moved

Previously, Saloon's `Config` and `MockConfig` classes lived in a `Saloon\Helpers` directory. These classes have now been moved to the root `Saloon` namespace. You should update your `use` statements to the new namespace.

| Find                            | Replace                 |
| ------------------------------- | ----------------------- |
| `use Saloon\Helpers\Config`     | `use Saloon\Config`     |
| `use Saloon\Helpers\MockConfig` | `use Saloon\MockConfig` |

#### Core Interfaces Removed

Another decision that was made was to backtrack on the interfaces that were added in Saloon v2. This may not affect your integration, but a lot of the code examples and recommendations in v2 suggested using the contracts instead of the abstract classes, so you may have a few places in your application where you have imported the interface and not the class. You should find and replace the following main interfaces.

<table><thead><tr><th width="394">Find</th><th>Replace</th></tr></thead><tbody><tr><td><code>use Saloon\Contracts\Connector</code></td><td><code>use Saloon\Http\Connector</code></td></tr><tr><td><code>use Saloon\Contracts\Request</code></td><td><code>use Saloon\Http\Request</code></td></tr><tr><td><code>use Saloon\Contracts\PendingRequest</code></td><td><code>use Saloon\Http\PendingRequest</code></td></tr><tr><td><code>use Saloon\Contracts\Response</code></td><td><code>use Saloon\Http\Response</code></td></tr></tbody></table>

{% hint style="info" %}
There have been many other interfaces removed in v3 however it's unlikely that your application would have used them. They are listed below.
{% endhint %}

#### HasBody Trait Renamed to HasStringBody

During some of the code review to improve readability, the `HasBody` body trait has been renamed to `HasStringBody` to be more explicit. If any of your requests send a **plain string body**, you should find and replace this trait.

<table><thead><tr><th width="349">Find</th><th>Replace</th></tr></thead><tbody><tr><td><code>use Saloon\Traits\Body\HasBody</code></td><td><code>use Saloon\Traits\Body\HasStringBody</code></td></tr><tr><td><code>use HasBody</code></td><td><code>use HasStringBody</code></td></tr></tbody></table>

### <mark style="color:orange;">Medium</mark> Impact Changes

Medium-impact changes are changes that come up for some people but not others.

#### SendAndRetry method argument name changes

The second argument of the `$connector->sendAndRetry()` method has been renamed from `maxAttempts` to `tries`. If you are using named arguments then you will need to replace this name.

<pre class="language-php"><code class="lang-php">$connector->sendAndRetry(
    request: $request,
<strong>    tries: 5,
</strong>);
</code></pre>

#### SendAndRetry handler now expects a Request class and not PendingRequest

If you previously used the `handleRetry` argument when using `$connector->sendAndRetry()` - Saloon will now send an instance of `Request` instead of `PendingRequest` through the closure.

<pre class="language-php"><code class="lang-php">$connector->sendAndRetry(
    request: $request,
    tries: 5,
    interval: 0,
<strong>    handleRetry: static function (Throwable $exception, Request $request) {
</strong>        // $request->headers()->add();
    },
);
</code></pre>

#### Config Method Changes

With version three, some of the methods in the `Saloon\Config` class have been renamed. These changes have been listed below and you should make sure to rename them if you have used them in your application.

| Find                          | Replace                           |
| ----------------------------- | --------------------------------- |
| `Config::middleware()`        | `Config::globalMiddleware()`      |
| `Config::resetMiddleware()`   | `Config::clearGlobalMiddleware()` |
| `Config::resolveSenderWith()` | `Config::setSenderResolver()`     |
| `Config::setDefaultSender()`  | `Config::$defaultSender =`        |

### <mark style="color:blue;">Low</mark> Impact Changes

Low-impact changes are changes that are unlikely to come up unless you have overwritten some of Saloon's functionality or used its interfaces. It's still worth looking through to make sure you haven't missed anything.

#### Methods Renamed On MockResponse

On the `MockResponse` class Saloon had a few methods which have now been renamed to be more consistent with the rest of Saloon. These changes are:

* `getBody()` has changed to `body()`
* `getHeaders()` has changed to `headers()`
* `getStatus()` has changed to `status()`

#### Middleware Arguments Changed

Saloon v3 introduced the ability to reorder middleware. With this, the old `prepend` argument on middleware methods has been replaced with a `pipeOrder` argument. Additionally, the second argument for the middleware is now `name` .&#x20;

#### Methods Removed From PendingRequest Class

The following methods have now been removed from the `PendingRequest` class. You can use the suggested replacement if you are using them in your application.

<table><thead><tr><th width="325">Old</th><th>Suggested Replacement</th></tr></thead><tbody><tr><td><code>$pendingRequest->send()</code></td><td><code>$pendingRequest->getConnector()->send()</code></td></tr><tr><td><code>$pendingRequest->sendAsync()</code></td><td><code>$pendingRequest->getConnector()->sendAsync()</code></td></tr><tr><td><code>$pendingRequest->getSender()</code></td><td><code>$pendingRequest->getConnector()->sender()</code></td></tr></tbody></table>

#### Promise Execution Changes

Previously, Saloon's `PendingRequest` and middleware would be invoked before an asynchronous request was sent (using `$connector->sendAsync()`) - now Saloon has fixed this issue and the `PendingRequest` and middleware will only be invoked while the `Promise` is being resolved - at the same time as sending the request. If your application depended on the old way the middleware was invoked you should make sure your application isn't affected by this change.

#### SimulatedResponsePayload Class Renamed

Previously, the `MockResponse` would extend a class called `SimulatedResponsePayload`. This class has now been renamed to `FakeResponse`. Additionally, methods on the `PendingRequest` like `$pendingRequest->setSimulatedResponsePayload()` has been renamed to `$pendingRequest->setFakeResponse()`. If you were extending the old class or using the methods to set the fake response in middleware, you should rename these methods.

#### Sender Contract Updated

The `Sender` interface has been completely rewritten in Saloon v3. Instead of just the `sendRequest` method. You now have to define three methods. If you have made a custom sender in Saloon, you should make sure this is updated.

```php
<?php

interface Sender
{
    /**
     * Get the factory collection
     */
    public function getFactoryCollection(): FactoryCollection;

    /**
     * Send the request synchronously
     */
    public function send(PendingRequest $pendingRequest): Response;

    /**
     * Send the request asynchronously
     */
    public function sendAsync(PendingRequest $pendingRequest): PromiseInterface;
}
```

#### Date Helper Removed

Saloon v2 shipped with a simple `Date` helper class that wrapped around PHP's `DateTime` classes. This has now been removed in Saloon v3.

#### Other Helpers Renamed And Made Final

Saloon v2 shipped with a few helpers to reduce dependencies. The `Str` and `Arr` helper methods have been renamed to `StringHelpers` and `ArrayHelpers` respectively to prevent accidental imports when using Laravel. Saloon's internal helper classes have also been declared `final` as these shouldn't be extended or used by your application.

#### Other Interfaces (Contracts) Removed

As mentioned above, Saloon v3 removes a lot of interfaces that weren't adding value to the library. Alongside the ones mentioned above, these other interfaces have been removed.

<details>

<summary>Show Removed Interfaces</summary>

* src/Contracts/Arrayable.php
* src/Contracts/Authenticatable.php
* src/Contracts/CanThrowRequestExceptions.php
* src/Contracts/Connector.php
* src/Contracts/DebuggingDriver.php
* src/Contracts/Dispatcher.php
* src/Contracts/HasConfig.php
* src/Contracts/HasDebugging.php
* src/Contracts/HasDelay.php
* src/Contracts/HasHeaders.php
* src/Contracts/HasMiddlewarePipeline.php
* src/Contracts/HasMockClient.php
* src/Contracts/HasPagination.php
* src/Contracts/HasQueryParams.php
* src/Contracts/IntegerStore.php
* src/Contracts/MiddlewarePipeline.php
* src/Contracts/MockClient.php
* src/Contracts/Paginator.php
* src/Contracts/PendingRequest.php
* src/Contracts/Pipeline.php
* src/Contracts/Pool.php
* src/Contracts/Request.php
* src/Contracts/Response.php
* src/Contracts/SimulatedResponsePayload.php

</details>

#### The Base BodyRepository Class Is No Longer Stringable

If you have made your own `BodyRepository` class from the base class and relied on the `__toString` method this has now been removed. Additionally, the interface has been updated and now requires a `toStream()` method. Inside here, you must define how to convert your body type into a Stream using the provided `StreamFactory`.

#### Timeout Enum Removed

The old `Timeout` enum has now been removed. If your application used to use this method, you should migrate to use `Config::$defaultConnectionTimeout` and `Config::$defaultRequestTimeout`.

#### ToArray Method Removed From MultipartValue

Previously, the `MultipartValue` object had a `toArray` method. Now that all body repositories are converted into Streams, this method is no longer required.

#### SimulatedSender Removed

The `SimulatedSender` class which would be used if you were mocking or had a cached response has now been removed.

### Did we miss anything?&#x20;

If we forgot to add something to this upgrade please let us know by [opening an issue](https://github.com/saloonphp/saloon/issues).&#x20;


# Installation

To get started with Saloon, you will need to install it through [Composer](https://getcomposer.org/).&#x20;

```bash
composer require saloonphp/saloon "^3.0"
```

### Using Laravel?

You can install an additional Laravel Plugin for Saloon which provides Artisan console commands, a facade and useful testing helpers. To read more about the Laravel plugin [click here](https://docs.saloon.dev/installable-plugins/laravel-integration).


# Connectors

Connectors are classes which define an API integration's properties like its URL and headers. Any behaviour that should be used on every request like authentication should be defined in a connector. You should have a separate connector for each API integration.

### Getting Started

You should establish a standard place to keep your API connectors. For example in Laravel, a sensible place would be to place them inside the `App/Http/Integrations`folder.&#x20;

Create a new class and extend the abstract `Connector` class. You will then need to define a method `resolveBaseUrl`. This is the URL that points to the API.

```php
<?php

use Saloon\Http\Connector;

class ForgeConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api/v1';
    }
}
```

> If you have installed the Laravel plugin, you can use the **php artisan saloon:connector** command to  create a connector.

### Headers

Most API integrations will have common headers used by all of its requests. You can extend the `defaultHeaders` method to define the headers.

<pre class="language-php"><code class="lang-php">class ForgeConnector extends Connector
{
    // ...

<strong>    protected function defaultHeaders(): array
</strong>    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
</code></pre>

You can also use the `headers` method on a connector instance.

```php
$forge = new ForgeConnector;

$forge->headers()->add('Content-Type', 'text/plain');
```

### Timeout

By default, Saloon will have a connection timeout of 10 seconds and a request timeout of 30 seconds. You can customise this by using the `HasTimeout` trait and specifying a `connectTimeout` and `requestTimeout` property.

```php
use Saloon\Traits\Plugins\HasTimeout;

class ForgeConnector extends Connector
{
    use HasTimeout;
    
    protected int $connectTimeout = 60;
    
    protected int $requestTimeout = 120;
}
```

### Constructor Arguments

Since connectors are just classes, you can define a constructor to populate its default properties. For example, if the URL changes per user of your application you can define this as a constructor argument.

```php
class ForgeConnector extends Connector
{
    public function __construct(protected readonly string $baseUrl) {
        //
    }

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
```

```php
$connector = new ForgeConnector('https://forge.laravel.com/api/v1');
```

### HTTP Client Config

The connector uses a HTTP client to send the request. By default, this client is [Guzzle](https://github.com/guzzle/guzzle). If you would like to define Guzzle config options then you can extend the `defaultConfig`  method.

[Click here to see a list of the available options Guzzle provide.](https://docs.guzzlephp.org/en/stable/request-options.html)

```php
class ForgeConnector extends Connector
{
    // ...

    public function defaultConfig(): array
    {
        return [
            'stream' => true,
        ];
    }
}
```

You can also use the `config` method on a connector instance.

```php
$forge = new ForgeConnector;

$forge->config()->add('stream', true);
```


# Requests

API requests in Saloon each have a class. Within a request class, you define the endpoint, the HTTP method, and properties like headers, query parameters and body.

### Getting Started

Create a class that is in a similar place to your connector. The class should extend the `Request` abstract class. Inside the class, define a `$method` property and a `resolveEndpoint` method. The `resolveEndpoint` method will be combined with the connector's base URL.

```php
<?php

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetServersRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/servers';
    }
}
```

> If you have installed the Laravel plugin, you can use the **php artisan saloon:request** command to create a request.

### Headers

Some API requests require headers to be sent. To define default headers on your request, you can extend the `defaultHeaders` method. These will be merged with the connector's headers.

```php
class GetServersRequest extends Request
{
    // ...

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
```

You can also use the `headers` method on a request instance.

```php
$request = new GetServersRequest;

$request->headers()->add('Content-Type', 'text/plain');
```

### Query Parameters

Some API requests require query parameters to be sent. You can extend the `defaultQuery` method to provide these query parameters.

```php
class GetServersRequest extends Request
{
    // ...
    
    protected function defaultQuery(): array
    {
        return [
            'sort' => 'name', // ?sort=name
            'filter[active]' => 'true', // ?filter[active]=true
        ];
    }
}
```

You can also use the `query` method on a request instance.

```php
$request = new GetServersRequest;

$request->query()->add('sort', 'provider');
```

{% hint style="info" %}
Different APIs sometimes require different query string formats. Saloon uses PHP's built-in [`http_build_query`](https://www.php.net/manual/en/function.http-build-query.php) function to create query strings, but if you need something different, you can reformat the query string in the `Connector::handlePsrRequest()` method (see [Modifying the PSR-7 Request](https://docs.saloon.dev/digging-deeper/psr-support#modifying-the-psr-7-request).
{% endhint %}

### Timeout

By default, Saloon will have a connection timeout of 10 seconds and a request timeout of 30 seconds. You can customise this by using the `HasTimeout` trait and specifying a `connectTimeout` and `requestTimeout` property.

```php
use Saloon\Traits\Plugins\HasTimeout;

class GetServersRequest extends Request
{
    use HasTimeout;
    
    protected int $connectTimeout = 60;
    
    protected int $requestTimeout = 120;
}
```

### Constructor Arguments

Since requests are just classes, you can define a constructor to populate the request's properties. For example, if you are getting a specific resource you would need to pass its ID into the request class.

```php
class GetServerRequest extends Request
{
    protected Method $method = Method::GET;
    
    public function __construct(protected readonly string $id) {
        //
    }
    
    public function resolveEndpoint(): string
    {
        return '/servers/' . $this->id;
    }
}
```

```php
$request = new GetServerRequest('5664b05b-8b32-4523-a0d5-837f5080417a');
```


# Authentication

Saloon has optional authenticator classes to help you with the most common types of authentication. These are classes that can be used on your connector or request.

[You can view all of the built-in authenticators on GitHub.](https://github.com/saloonphp/saloon/tree/v3/src/Http/Auth)

### Authorization "Bearer" Tokens

The `TokenAuthenticator` class can be used to add a `Authorization: Bearer` header to the request. Just extend the `defaultAuth` method and return the authenticator class.

```php
<?php

use Saloon\Http\Auth\TokenAuthenticator;

class ForgeConnector extends Connector
{
    public function __construct(public readonly string $token) {}
    
    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }
}
```

### Basic Auth (Base64 Encoded)

The `BasicAuthenticator` class can be used to add a `Authorization: Basic` header to the request. Just extend the `defaultAuth` method and return the authenticator class.

```php
<?php

use Saloon\Http\Auth\BasicAuthenticator;

class ForgeConnector extends Connector
{
    public function __construct(
        public readonly string $username,
        public readonly string $password
    ){}
    
    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator($this->username, $this->password);
    }
}
```

### Query Parameter

The `QueryAuthenticator` class can be used to add a query parameter to requests. Just extend the `defaultAuth` method and return the authenticator class.

```php
<?php

use Saloon\Http\Auth\QueryAuthenticator;

class ForgeConnector extends Connector
{
    public function __construct(public readonly string $token) {}
    
    protected function defaultAuth(): QueryAuthenticator
    {
        return new QueryAuthenticator('api-key', $this->token);
    }
}
```

### Certificate Auth

The `CertificateAuthenticator` class can be used to authenticate with a custom client-side certificate. An optional password can be provided.  Just extend the `defaultAuth` method and return the authenticator class.

```php
<?php

use Saloon\Http\Auth\CertificateAuthenticator;

class ForgeConnector extends Connector
{
    public function __construct(
        public readonly string $path,
        public readonly string $password
    ){}
    
    protected function defaultAuth(): CertificateAuthenticator
    {
        return new CertificateAuthenticator($this->path, $this->password);
    }
}
```

### Header Auth

The `HeaderAuthenticator` class can be used to authenticate with a custom header. Just extend the `defaultAuth` method and return the authenticator class.

```php
<?php

use Saloon\Http\Auth\HeaderAuthenticator;

class ForgeConnector extends Connector
{
    public function __construct(public readonly string $token) {}
    
    protected function defaultAuth(): HeaderAuthenticator
    {
        return new HeaderAuthenticator($this->token, 'X-API-KEY');
    }
}
```

### Multiple Authenticators

You may need to use multiple authenticator classes together, like a certificate and an authorization token. You can use the `MultiAuthenticator` class for this scenario.

```php
<?php

use Saloon\Http\Auth\MultiAuthenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Auth\CertificateAuthenticator;

class ForgeConnector extends Connector
{
    public function __construct(
        public readonly string $certificate,
        public readonly string $token
    ){}
    
    protected function defaultAuth(): MultiAuthenticator
    {
        return new MultiAuthenticator(
            new CertificateAuthenticator($this->certificate),
            new TokenAuthenticator($this->token),
        );
    }
}
```

### Custom Authentication

If your API integration requires a more complicated authentication process, you can create your own authenticator classes which can be used on your connector. This helps abstract any complicated logic away from the connector keeping it tidy.

```php
<?php

use Saloon\Http\PendingRequest;
use Saloon\Contracts\Authenticator;

class ForgeAuthenticator implements Authenticator
{
    public function __construct(public readonly string $token) {}

    public function set(PendingRequest $pendingRequest): void
    {
        // $pendingRequest->headers()->add(...);
        // $pendingRequest->config()->add(...);
    }
}
```

```php
<?php

class ForgeConnector extends Connector
{
    protected function defaultAuth(): ForgeAuthenticator
    {
        return new ForgeAuthenticator($this->token);
    }
}
```

### The authenticate method

You may also use the `authenticate` method on your connector or request if you would like to use or overwrite an authenticator at runtime.

```php
$forge = new ForgeConnector;

$forge->authenticate(new TokenAuthenticator($user->forge_token));

// $forge->send(...)
```

{% hint style="info" %}
Only one authenticator can be used at the same time.
{% endhint %}


# Sending Body/Data

When sending HTTP requests, a common requirement is to send data to the server with POST, PUT, or PATCH requests, like JSON, XML or multipart data. Saloon makes this easy for you with built-in body traits.

### Getting Started

To get started, you will need to add the `HasBody` interface to your request. This interface is required as it tells Saloon to look for a `body()` method supplied by one of the body traits. Without this interface, Saloon will not send any request body to the HTTP client. Also make sure to change your method to POST, PUT or PATCH depending on the requirements of the API.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add a trait to provide an implementation for the missing `body()` method. Saloon has a trait for all the common types of request bodies.

Continue reading below to understand more about the specific body type that you need.

{% content-ref url="request-body-data/json-body" %}
[json-body](https://docs.saloon.dev/the-basics/request-body-data/json-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/multipart-form-body" %}
[multipart-form-body](https://docs.saloon.dev/the-basics/request-body-data/multipart-form-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/stream-body" %}
[stream-body](https://docs.saloon.dev/the-basics/request-body-data/stream-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/form-body-url-encoded" %}
[form-body-url-encoded](https://docs.saloon.dev/the-basics/request-body-data/form-body-url-encoded)
{% endcontent-ref %}

{% content-ref url="request-body-data/xml-body" %}
[xml-body](https://docs.saloon.dev/the-basics/request-body-data/xml-body)
{% endcontent-ref %}

{% content-ref url="request-body-data/string-plain-text-body" %}
[string-plain-text-body](https://docs.saloon.dev/the-basics/request-body-data/string-plain-text-body)
{% endcontent-ref %}


# JSON Body

To get started, change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasJsonBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasJsonBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasJsonBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: application/json` header for you when using the `HasJsonBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $type,
        protected string $provider
    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->ubuntuVersion,
            'type' => $this->type,
            'provider' => $this->provider,
        ];
    }
}
```

Sometimes there can be too many items to define as constructor arguments. If this is the case for you, you can require the user to provide an array, or even a DTO to populate the default body of your request.

{% tabs %}
{% tab title="Using an array" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected array $payload
    ){}
    
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
```

{% endtab %}

{% tab title="Using a DTO (Data Transfer Object)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;
    
    public function __construct(
<strong>        protected Server $server
</strong>    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->server->ubuntuVersion,
            'type' => $this->server->type,
            'provider' => $this->server->provider,
        ];
    }
}
</code></pre>

{% endtab %}
{% endtabs %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the JSON request body:

* add(string $key, mixed $value) -> **Add items to the JSON body**
* remove(string $key) -> **Remove items from the JSON body**
* merge(…$values) -> **Merge another array of items into the JSON body**
* set(array $value) -> **Overwrite the JSON body entirely**
* all(): array -> **Get all the values of the JSON body**
* isEmpty(): bool -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->add('ubuntu_version', '22.04');

$request->body()->merge([
    'type' => 'app',
    'provider' => 'ocean2',
]);

$body = $request->body()->all();

// array: [
//    'ubuntu_version' => '22.04',
//    'type' => 'app',
//    'provider' => 'ocean2',
// ]
```

### Connector Body

If you would like to also have JSON body on your connector, you can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the properties will be merged. This is useful if you want to have a shared JSON body across every request, like an authentication token.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasJsonBody;

    protected function defaultBody(): array
    {
        return [
            'name' => 'Sam',
        ];
    }
}
```

### JSON Flags

When using the `HasJsonBody` trait, you may want to customise the flags used when encoding the body to be sent to the API you are integrating with. You may do this with the `setJsonFlags` method. It's recommended that you set this within the constructor of your request or connector to ensure that the flags are always used.

For example, if I know that my JSON will contain URLs, but I don't want the URL slashes to be escaped, I can use the `JSON_UNESCAPED_SLASHES` flag. You can also specify multiple flags at once by using the `|` pipe to separate the flags.

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasJsonBody;
    
    public function __construct()
    {
        $this->body()->setJsonFlags(JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
```


# Multipart Form Body

Multipart body (multipart/form-data) is often used in modern APIs as a way to upload a mixture of files and data. Saloon makes handling multipart bodies easy by providing you with a standardised way of interacting with the values to be sent. You don't have to worry about calculating boundaries or properly encoding the multipart values, you can just use simple arrays and a value object to define the values.

To get started, change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class UploadProfilePictureRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasMultipartBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasMultipartBody;
</strong>
class UploadProfilePictureRequest extends Request implements HasBody
{
<strong>    use HasMultipartBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: multipart/form-data` header for you when using the `HasMultipartBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### The MultipartValue

To build up multipart data, Saloon uses a class `Saloon\Data\MultipartValue`. This class is used as a value object and when your request is sent, it will automatically be converted into a stream to be sent to the API. To see how to use this class, continue reading below.

```php
<?php

use Saloon\Data\MultipartValue;

new MultipartValue(
    name: 'picture', // Required: the name of the multipart value
    value: 'file-path-or-stream', // Required: Absolute path or file stream
    filename: 'profile.png', // Optional: File name
    headers: [], // Optional: Headers to be sent with the individual value
)
```

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more. In this example, we are uploading a profile picture to a server so we will expect a file path through our constructor - however, Saloon can support file paths or streams.

```php
<?php

use Saloon\Http\Request;
use Saloon\Data\MultipartValue;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasMultipartBody;

class UploadProfilePictureRequest extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $filePath,
    ){}
    
    protected function defaultBody(): array
    {
        return [
            new MultipartValue(name: 'picture', value: $this->filePath)
        ];
    }
}
```

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the multipart request body:

* add(string $name, mixed $value, string $filename = null, array $headers = \[]) -> **Add a multipart value to the multipart body**
* attach(MultipartValue $value) -> **Attach a multipart directly to the multipart body**
* remove(string $key) -> **Remove an item from the multipart body**
* merge(... $arrays) -> **Merge another array of multipart values into the multipart body**
* set(array $value) -> **Overwrite the entire multipart body with a different set of values**
* all() -> **Get the array of multipart values**
* get(string $key) **-> Get an individual multipart value**
* isEmpty() **-> Check if the multipart body is empty**
* isNotEmpty() **-> Check if the multipart body is not empty**

```php
<?php

$request = new UploadProfilePictureRequest;

$request->body()->add(
    name: 'picture',
    contents: 'your-file-contents-or-stream', 
    filename: 'picture.png', // Optional file name
    headers: [
       // Optional custom headers
    ]
);

// You may also get a multipart value by name.

$request->body()->get('picture'); // MultipartValue class

```

{% hint style="info" %}
The `contents` of the `MultipartValue` class can be either the raw-text contents of the file or a PHP stream/resource.
{% endhint %}

### Connector Body

If you would like to also have a multipart body on your connector, you can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the properties will be merged. This is useful if you want to have a shared value across every request.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Data\MultipartValue;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasMultipartBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasMultipartBody;

    protected function defaultBody(): array
    {
        return [
            new MultipartValue(name: 'logo', value: 'image-contents'), 
            new MultipartValue(name: 'logo', value: StreamObject), 
            new MultipartValue(name: 'logo', value: 'image-contents', filename: 'logo.png', headers: [...]),
        ];
    }
}
```


# XML Body

To get started, change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasXmlBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasXmlBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasXmlBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: application/xml` header for you when using the `HasXmlBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasXmlBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasXmlBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected readonly string $ubuntuVersion,
        protected readonly string $type,
        protected readonly string $provider
    ){}
    
    protected function defaultBody(): string
    {
        return <<<XML
            <?xml version="1.0" ?>
            <root>
                <ubuntu-version>' . $this->ubuntuVersion . '</ubuntu-version>
                <type>' . $this->type . '</type>
                <provider>' . $this->provider . '</provider>
            </root>
        XML;
    }
}
```

{% hint style="info" %}
While you are expected to return a raw string for the XML body, it can be tedious to write XML as text. We have built a library, [XML Wrangler](https://github.com/saloonphp/xml-wrangler) which allows you to read and write XML in a more developer-friendly way.
{% endhint %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the XML request body:

* set(string $value) -> **Overwrite the XML body entirely**
* all(): array -> **Get the XML body**&#x20;
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->set('<?xml version="1.0"?><root></root>');

$body = $request->body()->all();

// string: 'plain-text-response-body'
```

### Connector Body

You can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the body on the request will take priority. If you have the body on just the connector but not on the request, the request will inherit the body on the connector.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasXmlBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasXmlBody;

    protected function defaultBody(): string
    {
        return '<?xml version="1.0"?><root></root>';
    }
}
```

### XML Wrangler

You may be interested in a third-party library written by the maintainer of Saloon - XML Wrangler. It provides a modern syntax for reading and writing XML in PHP.

{% content-ref url="../../installable-plugins/xml-wrangler" %}
[xml-wrangler](https://docs.saloon.dev/installable-plugins/xml-wrangler)
{% endcontent-ref %}


# URL Encoded Body (Form)

To get started, change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasFormBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasFormBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasFormBody;
</strong>
    protected Method $method = Method::POST;
}
</code></pre>

{% hint style="info" %}
Saloon will automatically send the `Content-Type: application/x-www-form-urlencoded` header for you when using the `HasFormBody` trait, however, if you would like to overwrite this behaviour then you can use the `defaultHeaders` method on the request or modify the headers before the request is sent.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $type,
        protected string $provider
    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->ubuntuVersion,
            'type' => $this->type,
            'provider' => $this->provider,
        ];
    }
}
```

Sometimes there can be too many items to define as constructor arguments. If this is the case for you, you can require the user to provide an array, or even a DTO to populate the default body of your request.

{% tabs %}
{% tab title="Using an array" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected array $payload
    ){}
    
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
```

{% endtab %}

{% tab title="Using a DTO (Data Transfer Object)" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;
    
    public function __construct(
<strong>        protected Server $server
</strong>    ){}
    
    protected function defaultBody(): array
    {
        return [
            'ubuntu_version' => $this->server->ubuntuVersion,
            'type' => $this->server->type,
            'provider' => $this->server->provider,
        ];
    }
}
</code></pre>

{% endtab %}
{% endtabs %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the form request body:

* add(string $key, mixed $value) -> **Add items to the form body**
* remove(string $key) -> **Remove items from the form body**
* merge(…$values) -> **Merge another array of items into the form body**
* set(array $value) -> **Overwrite the form body entirely**
* all(): array -> **Get all the values of the form body**
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->add('ubuntu_version', '22.04');

$request->body()->merge([
    'type' => 'app',
    'provider' => 'ocean2',
]);

$body = $request->body()->all();

// array: [
//    'ubuntu_version' => '22.04',
//    'type' => 'app',
//    'provider' => 'ocean2',
// ]
```

### Connector Body

If you would like to also have form body on your connector, you can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the properties will be merged. This is useful if you want to have a shared form body across every request, like an authentication token.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasFormBody;

    protected function defaultBody(): array
    {
        return [
            'name' => 'Sam',
        ];
    }
}
```


# String / Plain Text Body

This body type has been created if you need to send a plain string/text to the server. This body type does not come with a default Content-Type, so you must provide this.

To get started, change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. Without this interface, Saloon will not send any request body to the HTTP client.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasStringBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasStringBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasStringBody;
</strong>
    protected Method $method = Method::POST;
    
    // Define our own Content-Type...
    
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'text/plain',
        ];
    }
}
</code></pre>

{% hint style="info" %}
Saloon won't add a Content-Type header for you for plain string bodies so you must provide your own Content-Type header.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
<strong>use Saloon\Traits\Body\HasStringBody;
</strong>
class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasStringBody;
</strong>
    protected Method $method = Method::POST;
    
    public function __construct(
        protected string $ubuntuVersion,
        protected string $provider
    ){}
    
    protected function defaultBody(): string
    {
        return 'Howdy, Partner. I want a ' . $this->ubuntuVersion . ' server through ' . $this->provider . ' provider!';
    }
}
</code></pre>

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the string request body:

* set(string $value) -> **Overwrite the string body entirely**
* all(): array -> **Get the string body**&#x20;
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->set('Howdy, Partner');

$body = $request->body()->all();

// string: 'plain-text-response-body'
```

### Connector Body

You can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the body on the request will take priority. If you have the body on just the connector but not on the request, the request will inherit the body on the connector.

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasStringBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasStringBody;

    protected function defaultBody(): string
    {
        return 'Howdy, Partner';
    }
}
```


# Stream Body

Similar to the string / plain text body, the stream body allows you to send a stream or a file resource directly to the server. This is useful if the API accepts a plain-text style input but large files in the form of streams.

To get started, change your method to **POST, PUT or PATCH** depending on the requirements of the API. After that, you will need to add the `HasBody` interface to your request. Without this interface, Saloon will not send any request body to the HTTP client.&#x20;

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;

<strong>class CreateServerRequest extends Request implements HasBody
</strong>{
    protected Method $method = Method::POST;
}
</code></pre>

Next, you will need to add the `HasBody` trait to your request. This trait will implement the `body()` method that the `HasBody` interface requires. It also provides a method `defaultBody()` which you can extend to provide a default body on your request.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasStreamBody;

class CreateServerRequest extends Request implements HasBody
{
<strong>    use HasStreamBody;
</strong>
    protected Method $method = Method::POST;
    
    // Define our own Content-Type...
    
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'text/plain',
        ];
    }
}
</code></pre>

{% hint style="info" %}
Saloon won't add a Content-Type header for you for plain stream bodies so you must define the Content-Type or the API might not accept the data.
{% endhint %}

### Default Body

There are a couple of ways to interact with the request body to prepare it to be sent. You can either use the methods mentioned below to add to the body on any given instance or you can use the `defaultBody` method on your request. This is recommended because you could then define any requirements as constructor arguments in your request and then standardise your request even more.&#x20;

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasStreamBody;

class CreateServerRequest extends Request implements HasBody
{
    use HasStreamBody;

    protected Method $method = Method::POST;
    
    public function __construct(
        protected mixed $stream,
    ){}
    
    protected function defaultBody(): mixed
    {
        return $this->stream;
    }
}
```

{% hint style="info" %}
Saloon can accept a PHP resource or a PSR `StreamInterface` Stream implementation as the stream.
{% endhint %}

### Interacting with the body() method

While you can define the default body on your request, it might be useful to add or modify the body at runtime on a per-request basis. Saloon has the following methods to allow you to modify the stream request body:

* set(resource|StreamInterface $value) -> **Set the stream**
* all(): StreamInterface -> **Get the stream**
* isEmpty(): bool  -> **Check if the body is empty**
* isNotEmpty(): bool -> **Check if the body is not empty**

```php
<?php

$request = new CreateServerRequest;

$request->body()->set(fopen('cowboy-hat.png', 'r'));

$body = $request->body()->all(); // Returns PSR StreamInterface
```

### Connector Body

You can add the same interface and trait to your connector. If you have the trait on both the connector and the request, the body on the request will take priority. If you have the body on just the connector but not on the request, the request will inherit the body on the connector.

```php
<?php

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasStreamBody;

class ForgeConnector extends Connector implements HasBody
{
    use HasStreamBody;

    protected function defaultBody(): mixed
    {
        return fopen('cowboy-hat.png', 'r');
    }
}
```


# Sending Requests

To start sending requests, instantiate your connector and use the `send` method. This method accepts a request class.

```php
<?php

$forge = new ForgeConnector;
$request = new GetServersRequest;

$response = $forge->send($request);
```

### Asynchronous Requests

To send an asynchronous request, use the `sendAsync` method, and you will receive an instance of `PromiseInterface`. You should define a `then` and `otherwise` method to receive the response or exception.&#x20;

```php
<?php

$forge = new ForgeConnector('api-token');
$promise = $forge->sendAsync(new GetServersRequest);

$promise
   ->then(function (Response $response) {
      // Handle Response
   })
   ->otherwise(function (RequestException $exception) {
      // Handle Exception
   });
   
$promise->wait(); // Force the promise to be resolved
```

{% hint style="info" %}
Saloon uses Guzzle's Promises library. [Click here to learn more](https://github.com/guzzle/promises)
{% endhint %}


# Responses

After sending a request, Saloon will return a `Response` class. This response class contains many helpful methods for interacting with your HTTP response like seeing the HTTP status code and retrieving the body.

```php
<?php

$forge = new ForgeConnector;
$request = new GetServersRequest;

$response = $forge->send($request);

$status = $response->status();
$body = $response->body();
```

{% hint style="info" %}
By default, Saloon will not throw an exception if a synchronous request fails. [Refer to the handling failures section for handling errors.](https://docs.saloon.dev/the-basics/handling-failures)
{% endhint %}

### Useful Methods

Below are some of the key methods provided by Saloon's `Response` class.

<table><thead><tr><th width="342">Method</th><th>Description</th></tr></thead><tbody><tr><td><code>status</code></td><td>Returns the response status code.</td></tr><tr><td><code>headers</code></td><td>Returns all response headers</td></tr><tr><td><code>header</code></td><td>Returns a given header</td></tr><tr><td><code>body</code></td><td>Returns the raw response body as a string</td></tr><tr><td><code>json</code></td><td>Retrieves a JSON response body and json_decodes it into an array.</td></tr><tr><td><code>array</code></td><td>Alias of <code>json</code></td></tr><tr><td><code>collect</code></td><td>Retrieves a JSON response body and json_decodes it into a Laravel Collection. Requires <code>illuminate/collections</code>.</td></tr><tr><td><code>object</code></td><td>Retrieves a JSON response body and json_decodes it into an object.</td></tr><tr><td><code>xmlReader</code></td><td>Used for XML responses - returns a <a href="../installable-plugins/xml-wrangler">XML Wrangler</a> reader. Requires <code>saloonphp/xml-wrangler</code>.</td></tr><tr><td><code>dom</code></td><td>Used for HTML responses - returns a <a href="https://symfony.com/doc/current/components/dom_crawler.html">Symfony DOM Crawler</a> instance. Requires <code>symfony/dom-crawler</code>.</td></tr><tr><td><code>stream</code></td><td>Returns the response body as an instance of <code>StreamInterface</code></td></tr><tr><td><code>saveBodyToFile</code></td><td>Allows you to save the raw body to a file or open file resource.</td></tr><tr><td><code>dto</code></td><td>Converts the response into a data-transfer object. You must define your DTO first, <a href="../digging-deeper/data-transfer-objects">click here to read more.</a></td></tr><tr><td><code>dtoOrFail</code></td><td>Will work just like <code>dto</code> but will throw an exception if the response is considered "failed".</td></tr><tr><td><code>ok</code>, <code>successful</code>, <code>redirect</code>, <code>failed</code>, <code>clientError</code>, <code>serverError</code></td><td>Methods used to determine if a request was successful or not based on status code. The <code>failed</code> method <a href="../handling-failures#customising-when-saloon-thinks-a-request-has-failed">can be customised</a>.</td></tr><tr><td><code>throw</code></td><td>Will throw an exception if the response is considered "failed".</td></tr><tr><td><code>getPendingRequest</code></td><td>Returns the <code>PendingRequest</code> class that was built up for the request.</td></tr><tr><td><code>getPsrRequest</code></td><td>Returns the PSR-7 request that was built up by Saloon</td></tr><tr><td><code>getPsrResponse</code></td><td>Return the PSR-7 response that was built up by the HTTP client/sender.</td></tr></tbody></table>

### Asynchronous Responses

When `sendAsync` or concurrent requests, Saloon will respond with a `GuzzleHttp\Promise\PromiseInterface.` The promise will contain a `Response` a class described above. When the request fails, Saloon will not use the `then` method but return an instance of `RequestException`in the `otherwise` block.

```php
<?php

use Saloon\Http\Response;

$forge = new ForgeConnector;
$promise = $forge->sendAsync(new GetServersRequest);

$promise
    ->then(function (Response $response) {
        // Handle successful response
    })
    ->otherwise(function (Exception $exception) {
        // Handle failed request
    });

$promise->wait(); // Force the promise to be resolved
```

### Custom Responses

Sometimes you may want to use your response class. This is useful if you want to add your methods or overwrite Saloon's response methods. Saloon allows you to overwrite the response at a connector level for all requests or at a per-request level for a granular response.

The simplest way of registering a custom response is to use the `$response` property on either the connector or request.

{% tabs %}
{% tab title="Connector" %}

<pre class="language-php"><code class="lang-php">class ForgeConnector extends Connector
{
    // ...
    
<strong>    protected ?string $response = CustomResponse::class;
</strong>}
</code></pre>

{% endtab %}

{% tab title="Request" %}

<pre class="language-php"><code class="lang-php">class GetServersRequest extends Request
{
    // ...
    
<strong>    protected ?string $response = CustomResponse::class;
</strong>}
</code></pre>

{% endtab %}
{% endtabs %}

When you need a more advanced way to define a custom response, use the `resolveResponseClass` method on either the connector or request.

{% tabs %}
{% tab title="Connector" %}

```php
class ForgeConnector extends Connector
{
    // ...
    
    public function resolveResponseClass(): string
    {
        return CustomResponse::class;
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
class GetServersRequest extends Request
{
    // ...
    
    public function resolveResponseClass(): string
    {
        return CustomResponse::class;
    }
}
```

{% endtab %}
{% endtabs %}


# Handling Failures

Saloon has a powerful exception handler with many descriptive exceptions you can use in your application.

When you send a request, Saloon won't do anything if the request fails, but by default, it will use the status code to determine if a request is successful or not. The only exception to this is if Saloon cannot connect to an API, which will throw a `FatalRequestException`.

```php
<?php

$forge = new ForgeConnector;
$response = $forge->send(new ErrorRequest);

$response->failed(); // true
$response->status(); // 500
$response->body(); // {"message": "Server Error"}
```

### Always throw exceptions on failed requests

You may wish to throw an exception whenever a request fails (4xx or 5xx response). You can add the `AlwaysThrowOnErrors` trait on your connector, and then every request that fails will throw an exception.

```php
<?php

use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class ForgeConnector extends Connector
{
    use AlwaysThrowOnErrors;
    
    // {...}
}
```

### Using the throw method

On a per-response basis, you may use the `throw` method after sending your response. This method will throw an exception if the response has a "failed" HTTP status code like 4xx or 5xx.

```php
<?php

$forge = new ForgeConnector;
$response = $forge->send(new ErrorRequest);

// throws InternalServerErrorException (extends ServerException)

$response->throw();
```

### Response Exceptions

Saloon's default exception handler contains the following exceptions based on the status code and severity of the exception. These are thrown depending on the method you use below.

```
SaloonException
├── FatalRequestException (Connection Errors)
└── RequestException (Request Errors)
    ├── ServerException (5xx)
    │   ├── InternalServerErrorException (500)
    │   ├── ServiceUnavailableException (503)
    │   └── GatewayTimeoutException (504)
    └── ClientException (4xx)
        ├── UnauthorizedException (401)
        ├── PaymentRequiredException (402)
        ├── ForbiddenException (403)
        ├── NotFoundException (404)
        ├── MethodNotAllowedException (405)
        ├── RequestTimeOutException (408)
        ├── UnprocessableEntityException (422)
        └── TooManyRequestsException (429)
```

### Handling failures with promises

When sending requests using `sendAsync` or using request pooling, you will receive a `PromiseInterface` instance. Since this class catches exceptions, Saloon will automatically pass the request exception in the `otherwise` block, and you do not have to use the `throw` method.

```php
<?php

use Saloon\Http\Response;

$forge = new ForgeConnector('api-token');
$promise = $forge->sendAsync(new GetServersRequest);

$promise
    ->then(function (Response $response) {
        // Handle successful response
    })
    ->otherwise(function (RequestException $exception) {
        // Handle failed request
    });
```

### Customising when Saloon thinks a request has failed

You may integrate with an API which returns a 200 response status but with an error message in the response body. To handle this, you can extend the `hasRequestFailed` method on your connector or request.

{% tabs %}
{% tab title="Connector" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Connector;
use Saloon\Http\Response;

class ForgeConnector extends Connector
{
    // {...}
    
<strong>    public function hasRequestFailed(Response $response): ?bool
</strong>    {
<strong>        return str_contains($response->body(), 'Server Error');
</strong>    }
}
</code></pre>

{% endtab %}

{% tab title="Request" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Http\Response;

class ErrorRequest extends Request
{
    // {...}
    
<strong>    public function hasRequestFailed(Response $response): ?bool
</strong>    {
<strong>        return str_contains($response->body(), 'Server Error');
</strong>    }
}
</code></pre>

{% endtab %}
{% endtabs %}

### Customising the request exception

By default, Saloon will use the exceptions [listed above](#response-exceptions), but you may choose to return your own exception if a request has failed. Just extend the `getRequestException` method on either your connector or request. You will receive an instance of the response and a sender exception, which may be nullable.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;
use Saloon\Http\Response;
use \Throwable;

class ForgeConnector extends Connector
{
    // {...}
    
    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return new CustomException('Oh yee-naw!', $response, $senderException);
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Http\Response;
use \Throwable;

class ErrorRequest extends Request
{
    // {...}
    
    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return new CustomException('Oh yee-naw!', $response, $senderException);
    }
}
```

{% endtab %}
{% endtabs %}

{% hint style="info" %}
Priority is given to the request when you extend the `getRequestException` method on both your connector and request.
{% endhint %}


# Debugging

While building API integrations, sometimes you will send a request and the API will return an error. This could be due to sending the request badly or incorrect data. Sometimes debugging the request can be difficult - so Saloon has some helpers to resolve this.

### Prerequisites

Saloon's default debugging driver uses [Symfony's Var Dumper](https://github.com/symfony/var-dumper) library. If you're not using Laravel and do not have it installed, you can install it below.

```
composer require symfony/var-dumper
```

{% hint style="info" %}
Laravel already includes this library.
{% endhint %}

### Debugging Request & Response

The simplest way to debug a request and response is to use the `debug()` method on your connector before sending a request. This will output an easy-to-understand array of the request and the response.

{% tabs %}
{% tab title="Connector" %}

```php
$connector = new ForgeConnector;

$connector->debug()->send($request);
```

{% endtab %}

{% tab title="Request" %}

```php
$connector = new ForgeConnector;

$connector->send($request->debug());
```

{% endtab %}
{% endtabs %}

You can provide the `die` argument if you would like to terminate the application after receiving the response.

```php
$connector->debug(die: true)->send($request);
```

This will provide an output in your terminal/browser like this:

```
Saloon Request (UserRequest) -> array:6 [
  "connector" => "Saloon\Tests\Fixtures\Connectors\TestConnector"
  "request" => "Saloon\Tests\Fixtures\Requests\UserRequest"
  "method" => "GET"
  "uri" => "https://tests.saloon.dev/api/user"
  "headers" => array:2 [
    "Host" => "tests.saloon.dev"
    "Accept" => "application/json"
  ]
  "body" => ""
]

Saloon Response (UserRequest) -> array:3 [
  "status" => 200
  "headers" => []
  "body" => "{"name":"Sam"}"
]
```

{% hint style="warning" %}
This output will show the request just before it is sent to the sender. If the sender (like Guzzle) adds any additional headers or changes the request, these changes will not be displayed.
{% endhint %}

### Separate Debuggers

You may also use `debugRequest` and `debugResponse` independently if you would like to debug just the request or response respectively.

```php
<?php

$connector->debugRequest(); // $connector->debugRequest(die: true);

$connector->debugResponse(); // $connector->debugResponse(die: true);
```

### Custom Debugger Handlers

You may also provide a closure to the `debugRequest` and `debugResponse` methods if you would like to write your own debugging implementations.

```php
<?php

use Saloon\Http\Response;
use Saloon\Http\PendingRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// ...

$connector->debugRequest(function (PendingRequest $pendingRequest, RequestInterface $psrRequest) {
    ray($psrRequest);
});

$connector->debugResponse(function (Response $response, ResponseInterface $psrResponse) {
    ray($psrResponse);
});
```


# Testing

### Introduction

Writing tests for your API integrations is an important part of ensuring your application remains stable over time. This section of the documentation will walk you through how to use Saloon's testing helpers to mock API requests and write assertions to prove your application is handling requests and responses properly.

#### What should you test?

The general rule of thumb is that you should assume that the API you are integrating with has tests that ensure their API always returns what they tell you, so you don't have to worry about testing that specific endpoint returns the right data.

You should, however, always test how your application handles the API response that comes back. What happens if the API is down or it returns a 500 error? What happens if your access token expires? You should make sure your application can handle these scenarios.

#### Why use fake responses and fixtures instead of making the API calls every time?

When running your application tests, you are likely going to be running them over and over again which might incur charges or hit API rate limits. Instead, you can either write a fake response or Saloon can record an API call once and re-use that same response in all subsequent tests.

Faking API responses are typically much faster as they do not require an internet connection or potentially long wait times.

### Getting Started

#### The MockClient

Saloon's testing starts with the **MockClient** class. This class can be instantiated locally for testing individual requests or used globally to test an API call nested deep in your application. Inside the MockClient, you can define requests and responses that should be returned instead of a real API call.

```php
use Saloon\Http\Faking\MockClient;

test('my test', function () {
    $mockClient = new MockClient([
        GetServersRequest::class => MockResponse::make(body: '', status: 200),
    ]);
    
    $connector = new ForgeConnector;
    $connector->withMockClient($mockClient);
});
```

You use the key of the MockClient to define a request that should be mocked. You can also use a URL pattern. You can use an asterisk (**\***) to act as a wild card.

```php
$mockClient = new MockClient([
    'forge.laravel.com/api/v1/servers' => MockResponse::make(body: '', status: 200),
    'forge.laravel.com/*' => MockResponse::make(body: '', status: 200),
]);
```

You can also use a sequence of responses which will be returned regardless of the API call.

```php
$mockClient = new MockClient([
    MockResponse::make(body: 'First', status: 200),
    MockResponse::make(body: 'Second', status: 200),
    MockResponse::make(body: 'Third', status: 200),
]);
```

You can also use a closure for more advanced mock responses.

```php
$mockClient = new MockClient([
    GetServersRequest::class => function (PendingRequest $pendingRequest) {
        return MockResponse::make(...);
    },
]);
```

#### Manual Fake Responses

There are two ways to define a response, you can do it manually with `MockResponse::make()` which is suitable for simple responses. It will accept three arguments: body, HTTP status code and headers. You may pass a string or an array into the body argument. If you provide an array, it will automatically be converted into JSON.

```php
$mockClient = new MockClient([
    GetUserRequest::class => MockResponse::make(
        body: ['user' => ['name' => 'Sam']], 
        status: 200, 
        headers: ['Content-Type' => 'application/json']
    ),
]);
```

#### Recorded Responses (Fixtures)

The second way to define a response is with `MockResponse::fixture()` this is suitable for testing larger API responses or to save time. With this, Saloon will make a real API call for the first time, and then store the API call inside of a file in your application, so that when you run the test again, the response will be used and not make a real API call.

```php
$mockClient = new MockClient([
    GetUserRequest::class => MockResponse::fixture('user');
]);
```

### Testing your application

Now that you have an idea of how testing works in Saloon, let's put it into practice. This section of the documentation will teach you how to write a MockClient for your application. This guide assumes you have already set up a test suite for your application. If you are new to testing, we recommend [PEST](https://pestphp.com/).

{% hint style="info" %}
This section of the documentation uses the new **Global Mock Client** introduced in Saloon v3.5. Make sure that you are using this version of Saloon by running: `composer update "saloonphp/*"`
{% endhint %}

#### Setup

First of all, you will need to add a method which should run before every test in your application. Without this, the global mock client will leak into other tests and may break your test suite.

{% tabs %}
{% tab title="PEST" %}
If you are using PEST, then you should add the following code to your `Pest.php` file.

```php
use Saloon\Http\Faking\MockClient;

uses()
    ->beforeEach(fn () => MockClient::destroyGlobal())
    ->in(__DIR__);
```

{% endtab %}

{% tab title="PHPUnit" %}
If you are using PHPUnit, then you should add the following line inside of your `setUp` method in your test or application's `TestCase.php` file.

```php
use Saloon\Http\Faking\MockClient;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        MockClient::destroyGlobal();
    }
}
```

{% endtab %}
{% endtabs %}

{% hint style="info" %}
If you are using Laravel and have installed the [Laravel plugin](https://docs.saloon.dev/installable-plugins/laravel-integration), you do not need to destroy the global mock client before each test as the plugin will do this for you.
{% endhint %}

#### Writing tests

Let's say that your application has a controller which queries the Laravel Forge API and stores the list of servers in the database. We'll use the global mock client to return fake data so we don't send an API call to the external API.

Saloon will detect the global mock client and will use the mock response which you have defined instead of sending a real API call. It's recommended to define your mock at the top of a test.

{% tabs %}
{% tab title="Test" %}

```php
<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can store servers in the database from laravel forge', function () {
    MockClient::global([
        GetServersRequest::class => MockResponse::make(
            body: [
                'data' => [
                    ['name' => 'WEB-1', 'ip' => '192.168.0.1'],
                    ['name' => 'WEB-2', 'ip' => '192.168.0.2'],
                ],
            ],
            status: 200,
        ),
    ]);

    $this->assertDatabaseCount('servers', 0);
    
    // Call our controller which will invoke the Saloon request

    $this
        ->getJson('/api/servers/sync')
        ->assertOk();
        
    // Assert our database has created servers!
        
    $this->assertDatabaseCount('servers', 2);
    
    $this->assertDatabaseHas('servers', [
        'name' => 'WEB-1',
        'ip' => '192.168.0.1',
    ]);
    
    $this->assertDatabaseHas('servers', [
        'name' => 'WEB-2',
        'ip' => '192.168.0.2',
    ]);
});
```

{% endtab %}

{% tab title="Controller Code" %}

```php
<?php

class ForgeController
{
    // api/servers/sync
    
    public function sync()
    {
        $forge = new ForgeConnector;
        $servers = $forge->send(new GetServersRequest)->array('data');

        foreach($servers as $server) {
            DB::table('servers')->insert([
                'name' => $server['name'],
                'ip' => $server['ip'],
            ]);
        }

        return response('Synced servers', 200);
    }
}
```

{% endtab %}
{% endtabs %}

### Recording Requests (Fixtures)

So far we have covered testing where you have to write your mock response yourself. This works well for small responses but most API responses will have a lot more data. As previously mentioned, Saloon supports recording requests in your tests. This works by allowing the test to make a real API call the first time and then on subsequent API calls, the same response will be used.

This works by saving the response as a file in your application which can be committed in your project and used for later.

{% hint style="warning" %}
Make sure that you do not accidentally store sensitive information from the real API call. You can read more about redacting fixtures [here](#redacting-recorded-responses-fixtures).
{% endhint %}

#### Example with fixtures

Let's take a look at the example above but with fixtures, as you can see nothing else has to change except that we are using `MockResponse::fixture()`. It accepts one argument which is the name of the fixture.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can store servers in the database from laravel forge', function () {
    MockClient::global([
<strong>        GetServersRequest::class => MockResponse::fixture('servers'),
</strong>    ]);

    $this->assertDatabaseCount('servers', 0);
    
    // Call our controller which will invoke the Saloon request

    $this
        ->getJson('/api/servers/sync')
        ->assertOk();
        
    // Assert our database has created servers!
        
    $this->assertDatabaseCount('servers', 2);
    
    $this->assertDatabaseHas('servers', [
        'name' => 'REAL-SERVER-1',
        'ip' => '192.168.0.1',
    ]);
    
    $this->assertDatabaseHas('servers', [
        'name' => 'REAL-SERVER-2',
        'ip' => '192.168.0.2',
    ]);
});
</code></pre>

#### Customising Fixture Location

By default, fixtures will be stored in `tests/Fixtures/Saloon`. You can customise the fixture location by using the `setFixturePath` method on the `MockConfig` class.

```php
MockConfig::setFixturePath('tests/other-directory');
```

#### Namespacing Fixtures

With multiple API integrations, having all the fixtures in one directory can get messy. You can use `/` to create folders for your fixtures.

```php
MockClient::global([
   GetServersRequest::class => MockResponse::fixture('forge/servers'),
]);
```

{% hint style="warning" %}
You should be cautious when naming fixture folders - some operating systems like MacOS do not have case sensitivity for folders but some Unix-based systems like Linux do. This can result in fixtures not being found. It's recommended to use lowercase names.
{% endhint %}

#### Refreshing Fixtures

If you need to refresh a fixture, all you have to do is delete the JSON file and re-run your test. Saloon will see the file is no longer there and will re-record the request.

### Testing individual requests and SDKs

Occasionally, you might want to write unit tests for individual requests to ensure you are sending the right information to an API. If you are building an SDK, then you may wish to follow this guide for testing your SDK.

#### Using the withMockClient method on your connector

Instead of using the global mock client, you can create a local instance of a MockClient and pass it into your connector or request using the `withMockClient` method.

<pre class="language-php"><code class="lang-php">&#x3C;?php

test('a request sends the correct body', function () {
    $mockClient = new MockClient([
        CreateServerRequest::class => MockResponse::make('Server Created', 200),
    ]);

    $connector = new ForgeConnector;
<strong>    $connector->withMockClient($mockClient);
</strong>    
    $connector->send(new CreateServerRequest($name, $ip));
    
    $mockClient->assertSent(function (Request $request) {
        return $request->body()->all() === ['name' => $name, 'ip' => $ip];
    });
});
</code></pre>

### Assertions

Saloon has a variety of built-in assertion methods which you can also use in your test to assert that Saloon is sending the right information to the third-party API. You can define a variable for your MockClient and call the various assertion methods inside of your test.

```php
<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can store servers in the database from laravel forge', function () {
    $mockClient = MockClient::global([
        GetServersRequest::class => MockResponse::make(...),
    ]);

    // Your application code...
    
    $mockClient->assertSent(GetServersRequest::class);
    
    $mockClient->assertSentCount(1);
});
```

Saloon has the following assertions:

#### assertSent / assertNotSent

These methods will check that a specific request has been sent.

```php
$mockClient->assertSent(GetServersRequest::class);
```

The method can also accept a closure which should return true for more advanced assertions.

```php
$mockClient->assertSent(function (Request $request, Response $response) {
    return $request instanceof GetServersRequest;
});
```

#### assertSentCount

This method allows you to assert that a specific number of requests has been sent.

```php
$mockClient->assertSentCount(1);
```

The method also accepts a second argument which allows you to check the amount a specific request class was sent.

```php
$mockClient->assertSentCount(2, GetServersRequest::class);
$mockClient->assertSentCount(1, CreateServerRequest::class);
```

#### assertNothingSent

This method will assert that no requests have been sent.

```php
$mockClient->assertNothingSent();
```

### Preventing Stray Requests

You may want to prevent real API calls from being attempted in your tests. You can use `Config::preventStrayRequests()` which will throw an exception if a real API call is attempted.

{% tabs %}
{% tab title="PEST" %}
If you are using PEST, then you should add the following code to your `Pest.php` file.

```php
use Saloon\Config;

Config::preventStrayRequests();
```

{% endtab %}

{% tab title="PHPUnit" %}
If you are using PHPUnit, then you should add the following line inside of your `setUp` method in your test or application's `TestCase.php` file.

```php
use Saloon\Config;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::preventStrayRequests();
    }
}
```

{% endtab %}
{% endtabs %}

{% hint style="warning" %}
Fixtures will still be recorded and not counted as stray requests.
{% endhint %}

### Preventing Fixtures Being Recorded

While running tests, you may wish to prevent Saloon from attempting to record any further fixtures. This is useful for CI like GitHub actions where you shouldn't expect any fixtures to be recorded. You can use `MockConfig::throwOnMissingFixtures()` to prevent this from happening.

{% tabs %}
{% tab title="PEST" %}
If you are using PEST, then you should add the following code to your `Pest.php` file.

```php
use Saloon\MockConfig;

MockConfig::throwOnMissingFixtures();
```

{% endtab %}

{% tab title="PHPUnit" %}
If you are using PHPUnit, then you should add the following line inside of your `setUp` method in your test or application's `TestCase.php` file.

```php
use Saloon\MockConfig;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        MockConfig::throwOnMissingFixtures();
    }
}
```

{% endtab %}
{% endtabs %}

### Redacting Recorded Responses / Fixtures

When using fixtures to record real responses from an API - sometimes the API will return some sensitive information that you shouldn't store in your application's repository, like names of real people, financial data or emails. With Saloon, you can create a custom fixture class and provide a few methods to obscure the information when the data is stored. You can even provide closures for the data replacement so you can use tools like Faker to replace data like-for-like.

{% hint style="info" %}
The first time the request is made and the fixture is stored, the original response won't be redacted. Only future requests made with the fixture will use the redacted recording.
{% endhint %}

First, create a new class in your tests directory and extend the base `Fixture` class provided by Saloon. Next, extend the `defineName` method and give the fixture a name. You can still use slashes in this directory to denote folders.

Now you can use a few different methods to redact your fixture data.

* defineSensitiveHeaders for headers
* defineSensitiveJsonParameters for JSON responses
* defineSensitiveRegexPatterns for other body types

The methods expect a key => value array where the key is the property that is being redacted and the value is the replacement. You can use a string as the replacement or use a closure for more advanced replacement engines like using faker.

<pre class="language-php"><code class="lang-php">&#x3C;?php

namespace Tests\Fixtures\Forge\SingleServerFixture;

use Saloon\Http\Faking\Fixture;

class SingleServerFixture extends Fixture
{
    protected function defineName(): string
    {
        return 'forge/singleServer';
    }
    
<strong>    protected function defineSensitiveHeaders(): array
</strong>    {
        return [
            'Authorization' => 'REDACTED',
        ];
    }

<strong>    protected function defineSensitiveJsonParameters(): array
</strong>    {
        return [
            'name' => 'REDACTED',
            'password' => function () {
                return faker()->password;
            },
        ];
    }
    
<strong>    protected function defineSensitiveRegexPatterns(): array
</strong>    {
        return [
            '/@[a-z0-9_]{0,100}/' => 'REDACTED-TWITTER-HANDLE',
        ];
    } 
}
</code></pre>

Once you have created your custom fixture class with the redaction configuration, you can simply use it instead of `MockResponse::fixture()` in your tests. All detection methods in the mock client work with this type of fixture too.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$mockClient = new MockClient([
<strong>    GetServerRequest::class => new SingleServerFixture;
</strong>]);
</code></pre>

### Using Saloon's Laravel Facade

If you have installed the [official Laravel plugin](https://docs.saloon.dev/installable-plugins/laravel-integration), then you can use the `Saloon::fake()` method as an alias of `MockClient::global()`.

```php
use Saloon\Laravel\Facades\Saloon;

Saloon::fake([
    GetServersRequest::class => MockResponse::make(body: '', status: 200),
]);
```


# Data Transfer Objects

When building API integrations, sometimes dealing with a raw response or a JSON response can be tedious and unpredictable. Data transfer objects are a good solution as they allow you to define a structure for a request and response. Saloon supports casting a response from an API request into a DTO.

### Defining DTOs from responses

In your request, extend the `createDtoFromResponse` method. Within this method, you get access to the `Response` object containing the response to cast into a data transfer object.

```php
<?php

use Saloon\Http\Request;
use Saloon\Http\Response;

class GetServerRequest extends Request
{
    // {...}
    
    public function createDtoFromResponse(Response $response): mixed
    {
        $data = $response->json();
    
        return new Server(
            id: $data['id'],
            name: $data['name'],
            ipAddress: $data['ip'],
        );
    }
}
```

This is what the `Server` DTO looks like:

```php
<?php

class Server
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $ipAddress,
    ){}
}
```

### Retrieving your DTO from responses

Now we have defined our DTO on our request or connector, we can use the build in `dto` or `dtoOrFail` methods on our response class.

```php
<?php

$connector = new ForgeConnector;

$response = $connector->send(new GetServerRequest(id: 12345));

// Create a DTO even if the response was a failure
$server = $response->dto();

// Create a DTO or throw an exception if the response is not successful
$server = $response->dtoOrFail();
```

{% hint style="info" %}
Saloon will attempt to create a DTO from your response no matter the status of the response. This allows you to create "error" data transfer objects. If you don't want to use this functionality, you can use the `dtoOrFail` method which will throw a LogicException if the response is a failure. **You can customise what is considered a failed response** [**here**](https://docs.saloon.dev/the-basics/handling-failures#customising-when-saloon-thinks-a-request-has-failed)**.**
{% endhint %}

### PHPStan / TypeHinting DTOs

Due to the way DTOs are created in Saloon, you can lose the type of DTO. This can reduce the developer experience. There are two ways to get around this issue.

You can use an inline-doc block above the `dto`/`dtoOrFail` method call.

```php
/** @var Server $server */
$server = $response->dto();
```

Or you can use the `createDtoFromResponse` method directly on your connector or request - which will use the return type specified.

```php
$server = $request->createDtoFromResponse($response);
```

### Using a DTO to populate request data

You may also allow your data transfer objects to go both ways, back into requests. You can do this easily with Saloon, just accept your DTO as an argument in the constructor of your request and then use the DTO to set default properties inside of the request.

{% tabs %}
{% tab title="Definition" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class UpdateServerRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;
    
    public function __construct(readonly protected Server $server)
    {
        //
    }
    
    public function resolveEndpoint(): string
    {
<strong>        return '/servers/' . $this->server->id;
</strong>    }
    
    protected function defaultBody(): array
    {
        return [
<strong>            'name' => $this->server->name,
</strong>        ];
    }
    
    public function createDtoFromResponse(Response $response): mixed
    {
        $data = $response->json();
    
        return new Server(
            id: $data['id'],
            name: $data['name'],
            ipAddress: $data['ip'],
        );
    }
}
</code></pre>

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$connector = new ForgeConnector;
$response = $connector->send(new GetServerRequest(id: 12345));
$server = $response->dto();

// Modify the server
$server->name = 'YEE-HAW-2';

// Update the server request

$response = $connector->send(new UpdateServerRequest($server));
```

{% endtab %}
{% endtabs %}

### Accessing the response from your DTO

Sometimes debugging a DTO can be difficult, especially if you have passed the data object through your application and no longer have access to the original `Response` that you created the DTO from. Saloon can inject the response into your data transfer object for you if you use the `HasResponse` trait and the `WithResponse` interface. Let's add it to our existing `Server` DTO.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Response;
use Saloon\Traits\Responses\HasResponse;
use Saloon\Contracts\DataObjects\WithResponse;

<strong>class Server implements WithResponse
</strong>{
<strong>    use HasResponse;
</strong>
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $ipAddress,
    ){}
}
</code></pre>

Now whenever we retrieve an instance of our data transfer object, you will be able to access the underlying response that was created with it!

```php
<?php

$server = $response->dto();

$response = $server->getResponse();
```


# Building SDKs

Saloon provides everything you need to build a great SDK or library for an API. It already offers the ability to mock responses, provide authentication, implement OAuth2 boilerplate and even record your API requests in your tests. With Saloon you won't need to write the same boilerplate code over and over again. Saloon comes with just three dependencies making your library or SDK lightweight.

{% hint style="info" %}
You can see some Saloon SDKs built by the PHP community by [visiting the showcase page!](https://docs.saloon.dev/conclusion/showcase)
{% endhint %}

### Getting Started

This guide will be using PokéAPI as an example API. [Click here to see a full SDK example repository](https://github.com/Sammyjo20/pokeapi-sdk/tree/v3). This guide assumes you know the basics of connectors, requests and sending requests.

To start building an SDK with Saloon, we recommend that you create a connector as your SDK class. This class allows you to configure the base URL and any defaults you may need like default headers or authentication.

#### Example SDK Connector

This is an example SDK for the [PokéAPI](https://pokeapi.co/). As you can see, I have defined the API base URL, as well as used the constructor to provide an authentication token. I have also defined some default headers.

```php
<?php

namespace Pokeapi;

use Saloon\Http\Request;
use Saloon\Http\Connector;
use Saloon\Http\Auth\TokenAuthenticator;

class Pokeapi extends Connector
{
    public function __construct(protected readonly string $token) {}

    public function resolveBaseUrl(): string
    {
        return 'https://pokeapi.co/api/v2';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
    
    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }
}
```

Now that we have created the SDK class, all we need to do is instantiate it and provide the API token. We are now ready to send requests through our SDK. You may add your methods to this class as it is the root class of your SDK.

```php
<?php

use Pokeapi\Pokeapi;

$pokeapi = new Pokeapi('my-api-token');

// Ready to make requests!
```

### Sending Requests

One of the ways that you can build SDKs in Saloon is by creating request classes and then calling them from the SDK connector. This is the simplest way and you will get up and running really quickly with Saloon. Make sure you are familiar with how requests work first.

#### Getting started

Firstly, you will need to [create a request](https://docs.saloon.dev/the-basics/requests), this is the same as making a normal request described in the documentation.

#### Use your request

When you have created the request, all that developers would need to do is instantiate and send the request on the connector. This means you only need to have two classes as a minimum for a fully working SDK!

```php
<?php

use Pokeapi\Pokeapi;

$pokeapi = new Pokeapi('my-api-token');
$request = new GetAllPokemon(page: 1);

// Developers would just send the request.

$response = $pokeapi->send($request);
```

With this method, it’s really simple to build your SDK. All you would need to do is create all the requests and then document them in your README. Developers using your SDK can just instantiate your SDK and then use the `send` methods. This also gives the developer freedom to customise the request's headers, config and query parameters if they choose to.

### Sending Requests With Methods

Sometimes you may want to make it easy for the developer to find all the methods that they need to call the API through your SDK. You can create methods on your connector which send an API request or you could write a "resource" class that contains lots of requests

{% tabs %}
{% tab title="Definition" %}

```php
class Pokeapi extends Connector
{
    // ...
    
    public function allPokemon(int $page): Response
    {
        return $this->send(new GetAllPokemon($page));
    }
}
```

{% endtab %}

{% tab title="Usage" %}

```php
<?php

$pokeapi = new Pokeapi('my-api-token');

$response = $pokeapi->allPokemon(page: 1);
```

{% endtab %}
{% endtabs %}

### Request Resources/Groups

The resource pattern can help you combine your SDK requests into simple groups that are easy for the developer to find and consume. First, we'll create a class that extends Saloon's `BaseResource` class.

For this API, I will create a Pokémon resource which will group all the Pokémon requests together. Imagine a resource like a controller in an MVC framework like Laravel. You should pass any arguments the request needs through the method arguments.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\BaseResource;
use Saloon\Http\Response;

<strong>class PokemonResource extends BaseResource
</strong>{
     public function all(int $page): Response
     {
         return $this->connector->send(new GetAllPokemon($page));
     }
     
     public function get(int $id): Response
     {
         return $this->connector->send(new GetSinglePokemon($id));
     }
}
</code></pre>

#### Defining a resource on your connector

Now we'll define a method on the connector which returns this resource class. Don't forget to pass the connector's instance (`$this`) into the resource.

```php
class Pokeapi extends Connector
{
    // ...
    
    public function pokemon(): PokemonResource
    {
        return new PokemonResource($this);
    }
}
```

Now all our users have to do is access the `pokemon()` method on the SDK class to get access to all the various requests that our SDK has to offer.

```php
<?php

$pokeapi = new Pokeapi('my-api-token');

$allPokemon = $pokeapi->pokemon()->all(page: 1);

$giratina = $pokeapi->pokemon()->get(id: 'giratina');
```

### Custom Responses

You may wish to customise the `Response` class that is returned by Saloon from your SDK connector. [Click here to read more about custom responses.](https://docs.saloon.dev/the-basics/responses#custom-responses)

### Additional Features

Please read through the other sections of Saloon's documentation to see the other features that you can offer for your SDK. Every other feature like testing, authentication, failure handling is all supported for SDKs.

### Testing

When building an SDK, it's important to write tests that ensure the SDK calls the correct requests from an API and returns the right response, especially if you're converting the response into a data-transfer-object. [Read through the testing section of the documentation](#testing) to get familiar with mocking and recording requests.


# Solo Requests

While Saloon's typical setup of a connector and requests is great, sometimes all you need is to make a single request to a service. For scenarios like these, you may create a "SoloRequest" instead of making a connector and a single request. This saves you from having to create additional classes.

### Setup

Create a class, but instead of extending `Saloon\Http\Request`, you should extend `Saloon\Http\SoloRequest.` Next, just define everything else like you would a normal request. Make sure to include the full URL of the service you are integrating with.

```php
<?php

use Saloon\Http\SoloRequest;
use Saloon\Enums\Method;

class GetAllPokemonRequest extends SoloRequest
{
    protected Method $method = Method::GET;
    
    public function resolveEndpoint(): string
    {
        return 'https://pokeapi.co/api/v2/pokemon';
    }
}
```

### Defaults

Saloon Requests allow you to define all your default headers, config, query parameters and define request body just like you would traditionally with a connector.

### Sending Solo Requests

As you don't have a connector for this request, you can use the `send` or `sendAsync` methods directly on the request instance. This method works exactly the same as it would on the connector.

```php
<?php

$request = new GetAllPokemonRequest;
$response = $request->send();
```


# Retrying Requests

Sometimes you may use APIs that are unreliable or require you to retry multiple times before a request is successful. Saloon has a global retry system that can be configured to automatically retry failed requests.

### Configuring our connector

Let's say that we have an API which isn't reliable. We can define a public `$tries` property on our connector which defines how many times a request will be sent if it fails.

<pre class="language-php"><code class="lang-php">class ForgeConnector extends Connector
{
<strong>    public ?int $tries = 3;
</strong>}
</code></pre>

Now when we send any request through this connector, if it fails it will be retried.

```php
$forgeConnector = new ForgeConnector;

$response = $forgeConnector->send(new UnreliableRequest);
```

If we get to attempt three and it still fails, Saloon will throw a `FatalRequestException` (for connection errors) or a `RequestException` for typical HTTP errors.

{% hint style="warning" %}
The retry functionality will only work when sending requests synchronously. The retry logic will not work when using `sendAsync` or pools because Saloon needs to wait for a response to come back to determine if it needs to be retried.
{% endhint %}

#### What does Saloon consider a failed request?

By default, if the API couldn't be connected to or if the response status code is either a 4xx or 5xx, then Saloon will consider it a failed request. You can change this by extending the `hasRequestFailed` method on your connector. [Click here to learn more.](https://docs.saloon.dev/the-basics/handling-failures#customising-when-saloon-thinks-a-request-has-failed)

### Intervals

You can also configure an interval in **milliseconds** that Saloon should wait between retries. You can configure an interval with the `$retryInterval` property.

<pre class="language-php"><code class="lang-php">class ForgeConnector extends Connector
{
    public ?int $tries = 3;
    
<strong>    public ?int $retryInterval = 1000;
</strong>}
</code></pre>

#### Exponential Backoff

You can also use exponential backoff when retrying which will double the retry interval after each unsuccessful attempt. This is useful as it can be less strenuous on the API if it is experiencing issues.

<pre class="language-php"><code class="lang-php">class ForgeConnector extends Connector
{
    public ?int $tries = 3;
    
    public ?int $retryInterval = 500;
    
<strong>    public ?bool $useExponentialBackoff = true;
</strong>}
</code></pre>

### Exceptions

By default, when all attempts have been exceeded, Saloon will either throw a `FatalRequestException` or a `RequestException`. You can choose to disable this behaviour and return the last response that Saloon received instead.

If the exception was `FatalRequestException` then the exception will still be thrown as we wouldn't have a response to return.

<pre class="language-php"><code class="lang-php">class ForgeConnector extends Connector
{
    public ?int $tries = 3;
    
<strong>    public ?bool $throwOnMaxTries = false;
</strong>}
</code></pre>

### Customising the request before the next retry

In some situations, you might want to change the next request to improve the chances of the next retry being successful. You can overwrite the `handleRetry` method on the connector or request and use it to customise the `Request` class. You must also return a boolean in this method to tell Saloon if the retry should commence.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Exceptions\RequestException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Exceptions\FatalRequestException;
use Saloon\Http\Request;
    
class ForgeConnector extends Connector
{
    public ?int $tries = 3;
    
    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if ($exception instanceof RequestException &#x26;&#x26; $exception->getResponse()->status() === 401) {
<strong>            $request->authenticate(new TokenAuthenticator($this->getNewToken()));
</strong>        }
        
        return true;
    }
}
</code></pre>

### Using the sendAndRetry method

{% hint style="warning" %}
This method has been deprecated since Saloon v3.6.4
{% endhint %}

Saloon also has a `sendAndRetry` method which can be used on the fly on any connector or request without needing to configure anything. This method has the same functionality in the format of a method.

```php
<?php

$forgeConnector = new ForgeConnector;

$response = $forgeConnector->sendAndRetry(
    request: new UnreliableRequest,
    tries: 3,
    interval: 500,
    handleRetry: function (FatalRequestException|RequestException $exception, Request $request) {
        //
    },
    throw: true,
    useExponentialBackoff: true,
);
```


# Delaying Requests

Sometimes in your application, you may want to introduce a delay in your requests. This may be to avoid hitting rate limits or to avoid overloading a development environment. With Saloon, you may use the `defaultDelay` and `delay` methods on either your connector or request to define a delay in milliseconds.

### Default Delay

You may also wish to define a default delay on your connector or request. You can do this by extending the `defaultDelay` method.&#x20;

{% tabs %}
{% tab title="Connector" %}

```php
<?php

class ForgeConnector extends Connector
{
    // Every request sent through this connector will have a 500ms delay.

    protected function defaultDelay(): ?int
    {
        return 500;
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

class GetServersRequest extends Request
{
    // Every time this request is sent, a 500ms delay is added

    protected function defaultDelay(): ?int
    {
        return 500;
    }
}
```

{% endtab %}
{% endtabs %}

### Using the delay methods

You can also apply a delay to a connector or a request on the fly with the `delay()` method.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

$forge = new ForgeConnector;

// Delay every request sent with the ForgeConnector by 500ms 

$forge->delay()->set(500);
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

$request = new GetServersRequest;

// Delay just this request instance by 500ms

$request->delay()->set(500);
```

{% endtab %}
{% endtabs %}

{% hint style="warning" %}
If you have defined the delay on both the connector and the request, the request delay will take priority.&#x20;
{% endhint %}


# Concurrency & Pools

Saloon supports request concurrency and requests pools out of the box. This allows you to make multiple API calls to the same service while keeping the CURL connection open. Under the hood, it uses PHP's cURL multi-handler to keep the connection open, this results in huge speed benefits when making lots of API calls. Saloon's concurrency is powered by [Guzzle's implementation](https://docs.guzzlephp.org/en/stable/quickstart.html?highlight=pool#concurrent-requests) behind the scenes.

One of Laravel's core members, Nuno Maduro [wrote a great blog post](https://nunomaduro.com/speed_up_your_php_http_guzzle_requests_with_concurrency) about request concurrency and its performance with Guzzle directly. The same performance can be shared with Saloon's implementation as Saloon uses Guzzle behind the scenes.

{% hint style="info" %}
Concurrency is only supported with the `GuzzleSender` and `HttpSender` senders for Saloon. The default sender out of the box with Saloon is the GuzzleSender.
{% endhint %}

We conducted our own benchmark by making 1,000 API calls to our internal testing API, running on a virtual server with 2 vCPUs and 2GB of RAM. The results were impressive, as all 1,000 API calls were completed in just **2.5 seconds**, a significant improvement compared to 60 seconds when using synchronous requests.

```php
<?php

$connector = new TestConnector;

// ⬇️ Takes 60 seconds... 😪

$requests = function () {
    for ($i = 0; $i < 1000; $i++) {
        $connector->send(new GetUserRequest);
    }
};

// ⬇️ Takes 2.5 seconds! 🔥

$requests = function () {
    for ($i = 0; $i < 1000; $i++) {
        yield new GetUserRequest;
    }
};

$connector->pool($requests, concurrency: 10)->send()->wait();
```

### Getting Started

Saloon's pooling has been designed specifically to be easy to use. Simply instantiate your connector class and use the `pool` method to create a pool. This method doesn't require any arguments, but you may provide requests, concurrency and handlers right from this method.

```php
<?php

$forge = new ForgeConnector;

// Pool has various optional parameters if you want to set them here...

$pool = $forge->pool(
    requests: [],
    concurrency: 5,
    responseHandler: function () { ... },
    exceptionHandler: function () { ... },
);

// Initiate the transfer of requests

$promise = $pool->send();

// Force all the requests to be fulfilled

$promise->wait();
```

### Available Methods

* `setRequests(callable|iterable $requests)`
* `getRequests()`
* `withResponseHandler(callable $callable)`
* `withExceptionHandler(callable $callable)`
* `send()`

### Providing Requests

The pool class accepts many types of requests:

* Array of requests
* PHP generator providing requests
* Closure or Invokable class returning an array of requests
* Closure or Invokable class returning a generator

You may provide these requests into the pool either as the first argument of the `pool` method or using the `setRequests` method. The requests can be instances of `Saloon\Http\Request` or `Saloon\Http\PendingRequest`.

#### Array of requests

The simplest way to provide requests to the pool is an array.

```php
<?php

$forge = new ForgeConnector;

$forge->pool([
    new GetServersRequest,
    new GetSitesRequest,
    new GetUserRequest,
]);

// Or 

$pool = $forge->pool();

$pool->setRequests([
    new GetServersRequest,
    new GetSitesRequest,
    new GetUserRequest,
]);
```

#### Using a PHP generator

If you are going to send lots of requests you may wish to provide a generator into the pool. This allows you to keep memory consumption low and potentially send hundreds of requests.

```php
<?php

$generatorCallback = function (): Generator {
    for ($i = 0; $i < 3; $i++) {
        yield $i => new UserRequest;
    }
};

$forge = new ForgeConnector;
$pool = $forge->pool($generatorCallback);

// or

$pool->setRequests($generatorCallback);
```

#### Using a callable or invokable class

You may wish to use a callable method to return an array of requests or a generator, this is useful if you have any additional logic that needs to execute just before the request pool begins.

{% tabs %}
{% tab title="Callable" %}

```php
<?php

$callback = function () {
    return [
        new GetServersRequest,
        new GetSitesRequest,
        new GetUserRequest,
    ];
};

$forge = new Forge;
$pool = $forge->pool($callback);

// or

$pool->setRequests($callback);
```

{% endtab %}

{% tab title="Invokable Class" %}

```php
<?php

class PoolClass {
    public function __invoke() {
        return [
            new GetServersRequest,
            new GetSitesRequest,
            new GetUserRequest,
        ];
    }
}

$forge = new ForgeConnector;
$pool = $forge->pool(new PoolClass);
```

{% endtab %}
{% endtabs %}

### Concurrency

By default, Saloon will send up to 5 requests concurrently. You can customise the concurrency that is used by pools with the `setConcurrency` method. It accepts an integer or a callable like a method or an invokable class in case you want to write advanced logic to determine the concurrency.

```php
<?php

$forge = new ForgeConnector;
$pool = $forge->pool(...);

$pool->setConcurrency(10);

// or

$pool->setConcurrency(function () {
    return 10;
});
```

### Response Handlers

When you send requests with pooling, each request is asynchronous, so you cannot guarantee when they are returned. In order to handle the response, Saloon has provided you with two handlers to handle successful requests and failed requests.

#### Handling Successful Requests

Any responses that are successful will be handled by the response handler. You may add this with the constructor of your pool or add it once it is created. You can only have one handler. You will get a response instance.

```php
<?php

use Saloon\Http\Response;

$pool = $forge->pool(
    requests: [],
    concurrency: 5,
    responseHandler: function (Response $response) {
        // Handle Response
    },
);

// Or

$pool->withResponseHandler(function (Response $response) {
    // Handle Response
});
```

#### Handling Failed Requests

When requests fail, they will always be caught with the error handler, even if you don't throw on requests. When a failed request happens you can handle the exception with the `withExceptionHandler` method.

```php
<?php

use Saloon\Http\Response;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Exceptions\Request\FatalRequestException;

$pool = $forge->pool(
    requests: [],
    concurrency: 5,
    exceptionHandler: function (FatalRequestException|RequestException $exception) {
        // Handle Exception
    },
);

// Or

$pool->withExceptionHandler(function (FatalRequestException|RequestException $exception) {
    // Handle Exception
});
```

### Named/Keyed Requests

Saloon also supports keyed responses to help you easily track exact requests that have been sent. This is especially useful if you are sending requests to different endpoints. You may use keying with arrays or even with a generator.

```php
<?php

$pool = $forge->pool([
    'servers' => new GetServersRequest,
    'sites' => new GetSitesRequest,
    'user' => new GetUserRequest,
]);

// You may access the key in the response and error handlers

$pool->withResponseHandler(function (Response $response, string $key) {
    match($key) {
        'servers' => $this->updateServersList($response),
        'sites' => $this->updateSitesList($response),
        'user' => $this->updateUser($response),
    }
});
```

### Sending

Once you have provided the pool with requests, you are ready to send them. Just use the `send` method on the pool. This will return an instance of `PromiseInterface` . Requests will be handled asynchronously but you can force them to complete with the `wait` method.

```php
<?php

$forge = new ForgeConnector;

$pool = $forge->pool([
    new GetServersRequest,
    new GetSitesRequest,
    new GetUserRequest,
]);

$pool->withResponseHandler(function (Response $response) {
    // Handle Response
});

$pool->withExceptionHandler(function (FatalRequestException|RequestException $exception) {
    // Handle Exception
});

// Initiate the transfer of requests

$promise = $pool->send();

// Force all the requests to be fulfilled

$promise->wait();
```


# OAuth2

Saloon supports some OAuth2 authentication grants out of the box which you can use in your application. They come in the form of traits that can be easily added to your connector and then configured to your needs. Read more below by finding the grant type you would like to use

### Authorization Code Grant

This flow is the typical process where your application redirects your users to a third party to approve your authentication request. After they have approved the authentication request, they will be redirected back to your application where you can generate access tokens.

{% content-ref url="oauth2-authentication/oauth2-authentication" %}
[oauth2-authentication](https://docs.saloon.dev/digging-deeper/oauth2-authentication/oauth2-authentication)
{% endcontent-ref %}

### Client Credentials Grant

This flow is often used for internal server-to-server authentication. Like a shopping website communicating with an internal warehouse API. You are often given a single access token which can be used to generate access tokens.

{% content-ref url="oauth2-authentication/client-credentials-grant" %}
[client-credentials-grant](https://docs.saloon.dev/digging-deeper/oauth2-authentication/client-credentials-grant)
{% endcontent-ref %}


# Authorization Code Grant

Some API providers implement the OAuth 2 *Authorization Code Flow* for authentication. Implementing this grant type every time you create a new API integration can be tedious and time-consuming. Saloon offers a simple, extendable OAuth2 trait to help you get up and running quickly.

### Prerequisites

This section of the documentation assumes that you are familiar with OAuth2 and specifically the *Authorization Code Grant*. If you are not familiar with how this grant type works, [Auth0 has a great explanation on its website.](https://auth0.com/docs/get-started/authentication-and-authorization-flow/authorization-code-flow)

### Flow Example

Saloon has provided methods for the full Authorization Code grant.

```php
$connector = new SpotifyConnector;

// 1. Redirect the user to the authorization URL...

$authorizationUrl = $connector->getAuthorizationUrl($scopes, $state);

// 2. Handle the callback from the API provider and create an access token...

$authenticator = $connector->getAccessToken($code, $state);

// 3. Authenticate the connector

$connector->authenticate($authenticator);

// 4. Send your requests

$connector->send(new GetTracksRequest);

// 5. Refresh your access tokens...

$newAuthenticator = $connector->refreshAccessToken($authenticator);
```

### Getting Started

Let’s start with preparing our connector to support the Authorization Code Flow. All we have to do is add the `AuthorizationCodeGrant` trait to our connector.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyConnector extends Connector;
{
    use AuthorizationCodeGrant;
}
```

After you have added the trait, you will need to tell Saloon how to authenticate with your API. First, extend the `defaultOauthConfig` method and use the methods to define your client ID, secret and redirect URI. Saloon also has sensible defaults set for the authorization and token endpoints, but you may customize them if you need to. For example, Spotify has a different base URL than the connector's base URL, so we have overwritten it in this example.

You can also provide default scopes and even provide a callback to modify the OAuth2 requests being sent.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyConnector extends Connector
{
    use AuthorizationCodeGrant;

    public function resolveBaseUrl(): string
    {
        // Spotify's API has a different base URL for OAuth2 auth.

        return 'https://api.spotify.com/v1';
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId('my-client-id')
            ->setClientSecret('my-client-secret')
            ->setDefaultScopes(['user-read-currently-playing'])
            ->setRedirectUri('https://my-app.saloon.dev/auth/callback')
            ->setAuthorizeEndpoint('https://accounts.spotify.com/authorize')
            ->setTokenEndpoint('https://accounts.spotify.com/api/token')
            ->setUserEndpoint('/me')
            ->setRequestModifier(function (Request $request) {
                // Optional: Modify the requests being sent.
            });
    }
}
```

{% hint style="info" %}
Each of the endpoint methods, like `setAuthorizeEndpoint`on the OAuthConfig class support full URLs if you need to overwrite the base URL on the connector however you may just use the endpoint if the base URL is the same.
{% endhint %}

#### Overwriting the OAuth2 config

Sometimes, you may have a different OAuth2 client ID and secret for each user of your application. If your OAuth2 config is dependent on a per-user/tenant basis, it's recommended that you pass in the credentials as constructor arguments of your connector and then set the `oauthConfig` inside the constructor.

In the following example, I will pass in the `$clientId` and the `$clientSecret` as constructor arguments and overwrite the OAuth2 config.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;

class SpotifyConnector extends Connector
{
    use AuthorizationCodeGrant;

<strong>    public function __construct(string $clientId, string $clientSecret)
</strong>    {
        $this->oauthConfig()->setClientId($clientId);
        $this->oauthConfig()->setClientSecret($clientSecret);
    }

    // ...
}
</code></pre>

### Creating an Authorization URL

Now we have setup our connector to support the authorization code grant, we are ready to start the OAuth2 process. Usually, the first stage is to generate a URL to redirect our application users to. To generate an authorization URL, you can use the `getAuthorizationUrl` method on the connector.

```php
<?php

$connector = new SpotifyConnector;
$authorizationUrl = $connector->getAuthorizationUrl();
```

You can also pass in scopes which will be merged with the default scopes if you provided them in the OAuth config. Saloon will separate scopes with spaces but if your API integration requires scopes to be separated any other way, you can specify this with the `scopeSeparator` argument.

```php
$authorizationUrl = $connector->getAuthorizationUrl(
    scopes: ['user-library-read'],
    scopeSeparator: '+',
);
```

You can also provide additional query parameters if you need to with the `additionalQueryParameters` argument. This should be a key-value array where the key is the query parameter name and the value is the value of the query parameter.

```php
$authorizationUrl = $authConnector->getAuthorizationUrl(
    additionalQueryParameters: [
        'username' => 'JohnWayne'
    ],
);
```

#### State

To help prevent CSRF attacks or send additional data during authentication, you can pass an additional unique string in your authorization URL that the API's OAuth2 server will send back to you after your user has approved or denied access to your OAuth2 app.

```php
$authorizationUrl = $authConnector->getAuthorizationUrl(
    state: 'application-user-id',
);
```

If you do not provide your own state, Saloon will automatically generate a unique, 32-character string. Once you have generated the authorization URL, you can then use the `getState` method on your connector to get the state back. You should store this string in your application's session or cache to be verified later.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$connector = new SpotifyConnector;
$authorizationUrl = $connector->getAuthorizationUrl();

<strong>$state = $authConnector->getState(); // '8484b43fdjfdnfdj3llls...'
</strong></code></pre>

### Creating Access Tokens

After the user has approved your application, the API provider will redirect you back to your application with an authorization code and state. This data usually sent in the form of query parameters should be passed into your `getAccessToken` method on your connector. If successful, the method will return an `AccessTokenAuthenticator`. The access token, refresh token and expiry are wrapped up in a [Saloon Authenticator](https://docs.saloon.dev/the-basics/authentication#custom-authenticators) class that can be used to authenticate your connector/requests. It acts like a DTO that can be easily serialized and transported around your application.

<pre class="language-php"><code class="lang-php">&#x3C;?php

$connector = new SpotifyConnector;
<strong>$authenticator = $authConnector->getAccessToken($code);
</strong>
// Use authenticator to authenticate your connector instance

$connector->authenticate($authenticator);

// Any request sent through this connector will now have authentication applied

$connector->send(new GetTracksRequest);
</code></pre>

{% hint style="info" %}
Once you have received the authenticator instance, you should cache it securely in your application for future use. Read further to see how you can do this.
{% endhint %}

#### Verifying State

If you stored the state that was generated during creating an authorization URL, you should pass this expected state alongside the state sent back by the API provider's OAuth2 server. This will be used to verify the state provided back by the application is valid. If the state does not match the expected state, Saloon will throw an exception.

```php
<?php

$authConnector = new SpotifyConnector;

// It will throw an exception if the state and expected state don't match
// however both must be present

$authenticator = $authConnector->getAccessToken($code, $state, $expectedState);
```

### Storing Authentication For Later

You will likely need to store the authenticator securely so you can use it for future requests. You may serialize and unserialize the authenticator class using the helper methods below, then you can store the string wherever you like, usually encrypted in the database if it's against a user. Then, you can retrieve this authenticator and use it to authenticate your connector.

```php
<?php

$connector = new SpotifyConnector;
$authenticator = $connector->getAccessToken($code);

// Securely store this against your user.

$serialized = $authenticator->serialize();

// Unserialize the authenticator when retrieving it

$authenticator = AccessTokenAuthenticator::unserialize($serialized);
```

### Authenticator Methods

The authenticator returned by Saloon when using the `getAccessToken` or `refreshAccessToken` methods will contain the Access Token, Refresh Token and Expiry Date that was returned by the OAuth2 server. You can access these properties with the following methods. You can also check if the authenticator has expired, which will come in handy when refreshing access tokens.

```php
<?php

$authenticator->getAccessToken();
$authenticator->getRefreshToken();
$authenticator->getExpiresAt();

$authenticator->hasExpired();
$authenticator->hasNotExpired();
```

### Refreshing Access Tokens

When retrieving your authenticator out of storage, you should always check if the access token has expired and if it needs refreshing. If the authenticator's access token has expired, you can call the `refreshAccessToken` method which will create a fresh authenticator.

In this example, `$user` is the user of my application and I have written methods to get and store the authenticators.

```php
<?php

// $user in this example is my application's user

$authenticator = $user->getCachedAuthenticator();
$connector = new SpotifyConnector;

if ($authenticator->hasExpired()) {
    // We'll refresh the access token which will return a new authenticator
    // which we can store against our user in our application.

    $authenticator = $connector->refreshAccessToken($authenticator);
    $user->updateAuthenticator($authenticator);
}

// Authenticate our connector and send the request

$connector->authenticate($authenticator);

$response = $connector->send(new GetTracksRequest);
```

{% hint style="info" %}
If you are using Laravel and the Saloon Laravel library, you can use built-in `EncryptedOAuthAuthenticatorCast` **/** `OAuthAuthenticatorCast` Eloquent casts to automatically cast the authenticator for storing in your database.
{% endhint %}

### Customising The Authenticator

Sometimes the API provider you are authenticating with may require additional information to be used in the authenticator. You can customise how the authenticator will be created by extending the `createAccessTokenAuthenticator` method on your connector.

```php
<?php

protected function createOAuthAuthenticator(string $accessToken, string $refreshToken, DateTimeImmutable $expiresAt): OAuthAuthenticatorInterface
{
    return new SpotifyAuthenticator($accessToken, $refreshToken, $expiresAt);
}
```

### Customising How The Authenticator Is Created

Sometimes the API provider you are authenticating with may have a different way that they respond with their tokens. If you need to customise the way Saloon creates the authenticator you can extend the `createOAuthAuthenticatorFromResponse` method.

```php
<?php

protected function createOAuthAuthenticatorFromResponse(SaloonResponse $response, string $fallbackRefreshToken = null): OAuthAuthenticatorInterface
{
    $responseData = $response->object();

    $accessToken = $responseData->access_token;
    $refreshToken = $responseData->refresh_token ?? $fallbackRefreshToken;
    $expiresAt = new DateTimeImmutable('+' . $responseData->expires_in . ' seconds');

    return $this->createOAuthAuthenticator($accessToken, $refreshToken, $expiresAt);
}
```

### Customising The Requests

Sometimes you might integrate with an API that requires additional query parameters or headers to be sent with the OAuth2 flow. You may use the `requestModifier` property on the methods or use the `setRequestModifier` method within the `OAuthConfig` to add a callable that is invoked before a request is sent.

{% tabs %}
{% tab title="Per Request" %}

```php
<?php

$connector->getAccessToken($code, requestModifier: function (Request $request) {
    $request->query()->add('access_type', 'offline');
});

$connector->getRefreshToken($code, requestModifier: function (Request $request) {
    $request->headers()->add('X-App-Key', $appKey);
});

$connector->getUser($code, requestModifier: function (Request $request) {
    $request->headers('Accept', 'text/plain');
});
```

{% endtab %}

{% tab title="All Requests" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Http\OAuth2\GetUserRequest;
use Saloon\Http\OAuth2\GetAccessTokenRequest;
use Saloon\Http\OAuth2\GetRefreshTokenRequest;

protected function defaultOauthConfig(): OAuthConfig
{
    return OAuthConfig::make()
        ->setClientId('my-client-id')
        ->setClientSecret('my-client-secret')
        ->setRedirectUri('https://my-app.saloon.dev/auth/callback')
        ->setRequestModifier(function (Request $request) {
            // This callback is invoked on every request, so you
            // may want to use if-statements or a match statement
            // to apply conditions based on request.

            if ($request instanceof GetAccessTokenRequest) {
                $request->query()->add('access_type', 'offline');
            }

            if ($request instanceof GetRefreshTokenRequest) {
                $request->headers()->add('X-App-Key', $appKey);
            }

            if ($request instanceof GetUserRequest) {
                $request->headers('Accept', 'text/plain');
            }
        }),
}
```

{% endtab %}
{% endtabs %}

### Using your own request classes

There are situations where Saloon's own request classes for getting the access token, refreshing the access token or getting the user might not suit the API you are integrating with. For example, if an API uses JSON encoding instead of form encoding. You may use the following methods on your connector to overwrite the instantiation process of the request classes.

```php
<?php

class SpotifyConnector extends Connector
{
    // ...

    protected function resolveAccessTokenRequest(string $code, OAuthConfig $oauthConfig): Request
    {
        return new CustomGetAccessTokenRequest($code, $oauthConfig);
    }

    protected function resolveRefreshTokenRequest(OAuthConfig $oauthConfig, string $refreshToken): Request
    {
        return new CustomGetRefreshTokenRequest($oauthConfig, $refreshToken);
    }

    protected function resolveUserRequest(OAuthConfig $oauthConfig): Request
    {
        return new CustomGetUserRequest($oauthConfig);
    }
}
```

### Returning Responses

If you prefer, you may request Saloon to return a `Saloon\Http\Resonse` instance instead of a `AccessTokenAuthenticator` when creating or refreshing access tokens. To use responses, just provide the `returnResponse` argument when creating or refreshing access tokens.

```php
<?php

$connector = new SpotifyConnector;

$response = $connector->getAccessToken(
    returnResponse: true,
);

$response = $connector->refreshAccessToken(
    returnResponse: true,
);
```

### Real-world example

If you would like to see an example integration using the OAuth2 methods mentioned above, you can see the following Laravel application.

<https://github.com/Sammyjo20/saloon-v2-spotify-example>


# Client Credentials Grant

Some API providers implement the OAuth2 *Client Credentials Grant* for authentication. Implementing this grant type every time you create a new API integration can be tedious and time-consuming. Saloon offers a simple, extendable OAuth2 trait to help you get up and running quickly.

### Prerequisites

This section of the documentation assumes that you are familiar with OAuth2 and specifically the *Client Credentials Grant*. If you are not familiar with how this grant type works, [Auth0 has a great explanation on its website.](https://auth0.com/docs/get-started/authentication-and-authorization-flow/client-credentials-flow)

### Flow Example

Saloon has provided methods for the full client credentials grant.

```php
$connector = new WarehouseConnector;

// 1. Create an access token authenticator

$authenticator = $connector->getAccessToken($scopes);

// 2. Authenticate the connector

$connector->authenticate($authenticator);

// 3. Send your requests

$connector->send(new GetInventoryRequest);
```

### Getting Started

Let's start with preparing our connector to support the client credentials grant. All we have to do is add the `ClientCredentialsGrant` trait to our connector.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\ClientCredentialsGrant;

class WarehouseConnector extends Connector
{
    use ClientCredentialsGrant;
}
```

After you have added the trait, you will need to tell Saloon how to authenticate with your API. First, extend the `defaultOauthConfig` method and use the methods to define your client ID and secret. Saloon also has sensible defaults set for the token endpoint, but you may customize it if you need to. For example, some APIs have a different base URL than the connector's base URL. You can also provide default scopes and even provide a callback to modify the OAuth2 requests being sent.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\ClientCredentialsGrant;
use Saloon\Helpers\OAuth2\OAuthConfig;

class WarehouseConnector extends Connector
{
    use ClientCredentialsGrant;

    public function resolveBaseUrl(): string
    {
        return 'https://local-warehouse.app';
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId('my-client-id')
            ->setClientSecret('my-client-secret')
            ->setDefaultScopes(['inventory.read'])
            ->setTokenEndpoint('/oauth/token')
            ->setRequestModifier(function (Request $request) {
                // Optional: Modify the requests being sent.
            });
    }
}
```

{% hint style="info" %}
The endpoint method, like `setTokenEndpoint` on the OAuthConfig class supports full URLs if you need to overwrite the base URL on the connector however you may just use the endpoint if the base URL is the same.
{% endhint %}

#### Overwriting the OAuth2 config

Sometimes, you may have a different OAuth2 client ID and secret for each user of your application. If your OAuth2 config is dependent on a per-user/tenant basis, it's recommended that you pass in the credentials as constructor arguments of your connector and then set the `oauthConfig` inside the constructor.

In the following example, I will pass in the `$clientId` and the `$clientSecret` as constructor arguments and overwrite the OAuth2 config.

```php
<?php

use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\ClientCredentialsGrant;

class WarehouseConnector extends Connector
{
    use ClientCredentialsGrant;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->oauthConfig()->setClientId($clientId);
        $this->oauthConfig()->setClientSecret($clientSecret);
    }

    // ...
}
```

### Creating Access Tokens

You are now ready to create access tokens. You should use the `getAccessToken` method on your connector. If successful, the method will return an `AccessTokenAuthenticator`. The access token and expiry (if provided) are wrapped up in a [Saloon Authenticator](https://docs.saloon.dev/the-basics/authentication#custom-authenticators) class that can be used to authenticate your connector/requests. It acts like a DTO that can be easily serialized and transported around your application.

```php
<?php

$connector = new WarehouseConnector;
$authenticator = $connector->getAccessToken();

// Use authenticator to authenticate your connector instance

$connector->authenticate($authenticator);

// Any request sent through this connector will now have authentication applied

$connector->send(new GetInventoryRequest);
```

{% hint style="info" %}
Once you have received the authenticator instance, you should cache it securely in your application for future use. Read further to see how you can do this.
{% endhint %}

#### Custom Scopes

Sometimes you may need to provide an additional "scope" to declare the level of access that your token needs. You may provide default scopes in the OauthConfig class on your connector, but you can also provide additional scopes when creating access tokens. Saloon will separate scopes with spaces but if your API integration requires scopes to be separated any other way, you can specify this with the `scopeSeparator` argument.

```php
<?php

$connector = new WarehouseConnector;

$authenticator = $connector->getAccessToken(
    scopes: ['inventory.update', 'inventory.delete'],
    scopeSeparator: '+',
);
```

#### Returning Responses

If you prefer, you may request Saloon to return a `Saloon\Http\Resonse` instance instead of a `AccessTokenAuthenticator` when creating access tokens. To use responses, just provide the `returnResponse` argument when creating access tokens.

```php
<?php

$connector = new WarehouseConnector;

$response = $connector->getAccessToken(
    returnResponse: true,
);
```

### Authenticator Methods

The authenticator returned by Saloon when using the `getAccessToken` method will contain the Access Token, and optionally an expiry date that was returned by the OAuth2 server. You can access these properties with the following methods. You can also check if the authenticator has expired, which will come in handy when refreshing access tokens.

```php
<?php

$authenticator->getAccessToken();
$authenticator->getExpiresAt();

$authenticator->hasExpired();
$authenticator->hasNotExpired();
```

### Customising The Authenticator

Sometimes the API provider you are authenticating with may require additional information to be used in the authenticator. You can customise how the authenticator will be created by extending the `createAccessTokenAuthenticator` method on your connector.

```php
<?php

protected function createOAuthAuthenticator(string $accessToken, ?DateTimeImmutable $expiresAt = null): OAuthAuthenticatorInterface
{
    return new WarehouseAuthenticator($accessToken, $expiresAt);
}
```

### Customising How The Authenticator Is Created

Sometimes the API provider you are authenticating with may have a different way that they respond with their tokens. If you need to customise the way Saloon creates the authenticator you can extend the `createOAuthAuthenticatorFromResponse` method.

```php
<?php

protected function createOAuthAuthenticatorFromResponse(SaloonResponse $response): OAuthAuthenticatorInterface
{
    $responseData = $response->object();

    $accessToken = $responseData->access_token;
    $expiresAt = new DateTimeImmutable('+' . $responseData->expires_in . ' seconds');

    return $this->createOAuthAuthenticator($accessToken, $expiresAt);
}
```

### Customising The Requests

Sometimes you might integrate with an API that requires additional query parameters or headers to be sent with the OAuth2 flow. You may use the `requestModifier` method on the `getAccessToken` method or use the `setRequestModifier` method within the `OAuthConfig` to add a callable that is invoked before a request is sent.

{% tabs %}
{% tab title="Per Request" %}

```php
<?php

$connector->getAccessToken(requestModifier: function (Request $request) {
    $request->query()->add('access_type', 'offline');
});
```

{% endtab %}

{% tab title="All Requests" %}

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Http\OAuth2\GetClientCredentialsTokenRequest;

protected function defaultOauthConfig(): OAuthConfig
{
    return OAuthConfig::make()
        ->setClientId('my-client-id')
        ->setClientSecret('my-client-secret')
<strong>        ->setRequestModifier(function (GetClientCredentialsTokenRequest $request) {
</strong><strong>            //
</strong><strong>        }),
</strong>}
</code></pre>

{% endtab %}
{% endtabs %}

### Using your own request classes

There are situations where Saloon's own request classes for getting the access token might not suit the API you are integrating with. For example, if an API uses JSON encoding instead of form encoding. You may use the following method on your connector to overwrite the instantiation process of the request class.

```php
<?php

class SpotifyConnector extends Connector
{
    // ...

    protected function resolveAccessTokenRequest(OAuthConfig $oauthConfig, array $scopes = [], string $scopeSeparator = ' '): Request
    {
        return new GetClientCredentialsTokenRequest($oauthConfig, $scopes, $scopeSeparator);
    }
}
```


# Middleware

Saloon has a powerful middleware system that allows you to tap into the request and response lifecycle and make any changes you need before the request is sent or the response is given back to the user. This is useful if you want to build your own advanced Saloon integrations or write more advanced logic like generating a unique reference for every request.

### The Boot Method

Before we get into Saloon's middleware, there is a useful built-in method on every connector and request that you can utilize. This is the `boot` method. It gets executed every time you send a request. You will get access to the underlying `PendingRequest` instance that the sender will provide to the HTTP client. The boot method is a great way to quickly tap into a pending request and change something like add a header, modify the request body or even trigger events.

You may extend the public boot method on either your connector or your request. Remember if you use the connector's boot method, every request with that connector will use that boot method.

You may register middleware inside of the boot method too, which will be used in the rest of the `PendingRequest` lifecycle.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;

class ForgeConnector extends Connector
{
    // ...
    
    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('X-Current-Time', new DateTime);
    }
}
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Request;
use Saloon\Http\PendingRequest;

class GetServersRequest extends Request
{
    // ...
    
    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('X-Current-Time', new DateTime);
    }
}
```

{% endtab %}
{% endtabs %}

### The PendingRequest

The `PendingRequest` class is an intermediary class that Saloon uses to populate everything when you send a request. Every time you send a request, a new `PendingRequest` instance is created which prevents your connector or request from being mutated every time a request is sent. The `PendingRequest` class has many of the methods that you are used to seeing on the request/connector for managing headers, query parameters, config, and request body.

The `PendingRequest` class is used for boot methods, middleware and [plugins](https://docs.saloon.dev/installable-plugins/traits).

### Request Middleware

You may also at any point tap into the request lifecycle by using request middleware. Request middleware is useful for changing something on the `PendingRequest` instance before the request is sent like making another request to get an authentication token for the original request.

On your connector or request, call the `middleware()` method and use the `onRequest()` method. You should provide a callable, like a closure or invokable class. This callable will be given access to the `PendingRequest` instance which can be mutated.

{% hint style="info" %}
Return values are not required, but you may either return an instance of `PendingRequest` or a `FakeResponse` class for an early fake response.
{% endhint %}

#### Anonymous Functions

You can use a regular closure/anonymous function to create a middleware on the fly.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\PendingRequest;

$forge = new ForgeConnector;

$forge->middleware()->onRequest(function (PendingRequest $pendingRequest) {
    $pendingRequest->headers()->add('Authorization', 'Bearer ' . $token);
});
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\PendingRequest;

$request = new GetServersRequest;

$request->middleware()->onRequest(function (PendingRequest $pendingRequest) {
    $pendingRequest->headers()->add('Authorization', 'Bearer ' . $token);
});
```

{% endtab %}
{% endtabs %}

#### Invokable Classes

You may also use invokable classes to keep your middleware classes tidy. If you are using invokable middleware classes, you should implement the `RequestMiddleware` interface.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Http\PendingRequest;
use Saloon\Contracts\RequestMiddleware;

class AuthenticateRequest implements RequestMiddleware
{
    public function __invoke(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', 'Bearer ' . $token);
    }
}
```

{% endtab %}

{% tab title="Usage (Connector)" %}

```php
<?php

use Saloon\Http\PendingRequest;

$forge = new ForgeConnector;
$forge->middleware()->onRequest(new AuthenticateRequest);
```

{% endtab %}

{% tab title="Usage (Request)" %}

```php
<?php

use Saloon\Http\PendingRequest;

$request = new GetServersRequest;
$request->middleware()->onRequest(new AuthenticateRequest);
```

{% endtab %}
{% endtabs %}

### Early Fake Responses

You may also choose to tap into Saloon's mock functionality by creating your own fake responses. When you return a `FakeResponse`, the rest of the request middleware will still be processed but the fake response will be stored on the `PendingRequest`.

If this fake response is present before Saloon sends the request, it won't send the request to the sender, instead, Saloon will use the `FakeResponse`. This is super handy if you want to build your own middleware that stops Saloon from sending real requests, like for caching.

```php
<?php

use Saloon\Http\PendingRequest;
use Saloon\Http\Faking\FakeResponse;

$request = new GetServersRequest;

$request->middleware()
    ->onRequest(function (PendingRequest $pendingRequest) {
        return new FakeResponse(
            body: ['data' => 'Fake Data!'], 
            status: 200, 
            headers: []
        );
    });
```

{% hint style="warning" %}
Even though you are returning a `FakeResponse` class, the next middleware will still receive the PendingRequest instance. Additionally, if another middleware also returns a fake response, the latest one will be used.
{% endhint %}

### Response Middleware

Once you have sent your request, even if it's a fake response, Saloon will send the response down the response middleware pipeline. You may add your own response middleware to change the response class or do something like log responses.

On your connector or request, you can call the `middleware()` method and use the `onResponse()` method. You should provide a callable, like a closure or invokable class. This callable will be given access to the `Response` instance.

{% hint style="info" %}
Return values are not required, but you may return an instance of `Saloon\Http\Response` to overwrite the response class in the middleware.
{% endhint %}

#### Anonymous Functions

You can use a regular closure/anonymous function to create a middleware on the fly.

{% tabs %}
{% tab title="Connector" %}

```php
<?php

use Saloon\Http\Response;

$forge = new ForgeConnector;

$forge->middleware()->onResponse(function (Response $response) {
    Logger::recordResponse($response);
});
```

{% endtab %}

{% tab title="Request" %}

```php
<?php

use Saloon\Http\Response;

$request = new GetServersRequest;

$request->middleware()->onResponse(function (Response $response) {
    Logger::recordResponse($response);
});
```

{% endtab %}
{% endtabs %}

#### Invokable classes

Similar to request middleware, you can also create invokable middleware classes for response middleware. You should use the `ResponseMiddleware` contract to ensure your invokable class has the correct signature.

{% tabs %}
{% tab title="Definition" %}

```php
<?php

use Saloon\Http\Response;
use Saloon\Contracts\ResponseMiddleware;

class LogResponse implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        Logger::recordResponse($response);
    }
}
```

{% endtab %}

{% tab title="Usage (Connector)" %}

```php
<?php

use Saloon\Http\PendingRequest;

$forge = new ForgeConnector;
$forge->middleware()->onResponse(new LogResponse);
```

{% endtab %}

{% tab title="Usage (Request)" %}

```php
<?php

use Saloon\Http\PendingRequest;

$request = new GetServersRequest;
$request->middleware()->onResponse(new LogResponse);
```

{% endtab %}
{% endtabs %}

### Using Constructors

While registering middleware on the fly is really useful, it often leads to repeated code. If you would like your connector to always have a specific request or response middleware you should use the `boot` method described above, or use the constructor of your connector or request.

```php
<?php

class ForgeConnector extends Connector
{
    // {...}
    
    public function __construct()
    {
        $this->middleware()->onRequest(new AuthenticateRequest);
        $this->middleware()->onResponse(new LogResponse);
    }
}
```

{% hint style="danger" %}
Be cautious using anonymous non-static closures inside the constructor/boot method. This may cause issues like Saloon not being able to close connections properly. [Click here to read more.](https://docs.saloon.dev/conclusion/known-issues#usage-of-anonymous-functions-with-long-running-processes-like-laravel-queues)
{% endhint %}

### Using Plugins

Plugins are another useful feature for Saloon that allows you to extend Saloon and tap into the middleware from traits. This is especially useful if you intend to use the trait on multiple requests or connectors.

[Read through the plugins page for more information.](https://docs.saloon.dev/installable-plugins/traits)

### Middleware Naming

You can choose to name your middleware. Each name must be unique to the given pipeline. For example, you cannot have two middleware with the same name on the request pipeline, but you could have the same name used once for the request pipeline and once for the response pipeline. Use the second argument to specify a name.

```php
<?php

$request = new GetServersRequest;

$request->middleware()->onResponse(new LogResponse, 'logResponse');
```

### Global Middleware

Saloon also supports adding global middleware. You most likely won't need this level of granularity but if you do, you may use the `Config` class. When using global middleware, you should make sure to name your middleware so it isn't accidentally registered twice.

```php
<?php

use Saloon\Config;

Config::globalMiddleware()->onResponse(new LogResponse, 'logResponse');
```

{% hint style="danger" %}
Be cautious with global middleware. Since it uses a static property behind the scenes, the value is kept between tests when running a full test suite. You can use the `Config::clearGlobalMiddleware()` method to get around this issue.
{% endhint %}

### Middleware Execution Order

The following is the order in which middleware is executed.

1. Global middleware (Like the Laravel plugin)
2. Mock client finds a fake response (if present)
3. Plugin middleware
4. User-added middleware
5. Debugging middleware is run (for the final object)

### Choosing When Your Middleware Is Executed

You may choose to change when your middleware is executed. For example, you might want to run something at the very end of all the other middleware that has been run. This is especially useful if you are building a plugin for people to install in Saloon and require the final request object before it is sent. You can use the third argument to specify an order. This expects a `PipeOrder` enum and allows you to choose either `FIRST` or `LAST`.&#x20;

When the middleware is executed, any middleware marked as "first" will run first, and any that have been marked as "last" will be executed last.

<pre class="language-php"><code class="lang-php">&#x3C;?php

use Saloon\Http\Request;
use Saloon\Http\PendingRequest;
use Saloon\Enums\PipeOrder;

class GetServersRequest extends Request
{
    // { ... }
    
    public function boot(PendingRequest $pendingRequest): void
    {
<strong>        $request->middleware()->onRequest(new RecordResponse, order: PipeOrder::LAST);
</strong>    }
}
</code></pre>

### Middleware Caveats

Here are some known caveats that you should know about when using Saloon's middleware.

* You cannot add request middleware from inside of another request middlewar&#x65;**,** but you can add response middleware inside of the onRequest() middleware method.
* You cannot add response middleware from inside of another response middleware.
* You may return a fake response in request middleware but you will always get a PendingRequest back

### Guzzle Handlers / Middleware

With previous versions of Saloon, you could add Guzzle middleware or "handlers" directly to the connector or request. From version two, Saloon is now sender-agnostic, so the `addHandler` method has been removed but you may still add Guzzle middleware if you are using the `GuzzleSender` (the default sender with Saloon)

### Adding Guzzle Middleware

You can add middleware to the Guzzle client by using the `sender` method on the connector. You must only add Guzzle middleware directly on your connector with your constructor method. This is because Saloon only instantiates the sender once, so in order to prevent middleware from being registered multiple times, it should be placed in the constructor.

```php
<?php

class ForgeConnector extends Connector
{
    // {...}
    
    public function __construct()
    {
        $this->sender()->addMiddleware(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request->withHeader('X-Custom-Header', 'Hello');
                
                return $handler($request, $options);             
            };
        })
    }
}
```

{% hint style="info" %}
To read more about Guzzle's middleware and handlers [click here](https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html).
{% endhint %}

### Accessing The Underlying Guzzle Instance

You may need to modify the Guzzle client or the handler stack. If you need to do this, you can use the `getGuzzleClient` or `getHandlerStack` methods.

```php
<?php

class ForgeConnector extends Connector
{
    // {...}
    
    public function __construct()
    {
        $guzzleClient = $this->sender()->getGuzzleClient();
        
        $handlerStack = $this->sender()->getHandlerStack();
    }
}
```




---

[Next Page](/llms-full.txt/1)
