<?php

class CpCountryMobileIdModel extends Model {
    protected $tableName = "cpcountrymobileid";
    protected $fields = array('cpcountrymobileid_id', 'countryname','mobilecode');
    
    public function getlist() {
        return $this->select();
    }
}
