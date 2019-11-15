<?php
    class Wellness_Metric
    {
        // Meta info
        public $id;
        public $name;
        public $technique;
        public $frequency;
        public $threshold;
        public $caution_min;
        public $mr_value;
        public $active;


        public $habits; // array to house each habit object associated with this metric

        // Constructor function
        function __construct($id, $name, $technique, $frequency, $threshold, $mr_value, $caution_min, $mr_dimension_rating)
        {
            $this->id = $id;
            $this->name = $name;
            $this->technique = $technique;
            $this->frequency = $frequency;
            $this->threshold = $threshold;
            $this->mr_value = $mr_value;
            $this->caution_min = $caution_min;
            if (is_null($threshold) || $mr_dimension_rating < $threshold) {
                $this->active = true;
            }
            else {
                $this->active = false;
            }
            $this->habits = array();
        }

        /*
        function return_html() {
            $op = "<h2>$this->mr_rating&nbsp$this->name</h2>";
            foreach($this->metrics AS $m) {
                if ($m->active) {
                    $op .= "<li>$m->name</li>";
                }
            }
            return $op;
        }
        */

        function populate_habits() {
            global $conn;
            // $metrics = array();
            $q = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/queries/retrieve_all_personal_wellness_habits.sql');
            $q .= " WHERE hm.metric_id = $this->id ";
            //return array($q);
            $res = $conn->query($q);
            if ($res->num_rows > 0) { // If there are results then pull them up
                while ($row = $res->fetch_assoc()) {
                    $m = new Wellness_Habit($row['name']);
                    $this->habits[] = $m;
                }
            }
            return;
        }

    }
