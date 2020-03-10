<?php


class dd
{
  public static function outdata($data,$stop=null){
    echo "<pre>Dump:";
    print_r($data);
    echo "</pre>";
    $stop ? exit() : '';
  }
}