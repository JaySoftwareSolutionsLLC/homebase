<?php
    // Calendar is how habits are tracked

    //---INCLUDE RESOURCES--------------------------------------------------------------
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/habits/resources/php/common.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/habits/resources/php/queries.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/habits/resources/classes/HabitMetric.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/habits/resources/classes/HabitLog.php');

    //---CONNECT TO DATABASE------------------------------------------------------------
    $conn = connect_to_db();

    //---Initialize variables-----------------------------------------------------------
    // $dt_start = new DateTime('monday this week');
    $dt_start = new DateTime('2020-05-13');
    // $dt_end = new DateTime('sunday this week');
    $dt_end = new DateTime('sunday next week');
    $today_dt = new DateTime('now');
    
    // All dates between start and end date (inclusive)
    $dates = return_dates_between($dt_start, $dt_end); // def @ /homebase/habits/resources/js/

    // Retrieve all habit-metric relationships
    $habit_metrics = array();

    $stmt = $conn->prepare($sql_habit_metrics);
    $stmt->bind_param("ss", $dt_end->format('Y-m-d'), $dt_start->format('Y-m-d'));
    $stmt->execute();
    $stmt->bind_result($habit_metric_id, $metric_name, $metric_id, $habit_name, $habit_id, $influence, $minutes_to_complete, $freq_str, $effective_datetime, $expire_datetime);
    while ($stmt->fetch()) {
        $habit_metrics[] = new HabitMetric($habit_metric_id, $metric_name, $habit_name, $habit_id, $influence, $freq_str, $minutes_to_complete, $effective_datetime, $expire_datetime);
    }
    $stmt->close();

    // Retrieve all habit logs
    $habit_logs = array();
    $stmt = $conn->prepare($sql_habit_logs);

    $stmt->bind_param("ss", $dt_start->format('Y-m-d'), $dt_end->format('Y-m-d'));
    $stmt->execute();
    $stmt->bind_result($habit_id, $datetime, $status);
    while ($stmt->fetch()) {
        $habit_logs[] = new HabitLog($habit_id, $datetime, $status);
    }
    $stmt->close();

?>
<link rel="stylesheet" href="/homebase/habits/resources/css/calendar.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
<title>Habit Calendar</title>

<table id="datatable" class='stripe compact row-border hover results' style="width:100%">
    <thead>
        <tr>
            <th>Metric</th>
            <th>Habit</th>
            <th>Influence</th>
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
            
            $hm->streak = return_habit_metric_streak($conn, $hm->habit_metric_id, $today_dt);
            $habit_metric_row = "
            <tr>
                <td>" . $hm->metric_name . "&nbsp;<i class='fas fa-edit'></i></td>
                <td style='text-align: left;'>";
            if ($hm->streak == 'hot-streak') {
                $habit_metric_row .= "<i class='fas fa-fire'></i>&nbsp;";
            } elseif ($hm->streak == 'cold-streak') {
                $habit_metric_row .= "<i class='fas fa-snowflake'></i>&nbsp;";
            }
            $habit_metric_row .= $hm->habit_name . "&nbsp;<i class='fas fa-edit'></i>" ;
            
            $habit_metric_row .= "</td>
                <td style='text-align: right;'>" . $hm->influence . "</td>
                <td style='text-align: right;'>" . $hm->freq_str . "</td>";
            foreach($dates as $d) {
                if ($d < $hm->effective_datetime->format('Y-m-d') 
                || (!is_null($hm->expire_datetime) && $hm->expire_datetime->format('Y-m-d') < $d)) {
                    $habit_metric_row .= "<td style='background: hsl(0, 0%, 44%);'></td>";
                    continue;
                } else {
                    $habit_metric_row .= "<td data-date='$d' data-habit-id='$hm->habit_id' data-habit-name='$hm->habit_name' class='habit-date'><div>"; // May need to move this down to conditionally check if requirements were met after looping through logs
                    $habit_metric_row .= "<i class='fas fa-plus-circle log-habit'></i>";
                    $daily_habit_completion_count = 0;
                    $daily_habit_start_count = 0;
                    foreach($habit_logs as $hl) {
                        if ($hl->habit_id == $hm->habit_id && $hl->date == $d) {
                            switch ($hl->status) {
                                case 'Completed':
                                    $daily_habit_completion_count += 1;
                                    break;
                                    
                                    case 'Started':
                                    $daily_habit_start_count += 1;
                                    break;
                                    
                                    default:
                                    break;
                                }
                            }
                        }
                        if ($daily_habit_completion_count > 0) {
                            $habit_metric_row .= "<span class='daily-completions'><i class='fas fa-check'></i>x$daily_habit_completion_count</span>"; // Display qty of completed logs
                        } else {
                            $habit_metric_row .= "<span class='daily-completions'>&nbsp;</span>";
                        }
                        if ($daily_habit_start_count > 0) {
                            $habit_metric_row .= "<span class='daily-starts'><i class='fas fa-hourglass-half'></i>x$daily_habit_start_count</span>"; // Display qty of started logs 
                        } else {
                            $habit_metric_row .= "<span class='daily-starts'>&nbsp;</span>";
                        }
                        if ($daily_habit_start_count) {
                            $habit_metric_row .= "<i class='fas fa-broom clear-habits'></i>";
                        }
                }
                $habit_metric_row .= "</div></td>";
            }
            $habit_metric_row .= "</tr> ";
            echo $habit_metric_row;
        } ?>

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
        "displayLength": 100,
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

    function listenForHabitLog() {
        console.log('Start: listenForHabitLog');
        $('td.habit-date i.log-habit').on('click', function (e) {
            // Shift + click creates a 'Started' event
            let tdEl = $(this).parents('td');
            let status = e.shiftKey ? 'Started' : 'Completed';
            // confirm with user
            let habitId = tdEl.attr('data-habit-id');
            let habitName = tdEl.attr('data-habit-name');
            let date = tdEl.attr('data-date');
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
        console.log('Finish: listenForHabitLog');
    }
    listenForHabitLog();

    function listenForHabitClear() {
        console.log('Start: listenForHabitClear');
        $('td.habit-date i.clear-habits').on('click', function (e) {
            let tdEl = $(this).parents('td');
            let habitId = tdEl.attr('data-habit-id');
            let habitName = tdEl.attr('data-habit-name');
            let date = tdEl.attr('data-date');
            let verified = confirm(`Are you sure you would like to clear all started habits for ${habitName} on ${date}?`);
            if (verified) {
                // update db
                $.ajax({
                    type: "POST",
                    url: '/homebase/resources/ajax/clear-started-habits.php',
                    dataType: 'JSON',
                    data: {
                        'habit_id' : habitId,
                        'date': date,

                    }
                }).done(function (responseJSON) {
                    if (responseJSON.success) {
                        tdEl.empty().html('<div><i class="fas fa-plus-circle log-habit"></i><span class="daily-completions">&nbsp;</span><span class="daily-starts">&nbsp;</span></div>');
                    }
                })
            }
        })
        console.log('Finish: listenForHabitClear');
    }
    listenForHabitClear();


} );
</script>