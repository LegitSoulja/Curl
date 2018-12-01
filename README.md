# Curl - PHP
Easy Curl Library

###Curl::POST

```php
Curl::POST(array(
  'url' => 'http://example.com',
  'type' => 'json',
  'data' => array(),
  'success' => function($r, $i){
      print_r($r);
  },
  'error' => function($e){
  }
), array(
  // extra curl options
), function(){
  // promise callback
});
```

####Curl::GET
```php
Curl::GET(array(
  'url' => 'http://example.com',
  'type' => 'json',
  'data' => array(),
  'success' => function($r, $i){
      print_r($r);
  },
  'error' => function($e){
  }
), array(
  // extra curl options
), function(){
  // promise callback
});
```

####Curl::extend

> At any time, you may need to parse data some way. Curl only has a json parser, but you can extend Curl to parse whatever type you throw at it.

```php
Curl::extend('json', function($data) {
  return json_encode($data);
})
```

> Note: Curl::extended parsers should throw errors when things happen. They're captured and sent to error, of your curl request and or the promise callback.
