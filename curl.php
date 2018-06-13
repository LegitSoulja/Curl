<?php

class Curl {

  private static function array_keys_exists($needles, &$haystack) {
    if(!is_array($needles)) return array(0x3);
    foreach($needles as &$needle) {
      if(!array_key_exists($needle, $haystack)) 
        return array(0x0, $needle);
      if(is_string($haystack[$needle]) && strlen($haystack[$needle]) < 1) 
        return array(0x1, $needle);
    }
    return array(0x2, $needle);
  }

  private static function parse($v){
    switch($v[0]) {
      case 0x0: throw new Exception("[Curl] No key present for `".$v[1]."`");
      case 0x1: throw new Exception("[Curl] No data present for `".$v[1]."`");
      case 0x2: return true;
      case 0x3: throw new Exception("[Curl] array_keys_exists needles must be an array");
      default: throw new Exception("[Curl] An error has occured with parsing.");
    }
  }

  public static function post($data, $options, $callback){

    if(!filter_var($data['url'], FILTER_VALIDATE_URL)) {
      throw new Exception("Invalid URL provided");
    }

    $curl = curl_init($data['url']);
    curl_setopt($curl, CURLOPT_POST, ((is_array($data['query']) ? count($data['query']) : 0)));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query((array_key_exists('query', $data) && is_array($data['query'])) ? $data['query']: array()));

    if(array_key_exists('type', $data)) {
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: '.$data['type']));
    }

    if(is_array($options)) {
      foreach($options as $opt => $v) {
        curl_setopt($curl, $opt, $v);
      }
    }

    $obj = (object) array('response'=>curl_exec($curl), 'info' => curl_getinfo($curl));
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

    $curl = curl_init($data['url']."?".http_build_query((array_key_exists('query', $data) && is_array($data['query'])) ? $data['query']: array()));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);

    if(array_key_exists('type', $data)) {
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: '.$data['type']));
    }

    if(is_array($options)) {
      foreach($options as $opt => $v) {
        curl_setopt($curl, $opt, $v);
      }
    }

    $obj = (object) array('response'=>curl_exec($curl), 'info' => curl_getinfo($curl));
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
            'type'=>'application/json'
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
            'type' => 'application/json'
          ), null, 
          function($response, $info){
            print_r(func_get_args());
          }
        );
      default: return self::test('post');
    }
  }

};
