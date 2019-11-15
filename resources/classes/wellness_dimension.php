<?php
    class Wellness_Dimension 
    {
        // Meta info
        public $id;
        public $name;
        public $golden_mean;
        public $mr_rating;
        public $metrics; // array to house each metric object associated with this dimension

        // Constructor function
        function __construct($id, $name, $golden_mean, $mr_rating)
        {
            $this->id = $id;
            $this->name = $name;
            $this->golden_mean = $golden_mean;
            $this->mr_rating = $mr_rating ?? 'NULL';
            $this->metrics = array();
        }

        function return_html() {
            $op = "<h2>$this->mr_rating&nbsp$this->name</h2>";
            foreach($this->metrics AS $m) {
                if ($m->active) {
                    $op .= return_timed_goal_progress_bar_html($m->name, $m->name, 0, 100, $m->mr_value, '2019-01-01', '2019-12-31');
                    foreach($m->habits AS $h) {
                        $op .= "<li>$h->name</li>";
                    }
                }
            }
            return $op;
        }

        function populate_metrics() {
            global $conn;
            // $metrics = array();
            $q = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/queries/retrieve_all_personal_wellness_metrics.sql');
            $q .= " WHERE dm.dimension_id = $this->id ";
            //return array($q);
            $res = $conn->query($q);
            if ($res->num_rows > 0) { // If there are results then pull them up
                while ($row = $res->fetch_assoc()) {
                    $m = new Wellness_Metric($row['id'], $row['name'], $row['technique'], $row['frequency'], $row['threshold'], $row['mr_value'], $row['caution_min'], $this->mr_rating);
                    $this->metrics[] = $m;
                }
            }
            return;
        }

    }
    
?>