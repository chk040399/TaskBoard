<?php
class ApiJson {
    public $status = 'failure';
    public $data = [];
    public $alerts = [];

    function setSuccess() {
        $this->status = 'success';
    }

    function setFailure() {
        $this->status = 'failure';
    }

    function addData($obj) {
        $this->data[] = $obj;
    }

    function addAlert($type, $text) {
        $this->alerts[] = [
            'type' => $type,
            'text' => $text
        ];
    }
}

