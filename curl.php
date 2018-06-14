<?php

class Curl {

  public function __construct(){
    throw new Exception("Curl is a static class, and cannot be initalized.");
  }

  private static function registerCurl(&$curl, $options) {
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    return $curl;
  }

  private static function parse($data, $type) {
    switch(strtolower($type)) {
      case 'json':
        try{
          $parse = json_decode($data);
          if($parse === null) {
            throw new Exception("Failed to parse data as json");
          }
          return $parse;
        }catch(Exception $ex) {
          return $data;
        }
      default: return $data;
    }
  }

  public static function post($data, $options, $callback){

    if(!filter_var($data['url'], FILTER_VALIDATE_URL)) {
      throw new Exception("Invalid URL provided");
    }

    $curl = null;

    $register = array(
      CURLOPT_URL => $data['url'],
      CURLOPT_POST => ((is_array($data['query']) ? count($data['query']) : 0)),
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_FOLLOWLOCATION => 1,
      CURLOPT_POSTFIELDS => http_build_query((array_key_exists('query', $data) && is_array($data['query'])) ? $data['query']: array())
    );

    if(is_array($options)) {
      $register = array_merge($options, $register);
    }

    self::registerCurl($curl, $register);

    $obj = (object) array('response'=>self::parse(curl_exec($curl), ((array_key_exists('type', $data) ? $data['type'] : null)) ), 'info' => curl_getinfo($curl));
    curl_close($curl);

    if(is_object($callback)) {
      return $callback($obj->response, $obj->info);
    }

    return $obj;

  }

  public static function get($data, $options, $callback){

    if(!filter_var($data['url'], FILTER_VALIDATE_URL)) {
      throw new Exception("Invalid URL provided");
    }

    $curl = null;

    $register = array(
      CURLOPT_URL => ($data['url']."?".http_build_query((array_key_exists('query', $data) && is_array($data['query'])) ? $data['query']: array())),
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_FOLLOWLOCATION => 1,
      CURLOPT_HTTPGET => 1
    );

    if(is_array($options)) {
      $register = array_merge($options, $register);
    }

    self::registerCurl($curl, $options);

    $obj = (object) array('response'=>self::parse(curl_exec($curl), ((array_key_exists('type', $data) ? $data['type'] : null)) ), 'info' => curl_getinfo($curl));
    curl_close($curl);

    if(is_object($callback)) {
      return $callback($obj->response, $obj->info);
    }

    return $obj;

  }

  public static function test($a = null){
    switch(strtolower($a)) {
      case 'post':
        self::post(
          array(
            'url'=>'http://httpbin.org/post', 
            'query'=>array('type'=>'post', 'data'=>array('post')),
            'type' => 'json'
          ), null, 
          function($response, $info){
            print_r(func_get_args());
          }
        );
        break;
      case 'get':
        self::get(
          array(
            'url'=>'http://httpbin.org/get', 
            'query'=>array('type'=>'get', 'data'=>array('get')),
            'type' => 'json'
          ), null, 
          function($response, $info){
            print_r(func_get_args());
          }
        );
        break;
      default: return self::test('post');
    }
  }
}
