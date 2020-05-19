<?php

class WellnessDimension {

    // Properties

    // Methods

    // Constructor
    public function __construct($dimension_id, $dimension_name) {
        $this->id = $dimension_id; // Should this be broken out into its own Metric class?
        $this->name = $dimension_name; // Should this be broken out into its own Metric class?
    }

}

?>