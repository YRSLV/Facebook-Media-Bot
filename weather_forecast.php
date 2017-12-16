<?php

//units=... For temperature in Celsius use units=metric

class weather_wrapper {

  public $url;
  public $contents;

  function __construct($c) {
    $this->url = "http://api.openweathermap.org/data/2.5/weather?q=" . trim($c) . "&lang=en&units=metric&APPID=specified_api_key";
    $this->contents = file_get_contents($this->url);
  }

  function get_forecast() {
    if (!empty($this->contents)) {

      $wthr=json_decode($this->contents);

      $temp_max=$wthr->main->temp_max;
      $temp_min=$wthr->main->temp_min;
      $pres=$wthr->main->pressure;
      $humid=$wthr->main->humidity;
      $w_speed=$wthr->wind->speed;
      $w_degree=$wthr->wind->deg;
      $clouds=$wthr->clouds->all;
      //(almost) magic :D to get current date and time
      $today = date("F j, Y, g:i a");
      $cityname = $wthr->name;

      /*$info = [
        "date"=>$today,
        "t_max"=>$temp_max,
        "t_min"=>$temp_min,
        "pressure"=>$pres,
        "humidity"=>$humid,
        "wind_speed"=>$w_speed,
        "wind_degree"=>$w_degree,
        "cloud_amount"=>$clouds
      ];*/

      $ans = [
        "Weather forecast for today, " . $today . " in " . $cityname . ": ",
        "Maximum temperature ☀: " . $temp_max . "°C",
        "Minimum temperature ❄: " . $temp_min . "°C",
        "Cloudiness ☁ : " . $clouds . "%",
        "Humidity 🌢: " . $humid . "%",
        "Pressure: " . $pres . " hPA",
        "Wind speed is: " . $w_speed . " m/s",
        "Wind degree ⮹ is: " . $w_degree . " degrees"
      ];

    } else {
      $ans = ["Request failed! Check your city at openweathermap.org"];
    }
    return $ans;
  }
}

?>
