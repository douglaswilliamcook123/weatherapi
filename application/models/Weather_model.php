<?php

class Weather_model extends CI_Model {

    function __construct() {
        parent::__construct();
        //$this->load->database();
    }

    public function createWeatherRecord($data) {
        $this->db->trans_start();
        $this->db->insert('Weather', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $last_id;
    }

    public function checkforecast($location) {
        $this->db->trans_start();
        $this->db->select("*");
        $this->db->from("Weather");
        $this->db->where('location', $location);
        $query = $this->db->get();
        $data = $query->row_array();
        $this->db->trans_complete();
        return $result;
    }

    public function deleteWeatherRecord($id) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->delete('Weather');
        return $last_id;
    }

    public function checkalert($location) {
        $this->db->trans_start();
        $this->db->select("*");
        $this->db->from("alerts");
        $this->db->where('location', $location);
        $query = $this->db->get();
        $data = $query->row_array();
        $this->db->trans_complete();
        return $result;
    }

    public function createAlert($data) {
        $this->db->trans_start();
        $this->db->insert('alerts', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $last_id;
    }

    public function deletealert($id) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->delete('alerts');
        $this->db->trans_complete();
    }

}
