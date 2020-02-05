<?php

    //---INCLUDE RESOURCES--------------------------------------------------------------
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    //---CONNECT TO DATABASE------------------------------------------------------------
    $conn = connect_to_db();

    //---Initialize variables-----------------------------------------------------------
    $dt_start = new DateTime('first day of january this year');
    // $dt_start = new DateTime('-1 week');
    // $dt_start = new DateTime('monday this week');
    $dt_end = new DateTime('sunday this week');
    class HabitMetric {
        public function __construct($metric_name, $habit_name, $habit_id, $influence, $freq_str, $habit_min_to_comp) {
            $this->metric_name = $metric_name; // Should this be broken out into its own Metric class?
            $this->habit_name = $habit_name; // Should this be broken out into its own Habit class?
            $this->habit_id = $habit_id;
            $this->habit_min_to_comp = $habit_min_to_comp;
            $this->influence = $influence;
            $this->freq_str = $freq_str;
        }
    }
    class HabitLog {
        public function __construct($habit_id, $datetime_str, $status) {
            $this->habit_id = $habit_id; // May not be necessary
            $this->datetime = new DateTime($datetime_str);
            $this->date = $this->datetime->format('Y-m-d');
            $this->status = $status;
        }
    }
    function return_dates_match($datetime1, $datetime2) {
        return $datetime1->format('Y-m-d') == $datetime2->format('Y-m-d');
    }
    function return_dates_between($datetime1, $datetime2, $format = 'Y-m-d', $datetime1_inclusive = true, $datetime2_inclusive = true) {
        $dates = array();
        if (!$datetime1_inclusive) {
            $datetime1->modify('+1 day');
        }
        if ($datetime2_inclusive) {
            $datetime2->modify('+1 day');
        }
        $interval = new DateInterval('P1D'); 
        $period = new DatePeriod($datetime1, $interval, $datetime2); 
    
        foreach($period as $date) {                  
            $dates[] = $date->format($format);  
        } 
    
        return $dates;
    }
    function return_scheduled_time_estimate($date, $habit_metrics, $habit_logs) {
        $minutes = 0;
        foreach($habit_logs as $hl) {
            if ($hl->status == 'Started' && $hl->date == $date) {
                foreach($habit_metrics as $hm) {
                    if ($hm->habit_id == $hl->habit_id) {
                        $minutes += $hm->habit_min_to_comp;
                        break;
                    }
                }
            }
        }
        return $minutes;
    }
    function return_influence_points($date, $habit_metrics, $habit_logs, $status = 'Completed') {
        $inf_pts = 0;
        foreach($habit_logs as $hl) {
            if ($hl->status == $status && $hl->date == $date) {
                foreach($habit_metrics as $hm) {
                    if ($hm->habit_id == $hl->habit_id) {
                        $inf_pts += $hm->influence;
                        break;
                    }
                }
            }
        }
        return $inf_pts;
    }
    $dates = return_dates_between($dt_start, $dt_end);

    // var_dump(return_dates_between($dt_start, $dt_end));

    // Retrieve all habit metrics
    $habit_metrics = array();
    $sql_habit_metrics = "  SELECT 	m.name
                                    ,m.id
                                    ,h.name
                                    ,h.id
                                    ,hm.influence
                                    ,h.minutes_to_complete
                                    ,CONCAT(hm.frequency_type, ' ', hm.frequency_int, '/', hm.frequency_window) AS 'freqstr'
                                    
                                FROM `personal_wellness_habit_metric` AS hm
                                INNER JOIN personal_wellness_habits AS h
                                ON (hm.habit_id = h.id)
                                INNER JOIN personal_wellness_metrics AS m
                                ON (hm.metric_id = m.id)

                            WHERE 	hm.effective_datetime < ?
                            	AND (hm.expire_datetime IS NULL OR hm.expire_datetime > ?)

                            ORDER BY hm.influence DESC ";

    $stmt = $conn->prepare($sql_habit_metrics);
    $stmt->bind_param("ss", $dt_end->format('Y-m-d'), $dt_start->format('Y-m-d'));
    $stmt->execute();
    $stmt->bind_result($metric_name, $metric_id, $habit_name, $habit_id, $influence, $minutes_to_complete, $freq_str);
    while ($stmt->fetch()) {
        $habit_metrics[] = new HabitMetric($metric_name, $habit_name, $habit_id, $influence, $freq_str, $minutes_to_complete);
    }
    $stmt->close();
    // var_dump($habit_metrics);


    // Retrieve all habit logs
    $habit_logs = array();
    $sql_habit_logs = " SELECT habit_id, datetime, status 
                        FROM `personal_wellness_habit_logs`
                        WHERE 	DATE(datetime) >= ?
                            AND DATE(datetime) <= ? ";
    $stmt = $conn->prepare($sql_habit_logs);

    $stmt->bind_param("ss", $dt_start->format('Y-m-d'), $dt_end->format('Y-m-d'));
    $stmt->execute();
    $stmt->bind_result($habit_id, $datetime, $status);
    while ($stmt->fetch()) {
        $habit_logs[] = new HabitLog($habit_id, $datetime, $status);
    }
    $stmt->close();
    // var_dump($habit_logs);

?>
<link rel="stylesheet" href="/homebase/sandbox.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

<table id="datatable" class='stripe compact row-border hover results' style="width:100%">
    <thead>
        <tr>
            <th>Metric</th>
            <th>Habit</th>
            <th>Influence</th>
            <!-- <th>Time</th> -->
            <th>Target</th>
            <?php foreach($dates as $d) {
                $dt = new DateTime($d);
                $day_of_week = $dt->format('l');
                echo "<th>$d<br/>($day_of_week)</th>";
            } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($habit_metrics as $hm) {
            $habit_metric_row = "
            <tr>
                <td>" . $hm->metric_name . "</td>
                <td>$hm->habit_name($hm->habit_id)</td>
                <td>" . $hm->influence . "</td>
                <td>" . $hm->freq_str . "</td>";
            foreach($dates as $d) {
                $habit_metric_row .= "<td data-date='$d' data-habit-id='$hm->habit_id' data-habit-name='$hm->habit_name' class='habit-date'>"; // May need to move this down to conditionally check if requirements were met after looping through logs
                foreach($habit_logs as $hl) {
                    if ($hl->habit_id == $hm->habit_id && $hl->date == $d) {
                        switch ($hl->status) {
                            case 'Completed':
                                $habit_metric_row .= '<i class="fas fa-check"></i>';
                                break;

                            case 'Started':
                                $habit_metric_row .= '<i class="fas fa-hourglass-half"></i>';
                                break;
                            
                            default:
                                break;
                        }
                    }
                }
                $habit_metric_row .= "</td>";
            }
            
            $habit_metric_row .= "</tr> ";
            echo $habit_metric_row;
        } ?>

        <!--
        <tr>
            <td>Sugar Consumption</td>
            <td><i class="fas fa-fire"></i>&nbsp;Log Consumption&nbsp;<i class="fas fa-fire"></i></td>
            <td>10</td>
            <td>10m</td>
            <td>=1x/day</td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td><i class="fas fa-hourglass-half"></i></td>
        </tr>
        <tr>
            <td>Sugar Consumption</td>
            <td>Blood Work</td>
            <td>7</td>
            <td>3h 0m</td>
            <td>&GreaterEqual;2x/year</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Photo Assessment</td>
            <td><i class="fas fa-fire"></i>&nbsp;Log Consumption&nbsp;<i class="fas fa-fire"></i></td>
            <td>7</td>
            <td>10m</td>
            <td>=1x/day</td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td><i class="fas fa-hourglass-half"></i></td>
        </tr>
        <tr>
            <td>Photo Assessment</td>
            <td class='weekly-requirement-met'>Time Restricted Eating</td>
            <td>7</td>
            <td>N/A</td>
            <td>&GreaterEqual;5x/week</td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td><i class="fas fa-times"></i></td>
            <td><i class="fas fa-times"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='weekly-requirement-met'><i class="fas fa-check"></i></td>
            <td class='weekly-requirement-met'></td>
        </tr>
        <tr>
            <td>Photo Assessment</td>
            <td>Prolonged Fasting</td>
            <td>9</td>
            <td>N/A</td>
            <td>=1x/month</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Photo Assessment</td>
            <td>Scalp Aromatherapy</td>
            <td>9</td>
            <td>3m</td>
            <td>&GreaterEqual;1x/day</td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td><i class="fas fa-times"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td class='daily-requirement-met'><i class="fas fa-check"></i></td>
            <td></td>
        </tr>
        <tr>
            <td>GMAT Score</td>
            <td>GMAT Study</td>
            <td>10</td>
            <td>60</td>
            <td>&GreaterEqual;12x/week</td>
            <td><i class="fas fa-check"></i><i class="fas fa-check"></i></td>
            <td><i class="fas fa-check"></i></td>
            <td><i class="fas fa-check"></i><i class="fas fa-check"></i></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr> -->
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <th></th>
            <?php foreach($dates as $d) {
                echo "<td class='numeric'>" . return_influence_points($d, $habit_metrics, $habit_logs) . " IP<br/>";
                echo "(" . return_scheduled_time_estimate($d, $habit_metrics, $habit_logs) . "m remaining) [" . return_influence_points($d, $habit_metrics, $habit_logs, 'Started') . "]</td>";
            } ?>
            <!-- <th>13h 31m</th> -->
            <!-- <th>2h 23m</th> -->
            <!-- <th>1h 23m</th> -->
            <!-- <th>2h 23m</th> -->
            <!-- <th>10m</th> -->
            <!-- <th>23m</th> -->
            <!-- <th>13m</th> -->
            <!-- <th>N/A</th> -->
        </tr>
    </tfoot>
</table>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js""></script>
<script>
$(document).ready(function() {
    // Generate Table 
    var groupColumn = 0;
    var table = $('#datatable').DataTable({
        "columnDefs": [
            { "visible": false, "targets": groupColumn }
        ],
        "order": [[ groupColumn, 'asc' ], [2, 'desc']],
        "displayLength": 25,
        "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
 
            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="<?= 3 + count($dates); ?>" style="text-align: left; font-size: 1.5rem; font-weight: 900;">'+group+'</td></tr>'
                    );
 
                    last = group;
                }
            } );
        }
    } );
 
    // Order by the grouping
    $('#datatable tbody').on( 'click', 'tr.group', function () {
        var currentOrder = table.order()[0];
        if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
            table.order( [ groupColumn, 'desc' ] ).draw();
        }
        else {
            table.order( [ groupColumn, 'asc' ] ).draw();
        }
    } );

    function listenForHabitDblClickEvents() {
        console.log('Start: listenForHabitDblClickEvents');
        $('td.habit-date').on('dblclick', function (e) {
            // Shift + Dbl click creates a 'Started' event
            let tdEl = $(this);
            let status = e.shiftKey ? 'Started' : 'Completed';
            // confirm with user
            let habitId = $(this).attr('data-habit-id');
            let habitName = $(this).attr('data-habit-name');
            let date = $(this).attr('data-date');
            let verified = confirm(`Are you sure you would like to make a ${status} ${habitName} event for ${date}?`);
            if (verified) {
                // update db
                $.ajax({
                    type: "POST",
                    url: '/homebase/resources/ajax/insert_habit_log.php',
                    dataType: 'JSON',
                    data: {
                        'habit_id' : habitId,
                        'status': status,
                        'datetime': date,
                    }
                }).done(function (responseJSON) {
                    if (responseJSON.success) {
                        switch (status) {
                            case 'Started':
                                tdEl.append('<i class="fas fa-hourglass-half"></i>');
                                break;
                            case 'Completed':
                                tdEl.append('<i class="fas fa-check"></i>');
                                break;
                        
                            default:
                                break;
                        }
                    }
                })
            }
        })
        console.log('Finish: listenForHabitDblClickEvents');
    }
    listenForHabitDblClickEvents();


} );
</script>