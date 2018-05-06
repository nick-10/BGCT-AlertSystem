<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// classes

class alert_info {
    public $member_id;
    public $member_name;
    public $room_id;
    public $room_name;

    function get_alert_contents() {
        $file = fopen("mainalert.txt", "r");
        while(!feof($file)) {
            $line =  explode("$$$",fgets($file));
            $this->member_id    = $line[0];
            $this->member_name  = $line[1];
            $this->room_id      = $line[2];
            $this->room_name    = $line[3];
        }
        fclose($file);
    }

    function output_alert() {
        $title = "<div class=\"name_zone\">" . $this->member_name . "</div>";
        $message = "<div class=\"message_zone\">Go to: " . $this->room_name . "</div>";
        $hidden_info_1 = "<div id=\"hidden_member_name\" style='display:none'>" . $this->member_name . "</div>";
        $hidden_info_2 = "<div id=\"hidden_member_id\" style='display:none'>" . $this->member_id . "</div>";
        $hidden_info_3 = "<div id=\"hidden_room_id\" style='display:none'>" . $this->room_id . "</div>";
        $hidden_info_4 = "<div id=\"hidden_room_name\" style='display:none'>" . $this->room_name . "</div>";
        print $title . $message . $hidden_info_1 . $hidden_info_2 . $hidden_info_3 . $hidden_info_4;
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// functions

/////////////////////////////////////////////////////////////////////////////////////
// get rooms

function get_rooms_for_main() {
    if(file_exists("rooms.csv")) {
        $file = fopen("rooms.csv", "r");
        while(!feof($file)) {
            $line = explode(",", fgets($file));
            $id     = $line[0];
            $name   = $line[1];
            print "<div class=\"room_button\" id=\"" . $id ."\" onclick=\"button_room_selected('" . $id ."')\">" . $name ."</div>";
        }
    }
    else {
        print "rooms.csv is missing! oh no!";
    }
}

/////////////////////////////////////////////////////////////////////////////////////
// check for alert

function check_alert() {
    if(file_exists("mainalert.txt")) {
        $alert_contents = new alert_info();
        $alert_contents->get_alert_contents();
        $alert_contents->output_alert();
    }
    else {
        print "<div class=\"name_zone\">Nooooooo Alert! :D</div>";
        print "<div id=\"hidden_member_name\" style='display:none'></div>";
        print "<div id=\"hidden_member_id\" style='display:none'></div>";
        print "<div id=\"hidden_room_id\" style='display:none'></div>";
        print "<div id=\"hidden_room_name\" style='display:none'></div>";
    }
}

/////////////////////////////////////////////////////////////////////////////////////
// Buttons

// Helper
function drop_file($file_name, $alert) {
    $file = fopen($file_name, "w");
    fclose($file);
//    print "RESPONSE SENT - " . $alert ." - I'LL STOP BOTHERING YOU NOW..";
}

// On the way button clicked
function button_on_the_way_clicked($id) {
    button_response_cancelled_clicked($id);
    $file_name = "otw_" . $id . ".txt";
    drop_file($file_name, "ON THE WAY");
}

// Not here clicked
function button_not_here_clicked($id) {
    button_response_cancelled_clicked($id);
    $file_name = "nh_" . $id . ".txt";
    drop_file($file_name, "NOT HERE");
}

// Response Cancelled Clicked
function button_response_cancelled_clicked($id) {
    $nh_file = "nh_" . $id . ".txt";
    $otw_file = "otw_" . $id . ".txt";
    if(file_exists($nh_file)) { unlink($nh_file); }
    if(file_exists($otw_file)) { unlink($otw_file); }
    print "";
}

/////////////////////////////////////////////////////////////////////////////////////
// check for response

function what_exsists_output($which) {
    print "<div id='response_info'>$which</div>";
}

function check_for_response($id) {
    $nh_file = "nh_" . $id . ".txt";
    $otw_file = "otw_" . $id . ".txt";
    if(file_exists($nh_file)) { what_exsists_output("nh"); }
    else if(file_exists($otw_file)) { what_exsists_output("otw"); }
    else {
        if(file_exists("mainalert.txt")) { what_exsists_output("none"); }
        else {what_exsists_output("no_alert"); }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// orders

$orders = $_GET['orders'];

if($orders == "get_rooms") { get_rooms_for_main(); }
if($orders == "check_alert") { check_alert(); }
if($orders == "check_for_response") {
    $id = $_GET['room_id'];
    check_for_response($id);
}
if($orders == "button_on_the_way_clicked") {
    $id = $_GET['room_id'];
    button_on_the_way_clicked($id);
}
if($orders == "button_not_here_clicked") {
    $id = $_GET['room_id'];
    button_not_here_clicked($id);
}
if($orders == "button_response_cancelled_clicked") {
    $id = $_GET['room_id'];
    button_response_cancelled_clicked($id);
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///// get rooms
//function return_rooms() {
//    if(file_exists("rooms.csv")) {
//        $file = fopen("rooms.csv", "r");
//        while(!feof($file)) {
//            $line = explode(",", fgets($file));
//            $id = $line[0];
//            $name = $line[1];
//            print "<div class=\"room_button\" id=\"" . $id ."\" onclick=\"room_selected('" . $id ."')\">" . $name ."</div>";
//        }
//    }
//    else {
//        print "rooms.csv is missing! oh no!";
//    }
//}
//
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///// get alert
//
//function get_alert_contents() {
//    $file = fopen("mainalert.txt", "r");
//    $new_alert_info = new alert_info();
//    while(!feof($file)) {
//        $line =  explode("$$$",fgets($file));
//        $new_alert_info->member_id = $line[0];
//        $new_alert_info->member_name = $line[1];
//        $new_alert_info->room_id = $line[2];
//        $new_alert_info->room_name = $line[3];
//    }
//    fclose($file);
//
//    return $new_alert_info;
//}
//
//function update_main_page_alert_contents() {
//    $alert_contents = get_alert_contents();
//    print "<div class=\"name_zone\" id='" . $alert_contents->member_id ."'>" . $alert_contents->member_name ."</div>";
//    print "<div class=\"message_zone\" id='" . $alert_contents->room_id . "'>Got to the: <span id='cur_room_name_full'>" . $alert_contents->room_name . "</span></div>";
//}
//
//function check_for_alert() {
//    if(file_exists("mainalert.txt")) {
//        update_main_page_alert_contents();
//    }
//    else {
//        print "<div class=\"name_zone\">Noooo Alert!</div>";
//    }
//}
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///// drop_file_tool
//
//function drop_file($file_name, $alert) {
//    $file = fopen($file_name, "w");
//    fclose($file);
//    print "RESPONSE SENT - " . $alert ." - I'LL STOP BOTHERING YOU NOW..";
//}
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///// Check for alert already dropped
//
//function check_for_already_dropped_response($room_id) {
//    $otw = "otw_" . $room_id . ".txt";
//    $nh = "nh_" . $room_id . ".txt";
//
//    if(file_exists($otw)) {
//        print "RESPONSE SENT: ON THE WAY";
//    }
//    else if(file_exists($nh)) {
//        print "RESPONSE SENT: NOT HERE";
//    }
//}
//
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///// ordders
//$orders = $_GET['orders'];
//
//if($orders == "get_rooms") { return_rooms(); }
//if($orders == "check_for_alert") { check_for_alert(); }
//if($orders == "on_the_way_click") {
//    $room_id = $_GET['room_id'];
//    drop_file("otw_" . $room_id . ".txt", "ON THE WAY!");
//}
//if($orders == "not_here_clicked") {
//    $room_id = $_GET['room_id'];
//    drop_file("nh_" . $room_id . ".txt", "NOT HERE");
//}
//if($orders == "check_for_already_dropped_response") {
//    $room_id = $_GET['room_id'];
//    check_for_already_dropped_response($room_id);
//}






























































