<?php

class HabitLog {

    // Properties

    // Methods

    // Constructor
    public function __construct($habit_id, $datetime_str, $status) {
        $this->habit_id = $habit_id; // May not be necessary
        $this->datetime = new DateTime($datetime_str);
        $this->date = $this->datetime->format('Y-m-d');
        $this->status = $status;
    }
}
?>