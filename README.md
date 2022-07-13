<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public $data = array();
    protected $weatherApiKey = "d575e6dd5ce04866bcb83439221207";
    protected $ipApiKey = "hc6w4pmwk4b95ray";
    protected $futuredays = "14";

    public function __construct() {
        parent::__construct();
        $this->load->model('Weather_model');
    }

    public function index() {

        //set location or postcode
        $data['location'] = 'po130sz';
        $response = $this->getCurrentWeather($data);
        print_r($response);
        // $response = $this->getForecastWeather($data);
    }

    public function getCurrentWeather($data) {
        $data['forecast'] = "current";
        if ($data['location'] == null) {
            $ip = $this->clientIP();
            $data['location'] = $this->getlocationfromIP($ip);
            $data['location'] = str_replace(' ', '_', $data['location']);
            $currentweather = $this->MakeNewWeatherCall($data);
        } else {
            $currentweather = $this->MakeNewWeatherCall($data);
        }
        //$this->checkForecastRecord($currentweather);

        return $currentweather;
    }

    public function getForecastWeather($data) {
        $data['forecast'] = "forecast";
        if ($data['location'] == null) {
            $ip = $this->clientIP();
            $data['location'] = $this->getlocationfromIP($ip);
            $data['location'] = str_replace(' ', '_', $data['location']);
            $currentweather = $this->MakeNewWeatherCall($data);
        } else {
            $currentweather = $this->MakeNewWeatherCall($data);
        }
        //$this->checkForecastRecord($currentweather);
        return $currentweather;
    }

    public function checkForecastRecord($currentweather) {
        $response = $this->Weather_model->checkforecast($currentweather->location);
        if ($response->current->condition != $currentweather->current->condition) {
            $this->setalert($currentweather);
        }
    }

    public function setalert($currentweather) {
        $response = $this->Weather_model->checkalert($currentweather->location);
    }

    public function createForecastRecord($data) {
        $this->Weather_model->createWeatherRecord($data);
    }

    public function MakeNewWeatherCall($data) {
        try {
            if (isset($data['forecast'])) {
                if ($data['forecast'] == 'current') {
                    $weatherApiUrl = "http://api.weatherapi.com/v1/current.json?key=" . $this->weatherApiKey . "&q=" . $data['location'] . "&aqi=no";
                } else if ($data['forecast'] == 'forecast') {
                    $weatherApiUrl = "http://api.weatherapi.com/v1/forecast.json?key=" . $this->weatherApiKey . "&q=" . $data['location'] . "&aqi=no&days=14";
                }
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $weatherApiUrl);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = (json_decode($response, true));
            return $response;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function getlocationfromIP($ip) {
        try {
            $details = json_decode(file_get_contents("https://api.ipregistry.co/{$ip}?key=" . $this->ipApiKey));
            return $details->location->city;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function clientIP() {
        try {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $address = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $address = $_SERVER['REMOTE_ADDR'];
            }
            return $address;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
