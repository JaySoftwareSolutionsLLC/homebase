<?php

class HabitMetric {

    // Properties

    // Methods

    // Constructor
    public function __construct($habit_metric_id, $metric_name, $habit_name, $habit_id, $influence, $freq_str, $habit_min_to_comp, $effective_datetime, $expire_datetime) {
        $this->habit_metric_id = $habit_metric_id; // Should this be broken out into its own Metric class?
        $this->metric_name = $metric_name; // Should this be broken out into its own Metric class?
        $this->habit_name = $habit_name; // Should this be broken out into its own Habit class?
        $this->habit_id = $habit_id;
        $this->habit_min_to_comp = $habit_min_to_comp;
        $this->influence = $influence;
        $this->freq_str = $freq_str;
        if (!empty($effective_datetime)) {
            $this->effective_datetime = new DateTime($effective_datetime);
        } else {
            $this->effective_datetime = null;
        }
        if (!empty($expire_datetime)) {
            $this->expire_datetime = new DateTime($expire_datetime);
        } else {
            $this->expire_datetime = null;
        }
    }

}

?>