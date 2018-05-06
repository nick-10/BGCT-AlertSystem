<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// update_member_search_panel

class member {
    public $id = "";
    public $first_name = "";
    public $last_name = "";
}

function allocate_member($full_line) {
    $line =  explode(",",$full_line);
    $new_mem = new member;
    $new_mem->id = $line[0];
    $new_mem->first_name = $line[1];
    $new_mem->last_name = $line[2];
    return $new_mem;
}

function fetch_members($input) {
    $arr = [];
    $file = fopen("data.csv", "r");
    while(! feof($file)) {
        $full_line = fgets($file);
        if($input == "") {
            $mem = allocate_member($full_line);
            array_push($arr, $mem);
        }
        else {
            if (strpos(strtolower($full_line), strtolower($input)) !== false) {
                $mem = allocate_member($full_line);
                array_push($arr, $mem);
            }
        }
    }
    fclose($file);
    return $arr;
}

function get_members($input) {
    $arr = fetch_members($input);

    for($x = 0; $x < count($arr); ++$x) {
        $first_chunk = "<a class=\"member_card\" id=\"" . $arr[$x]->id ."\"";
        $second_chunk = " href='javascript:member_selected(\"" . $arr[$x]->id . "\")'>";
        $third_chunk = $arr[$x]->first_name . " " . $arr[$x]->last_name;
        $forth_chunk = "</a>";
        $final_chunk = $first_chunk . $second_chunk . $third_chunk . $forth_chunk;

        print $final_chunk;
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// check_for_member

function check_for_member($name) {
    $arr = fetch_members($name);
    for($x = 0; $x < count($arr); ++$x) {
        $arr_name = $arr[$x]->first_name . " " . $arr[$x]->last_name;
        if(strtolower($name) == strtolower($arr_name)) {
            print "ERROR: NAME ALREADY IN DATABASE!";
        }
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// add_member
 function add_member($first_name, $last_name) {
    $size = count(fetch_members("")) + 1;
     $append_file = fopen("data.csv", "a");
     $str_to_write = "\n" . (string)$size . "," . $first_name . "," . $last_name;
     fwrite($append_file, $str_to_write);
     fclose($append_file);
     print "<strong style='color: limegreen'> Member Number #" . $size . " - " . $first_name . " " . $last_name . ". ADDED!</strong>";
 }


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get Rooms Tools

class rooms {
    public $id_name = "";
    public $full_name = "";
}

function get_rooms() {
    $arr = [];
    $file = fopen("rooms.csv", "r");
    while(! feof($file)) {
        $full_line = fgets($file);
        $line =  explode(",",$full_line);
        $new_room = new rooms;
        $new_room->id_name = $line[0];
        $new_room->full_name = $line[1];
        array_push($arr, $new_room);
    }
    fclose($file);

    return $arr;

}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get Rooms for "Alert Progress" Block

function configure_rooms_alert_progress() {
    $arr = get_rooms();
    print "<table>";
    for($index = 0; $index < count($arr); ++$index) {
        print "<tr class='ap_row'>";
        print "<td class='ap_td'>" . $arr[$index]->full_name . "</td>";
        print "<td class='ap_td' id='ap_" . $arr[$index]->id_name . "'><strong style='color:green'>NO ALERT</strong></td>";
        print "</tr>";
    }
    print "</table>";
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get Rooms for "Send Alert" Block

function configure_rooms_send_alert() {
    $arr = get_rooms();

    for($index = 0; $index < count($arr); ++$index) {
        $first_chunk = "<a class=\"sa_room_button\" id=\"sa_" . $arr[$index]->id_name . "\"";
        $second_chunk = " href=\"javascript:room_selected('" . $arr[$index]->id_name . "')\">";
        $third_chunk = $arr[$index]->full_name;
        $forth_chunk = "</a>";
        $full = $first_chunk . $second_chunk . $third_chunk . $forth_chunk;
        print $full;
    }
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Send Alert

function send_alert($person_id, $person_name, $room_id, $room_name) {
    $file = fopen("mainalert.txt", "w");
    $to_write = $person_id . "$$$" . $person_name . "$$$" .  $room_id . "$$$" . $room_name;
    fwrite($file, $to_write);
    fclose($file);
    print "<strong style='color:red;'>ACTIVE ALERT: " . $person_name . " go to the " . $room_name . "</strong>";
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check on Alert

class room_check {
    public $room_id = "";
    public $room_name = "";
    public $room_text_nh = "";
    public $room_txt_otw = "";
}

function create_checker_arr($arr) {
    $checker_arr = [];
    for($index = 0; $index < count($arr); ++$index) {
        $new_room_check = new room_check();
        $new_room_check->room_id = $arr[$index]->id_name;
        $new_room_check->room_name = $arr[$index]->full_name;
        $new_room_check->room_txt_nh = "nh_" . $arr[$index]->id_name . ".txt";
        $new_room_check->room_txt_otw = "otw_" . $arr[$index]->id_name . ".txt";
        array_push($checker_arr, $new_room_check);
    }
    return $checker_arr;
}

function check_on_alert() {
    if(file_exists("mainalert.txt")) {
        $checks_arr = create_checker_arr(get_rooms());
        print "<table>";
        for ($index = 0; $index < count($checks_arr); ++$index) {
            print "<tr class='ap_row'>";
            print "<td class='ap_td'>" . $checks_arr[$index]->room_name . "</td>";
            if (file_exists($checks_arr[$index]->room_txt_nh) || file_exists($checks_arr[$index]->room_txt_otw)) {
                if (file_exists($checks_arr[$index]->room_txt_nh)) {
                    print "<td class='ap_td' id='ap_" . $checks_arr[$index]->room_id . "'><strong style='color:red'>NOT HERE....</strong></td>";
                } else if (file_exists($checks_arr[$index]->room_txt_otw)) {
                    print "<td class='ap_td' id='ap_" . $checks_arr[$index]->room_id . "'><strong style='color:green'>ON THE WAY!</strong></td>";
                }
            } else {
                print "<td class='ap_td' id='ap_" . $checks_arr[$index]->room_id . "'><strong style='color:orange'>WAITING FOR RESPONSE</strong></td>";
            }
            print "</tr>";
        }
        print "</table>";
    }
    else {
        configure_rooms_alert_progress();
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Delete Alerts

function remove_main_alert() {
    if(file_exists("mainalert.txt")) { unlink("mainalert.txt"); }
}

function remove_all_room_alerts() {
    $rooms_arr = get_rooms();
    for($x = 0; $x < count($rooms_arr); ++$x) {
        $room = $rooms_arr[$x]->id_name;
        $nh = "nh_" . $room . ".txt";
        $otw = "otw_" . $room . ".txt";
        if(file_exists($nh)) { unlink($nh); }
        if(file_exists($otw)) { unlink($otw); }
    }
}

function alert_complete($output) {
    remove_main_alert();
    remove_all_room_alerts();
    print $output;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check for already set alert

function check_for_already_set_alert() {
    if(file_exists("mainalert.txt")) {
        include_once "main_backend.php";
        $cur_alert = new alert_info();
        $cur_alert->get_alert_contents();
        print "<span style='color:orange'>CURRENT ALERT: " . $cur_alert->member_name . " - GO TO: " . $cur_alert->room_name ."</span>";
    }
    else {
        print "<span style='color:green'>No Alert Currently!</span>";
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get From frontend

$orders = $_GET['orders'];

if($orders == "get_members") {
    get_members("");
}

if($orders == "get_search_results") {
    $input = $_GET["input"];
    get_members($input);
}

if($orders == "check_for_member") {
    $name = $_GET["name"];
    check_for_member($name);
}

if($orders == "add_member") {
    $first_name = $_GET["first_name"];
    $last_name = $_GET["last_name"];
    add_member($first_name, $last_name);
}

if($orders == "get_rooms_alert_progress") {
    configure_rooms_alert_progress();
}

if($orders == "get_rooms_send_alert") {
    configure_rooms_send_alert();
}

if($orders == "send_alert") {
    send_alert($_GET['person_id'], $_GET['person_name'], $_GET['room_id'], $_GET['room_name']);
}

if($orders == "check_on_alerts") {
    check_on_alert();
}

if($orders == "complete_alert") {
    alert_complete("Alert Removed!");
}

if($orders == "check_for_already_set_alert") {
    check_for_already_set_alert();
}



































