<?php
class Column extends BaseModel {
    public $id = 0;
    public $name = '';
    public $position = 0;
    public $tasks = []; // Task model array

    public function __construct($container, $id = 0, $internal = false) {
        parent::__construct('column', $id, $container);

        if ($internal) {
            return;
        }

        $this->loadFromBean($this->bean);
    }

    public static function fromJson($container, $json) {
        $instance = new self($container, 0, true);
        $instance->loadFromJson($container, $json);

        return $instance;
    }

    public function updateBean() {
    }

    public function loadFromBean($container, $bean) {
    }

    public function loadFromJson($container, $json) {
    }
}
