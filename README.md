# Curl - PHP
Advanced Curl Library

#### Curl::post
```php
// You can register a variable, or initiate a callback.
$curl = Curl::post(

  // Setup url and fields
  array(
    'url' => 'http://example.com',
    'query' => array(
      'data' => 'some type of data sent through post'
    )
  ),
  
  // Setup additional curl options
  array (
      CURLOPT_TIMEOUT => 5000,
      CURLOPT_MAXREDIRS => 5
  )
  
  /*
  ,
  // Callback function after executed, returns response and curl info
  function($response, $info) {
    print_r($response);
  }
  */
);

// get response
print_r($curl->response);

// get info
print_r($curl->info);

```
#### Curl::get

```php
Curl::get(

  // Setup url and fields
  array(
    'url' => 'http://example.com',
    'query' => array(
      'data' => 'some type of data sent through get'
    )
  ),
  
  // Setup additional curl options
  array (
      CURLOPT_TIMEOUT => 5000,
      CURLOPT_MAXREDIRS => 5
  ),
  
  // Callback function after executed, returns response and curl info
  function($response, $info) {
    print_r($response);
  }

);
```

#### Curl::get (Download)
```php
Curl::get(array(
  "url" => "https://raw.githubusercontent.com/LegitSoulja/Curl/master/README.md"
), null, function($a){
  print_r($a);
});
```
