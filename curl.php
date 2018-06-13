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
    if(self::parse(self::array_keys_exists(array('url', 'fields'), $data))) {
      $curl = curl_init($data['url']);
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(($data['fields'] == null) ? array() : $data['fields']));

      // additional options
      if(is_array($options)) {
        foreach($options as $opt => $v) {
          curl_setopt($curl, $opt, $v);
        }
      }

      $res = curl_exec($curl);
      $info = curl_getinfo($curl);
      curl_close($curl);

      if(is_object($callback)) {
        return $callback($res, $info);
      }

      return (object) array('response'=>$res, 'info'=>$info);

    }
  }

  public static function get($data, $options, $callback){
    if(self::parse(self::array_keys_exists(array('url', 'fields'), $data))) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $data['url']."?".http_build_query(($data['fields'] == null) ? array() : $data['fields']));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($curl, CURLOPT_HTTPGET, 1);

      // additional options
      if(is_array($options)) {
        foreach($options as $opt => $v) {
          curl_setopt($curl, $opt, $v);
        }
      }
      $res = curl_exec($curl);
      $info = curl_getinfo($curl);
      curl_close($curl);
      if(is_object($callback)) {
        return $callback($res, $info);
      }

      return (object) array('response'=>$res, 'info'=>$info);

    }
  }

  public static function test(){
    self::get(
      array(
        'url'=>'http://httpbin.org/response-headers', 
        'fields'=>array("a"=>"1", "b"=>2)
      ), null, 
      function($response, $info){

        print_r(func_get_args());
      }
    );
  }

};
