<?php
class Wellness_Habit
{
    // Meta info
    // public $id;
    public $name;
    // public $technique;
    // public $frequency;
    // public $threshold;
    // public $caution_min;
    // public $mr_value;
    // public $active;


    public $habits; // array to house each habit object associated with this metric

    // Constructor function
    function __construct($name)
    {
        // $this->id = $id;
        $this->name = $name;
        // $this->technique = $technique;
        // $this->frequency = $frequency;
        // $this->threshold = $threshold;
        // $this->mr_value = $mr_value;
        // $this->caution_min = $caution_min;
        // if (is_null($threshold) || $mr_dimension_rating < $threshold) {
        //     $this->active = true;
        // } else {
        //     $this->active = false;
        // }
        // $this->habits = array();
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
}
