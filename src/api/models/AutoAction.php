<?php
use MyCLabs\Enum\Enum;

class ActionTrigger extends Enum {
    const MoveToColumn = 1;
    const AssignedToUser = 2;
    const SetToCategory = 3;
    const PointsChanged = 4;
}

class ActionType extends Enum {
    const SetColor = 1;
    const SetCategory = 2;
    const SetAssignee = 3;
    const ClearDueDate = 4;
    const UseBaseColor = 5;
}

class AutoAction extends BaseModel {
    public $id = 0;
    public $board_id = 0;
    public $trigger;
    public $source_id = 0; // ID of the column etc. which triggers the action
    public $type;
    public $change_to = ''; // Whatever the target of the action changes to

    public function __construct($container, $id = 0) {
        parent::__construct('auto_action', $id, $container);

        $this->trigger = new ActionTrigger(ActionTrigger::MoveToColumn);
        $this->type = new ActionType(ActionType::SetColor);

        $this->loadFromBean($this->bean);
    }

    public function updateBean() {
        $bean = $this->bean;

        $bean->board_id = (string) $this->board_id;
        $bean->trigger = $this->trigger->getValue();
        $bean->source_id = (string) $this->source_id;
        $bean->type = $this->type->getValue();
        $bean->change_to = $this->change_to;
    }

    public function loadFromBean($bean) {
        if (!isset($bean->id)) {
            $this->is_valid = false;

            return;
        }

        if ($bean->id === 0) {
            return;
        }

        $this->is_valid = true;
        $this->loadPropertiesFrom($bean);
    }

    public function loadFromJson($json) {
        $obj = json_decode($json);

        if (!isset($obj->id)) {
            $this->is_valid = false;

            return;
        }

        $this->is_valid = true;
        $this->loadPropertiesFrom($obj);
    }

    private function loadPropertiesFrom($obj) {
        try {
            $this->id = (int) $obj->id;
            $this->board_id = (int) $obj->board_id;
            $this->trigger = new ActionTrigger((int) $obj->trigger);
            $this->source_id = (int) $obj->source_id;
            $this->type = new ActionType((int) $obj->type);
            $this->change_to = $obj->change_to;
        } catch (Exception $ex) {
            $this->is_valid = false;
        }
    }
}

