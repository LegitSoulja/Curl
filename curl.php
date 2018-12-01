<?php

final class Parser {
    
    private $parsers = array();
    
    public function __construct(){
        $this->parsers['json'] = function($d){
            return json_decode($d);
        };
    }
    
    public function extend($name, $func){
      $this->parsers[$name] = $func;
    }

    public function parse($data, $type) {
        return ((array_key_exists($type, $this->parsers)) ? $this->parsers[$type]($data) : false);
    }
    
}

final class Curl {
    

    private static $parser = null;
    
    public function __construct() {
        throw new Exception('Curl is a static class, and cannot be initialized');
    }
    
    private static function parser(){
        if(self::$parser == null) {
            self::$parser = new Parser();
        }
        return self::$parser;
    }
    
    public static function extend($name, $func) {
        self::parser()->extend($name, $func);
    }
    
    private static function register($options, &$curl = null){
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        return $curl;
    }
    
    public static function POST($req, $opt = array(), $cb = null) {
        if(!is_array($req) && !is_object($req)) throw new Exception('Invalid first argument, expect an array insted');
        if(!array_key_exists('url', $req) || !filter_var($req['url'], FILTER_VALIDATE_URL)) throw new Exception("Invalid URL provide, Curl::POST");
        
        
        self::register(self::array_merge_keys(
            array(
                CURLOPT_URL => $req['url'],
                CURLOPT_POST => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POSTFIELDS => ((array_key_exists('data', $req)) ? http_build_query($req['data']) : null)
            ), $opt
        ), $curl);
        
        $obj = array();
        try{
            $obj = array(
                 'response' => self::parser()->parse(curl_exec($curl), ((array_key_exists('type', $req)) ? $req['type'] : 'json')),
                 'info' => curl_getinfo($curl)
            );
        } catch (Exception $ex) {
            curl_close($curl);
            if(array_key_exists('error', $req) && is_callable($req['error'])) {
                call_user_func_array($req['success'], $obj);
            }
            return;
        }
        
        curl_close($curl);
        
        if(array_key_exists('success', $req) && is_callable($req['success'])) {
            call_user_func_array($req['success'], $obj);
        }
        
        if(is_callable($cb)) {
            call_user_func_array($cb, $obj);
        }
        
        return $obj;
    }
    
    private static function array_merge_keys($ray1, $ray2) {
        $keys = array_merge(array_keys($ray1), array_keys($ray2));
        $vals = array_merge($ray1, $ray2);
        return array_combine($keys, $vals);
    }
    
    public static function GET($req, $opt = array(), $cb = null) {
        if(!is_array($req) && !is_object($req)) throw new Exception('Invalid first argument, expect an array insted');
        if(!array_key_exists('url', $req) || !filter_var($req['url'], FILTER_VALIDATE_URL)) throw new Exception("Invalid URL provide, Curl::GET");

        self::register(self::array_merge_keys(array(
            CURLOPT_URL => $req['url'].((array_key_exists('data', $req)) ? ('?'.http_build_query($req['data'])) : ''),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1',
            CURLOPT_HTTPGET => 1
            ), $opt
        ), $curl);
        
        
        $obj = array();
        try{
            $obj = array(
                'response' => self::parser()->parse(curl_exec($curl), ((array_key_exists('type', $req)) ? $req['type'] : 'json')),
                'info' => curl_getinfo($curl)
            );
        } catch (Exception $ex) {
            curl_close($curl);
            if(array_key_exists('error', $req) && is_callable($req['error'])) {
                call_user_func_array($req['success'], $obj);
            }
            return;
        }
        
        if(curl_errno($curl)) {
            echo curl_error($curl);
        }
        
        curl_close($curl);
        
        if(array_key_exists('success', $req) && is_callable($req['success'])) {
            call_user_func_array($req['success'], $obj);
        }
        
        if(is_callable($cb)) {
            call_user_func_array($cb, $obj);
        }
        
        return (object) $obj;
        
    }
    
}
