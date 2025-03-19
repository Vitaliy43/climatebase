<?php

class ConvertorData extends WeatherData {

    public $db = 'new_climate2';
    public $point;

    function __construct($table,$station,$connect) {
		parent::__construct($table,$station,$connect);
        $this->point = $this->setPoint($station);

        if (!$this->point) {
            echo 'У станции '.$station.' нет записи в таблице new_points';
            exit;
        }
        if ($this->from_year_precip != $this->point['from_year']) {
            $this->from_year_precip = $this->point['rom_year'];
        }

	}

    public function setPoint($station) {
        $sql = "SELECT * FROM {$this->db}.new_points WHERE station = $station";
        $result=mysqli_query($this->connect,$sql);
        $row = $result->fetch_assoc();
        if (!$row) return false;
        return $row;
    }
}