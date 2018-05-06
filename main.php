<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="=UTF-8">
    <title>Main Page</title>
    <link rel="stylesheet" href="main_styles.css?id=4" type="text/css">
</head>
<body onload="first_load()">
    <?php include "nav.php"; create_nav_bar() ?>
    <div id="room_selection"></div>
    <div id="main_box">
        <div id="alert_contents_zone"></div>
        <div id="main_error_message"></div>
        <div id="button_zone">
            <div id="button_on_the_way" class="response_button" onclick="button_on_the_way()">On the Way</div>
            <div id="button_not_here" class="response_button" onclick="button_not_here()">Not Here</div>
            <div id="button_cancel_response" class="response_button" onclick="button_cancel_response()">Cancel Response</div>
        </div>
    </div>
    <div id="hidden_zone" style="display: none;">
        "<div id='response_info'></div>"
    </div>
</body>
<script>
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Global Variables
    var cur_room_id = "";
    var cur_room_name = "";
    var cur_member_id = "";
    var cur_member_name = "";

    var cur_response_code = "";

    var selected_room_id = "";
    var selected_room_name = "";


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // interval

    setInterval(function() {
        check_alert();
        update_global_variables();
        check_for_already_dropped_response();
    }, 2000);

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Initial Code

    function get_rooms() {
        update_page("orders=get_rooms", "room_selection")
    }

    function first_load() {
        get_rooms();
        check_alert();
        update_global_variables();
        check_for_already_dropped_response();
    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Interactive Code

    /////////////////////////////////////////////////////////////////////////////////////
    // check for alert

    function check_alert() {
        var cur_alert = document.getElementById("alert_contents_zone").innerHTML;
        update_page("orders=check_alert", "alert_contents_zone");
        if(document.getElementById("alert_contents_zone").innerHTML !== cur_alert) {
            document.body.style.backgroundColor = "white";
            document.getElementById("main_error_message").innerText = "";
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // update global variables

    function update_global_variables() {
        cur_room_id = document.getElementById("hidden_room_id").innerText;
        cur_room_name = document.getElementById("hidden_room_name").innerText;
        cur_member_id = document.getElementById("hidden_member_id").innerText;
        cur_member_name = document.getElementById("hidden_member_name").innerText;
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Play Alert Sound!

    function play_sound(info) {
        if(info === "alert") {
            new Audio('ding.mp3').play();
        }
        if(info === "response_hit") {
            new Audio('error.mp3').play();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // check for response

    function check_for_already_dropped_response() {
        if(check_room_selected()) {
            update_page("orders=check_for_response&room_id=" + selected_room_id, "hidden_zone");
            $response_info = document.getElementById("response_info").innerText;
            if($response_info === "nh") {
                document.body.style.backgroundColor = "orange";
                make_button_unselected("button_on_the_way");
                make_button_selected("button_not_here");
                make_button_unselected("button_cancel_response");
                document.getElementById("main_error_message").innerText = "RESPONSE MARKED: NOT HERE";
            }
            else if($response_info === "otw") {
                document.body.style.backgroundColor = "green";
                make_button_selected("button_on_the_way");
                make_button_unselected("button_not_here");
                make_button_unselected("button_cancel_response");
                document.getElementById("main_error_message").innerText = "RESPONSE MARKED: ON THE WAY";
            }
            else if($response_info === "none") {
                document.body.style.backgroundColor = "red";
                make_button_unselected("button_on_the_way");
                make_button_unselected("button_not_here");
                make_button_unselected("button_cancel_response");
                document.getElementById("main_error_message").innerText = "NO RESPONSE YET!!!!";
                play_sound("alert");
            }
            else if($response_info === "no_alert") {
                document.body.style.backgroundColor = "white";
                make_button_unselected("button_on_the_way");
                make_button_unselected("button_not_here");
                make_button_unselected("button_cancel_response");
                document.getElementById("main_error_message").innerText = "All quite here and stuff";
            }
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // update colors

    function make_button_unselected(id) {
        document.getElementById(id).style.backgroundColor = "whitesmoke";
        document.getElementById(id).style.borderColor = "lightgray";
        document.getElementById(id).style.color = "black";
    }

    function make_button_selected(id) {
        document.getElementById(id).style.backgroundColor = "orangered";
        document.getElementById(id).style.borderColor = "orangered";
        document.getElementById(id).style.color = "white";
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // buttons_clicked

    function button_room_selected(id) {
        if(selected_room_id !== "") {
            make_button_unselected(selected_room_id);
        }
        selected_room_id = id;
        selected_room_name = document.getElementById(id).innerText;
        make_button_selected(id);
        check_for_already_dropped_response();
    }

    function check_room_selected() {
        if(selected_room_id !== "") {
            return true;
        }
        else {
            document.getElementById("main_error_message").innerText = "SELECT A ROOM FIRST...";
            return false;
        }
    }

    function button_on_the_way() {
        play_sound("response_hit");
        if(check_room_selected()) {
            document.getElementById("response_info").innerText = "otw";
            update_page("orders=button_on_the_way_clicked&room_id=" + selected_room_id, "main_error_message");
        }
    }

    function button_not_here() {
        play_sound("response_hit");
        if(check_room_selected()) {
            document.getElementById("response_info").innerText = "nh";
            update_page("orders=button_not_here_clicked&room_id=" + selected_room_id, "main_error_message");
        }
    }

    function button_cancel_response() {
        play_sound("response_hit");
        if(check_room_selected()) {
            document.getElementById("response_info").innerText = "none";
            update_page("orders=button_response_cancelled_clicked&room_id=" + selected_room_id, "main_error_message");
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Helper Tools
    function build_xmlhttp() {
        if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
        else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
        return xmlhttp;
    }

    function update_page(url_orders, what_to_update) {
        var xmlhttp = build_xmlhttp();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                document.getElementById(what_to_update).innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "main_backend.php?" + url_orders, true);
        xmlhttp.send();
    }
</script>
</html>