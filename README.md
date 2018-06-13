# Curl - PHP
Advanced Curl Library

#### Curl::post
```php

Curl::post(

  // Setup url and fields
  array(
    'url' => 'http://example.com',
    'fields' => array(
      'data' => 'password'
    )
  ),
  
  // Setup additional curl options
  array (
      CURLOPT_URL => 'https://example.com'
  ),
  
  // Callback function after executed, returns response and curl info
  function($response, $info) {
    print_r($response);
  }

);

```
#### Curl::get
> *Note*: (Nothing changed except the static method)
```php
Curl::get(

  // Setup url and fields
  array(
    'url' => 'http://example.com',
    'fields' => array(
      'data' => 'password'
    )
  ),
  
  // Setup additional curl options
  array (
      CURLOPT_URL => 'https://example.com'
  ),
  
  // Callback function after executed, returns response and curl info
  function($response, $info) {
    print_r($response);
  }

);
```
