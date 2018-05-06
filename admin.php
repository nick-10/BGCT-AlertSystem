<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="admin_styles.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300" rel="stylesheet">
</head>
<body onload="first_load_in()">
    <?php include "nav.php"; create_nav_bar() ?>
<div class="main_body">
    <div id="left_panel">
        <div id="block_search_bar" class="admin_block">
            <input type="text" id="search_box" onkeyup="get_search_cards()" placeholder="Search for someone">
        </div>
        <div id="block_member_zone" class="admin_block">
            <div class="block_title">Search for a Member</div>
            <diV class="list_box" id="member_db_panel"></diV>
        </div>
        <div id="block_alert_status" class="admin_block">
            <div class="block_title">Alert Progress</div>
            <div class="list_box">
                <div id="room_zone_alert_progress"></div>
            </div>
        </div>
    </div>
    <div id="right_panel">
        <div id="block_add_member" class="admin_block">
            <div class="block_title">Add a Member</div>
            <input type="text" class="add_mem_input" id="add_mem_first_name" placeholder="First Name">
            <input type="text" class="add_mem_input" id="add_mem_last_name"  placeholder="Last Name">
            <div id="add_mem_error_message" style="color: red"></div>
            <a id="add_member_button" class="block_button" href="javascript:add_member()">Add Member</a>
        </div>
        <div id="block_send_alert" class="admin_block">
            <div class="block_title">Send Alert <strong id="alert_member_title"></strong></div>
            <div id="room_selection_zone_send_alert"></div>
            <div id="send_alert_error_message" style="margin-left: 10px"><strong style="color:orange">Choose a Person and Room!</strong></div>
            <a id="send_alert_button" class="block_button" href="javascript:send_alert()">Send Alert</a>
        </div>
        <div id="block_finish_alert" class="admin_block">
            <div class="block_title">Complete Alert<strong id="alert_member_title"></strong></div>
            <div id="current_alert_message" style="margin-left: 10px">filler</div>
            <a id="complete_alert_button" class="block_button" href="javascript:complete_alert()">Alert Over</a>
        </div>
    </div>
</div>
</body>
<script>

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Global Variables

    var cur_id = "";
    var cur_name = "";
    var cur_room_id = "";
    var cur_room_full_name = "";


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // interval

    setInterval(function() {
        check_if_alert_already_set();
        check_for_alert_response();
    }, 2000);


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Tools

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
        xmlhttp.open("GET", "admin_backend.php?" + url_orders, true);
        xmlhttp.send();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // on load

    function get_members() {
        update_page("orders=get_members", "member_db_panel")
    }

    function allocate_rooms_to_alert_progress() {
        update_page("orders=get_rooms_alert_progress","room_zone_alert_progress");
    }

    function allocate_rooms_to_send_alert() {
        update_page("orders=get_rooms_send_alert", "room_selection_zone_send_alert");
    }

    function first_load_in() {
        get_members();
        allocate_rooms_to_alert_progress();
        allocate_rooms_to_send_alert();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Search Bar

    function get_search_cards() {
        if (cur_name !== "") {
            document.getElementById(cur_id).style.backgroundColor = "white";
            document.getElementById(cur_id).style.color = "black";
            document.getElementById("alert_member_title").innerText = "";
            cur_name = "";
            cur_id = "";
        }
        var input = document.getElementById("search_box").value;
        update_page("orders=get_search_results&input=" + input, "member_db_panel");
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // DB Panel

    function change_to_unselected_color(id) {
        document.getElementById(id).style.backgroundColor = "white";
        document.getElementById(id).style.color = "black";
    }

    function change_to_selected_color(id) {
        document.getElementById(id).style.backgroundColor = "limegreen";
        document.getElementById(id).style.color = "white";

    }

    function member_selected(id) {
        if(cur_id !== "") {
            change_to_unselected_color(cur_id);
        }
        change_to_selected_color(id);
        cur_id = id;
        cur_name = document.getElementById(id).innerText;
        document.getElementById("alert_member_title").innerText = " - " + cur_name;
        if(cur_room_full_name === "") {
            var message_to_get_person = cur_name + " has been selected.<br><strong style=\"color:orangered\">Choose a room as well...</strong>";
            document.getElementById("send_alert_error_message").innerHTML = message_to_get_person;
        }
        else {
            var message_to_get_person = cur_name + " and " + cur_room_full_name + " has been selected.<br><strong style=\"color:green\">This is read to send!</strong>";
            document.getElementById("send_alert_error_message").innerHTML = message_to_get_person;
        }
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Alert Progress

    function check_for_alert_response() {
        update_page("orders=check_on_alerts", "room_zone_alert_progress")
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Add a Member

    function check_for_member(name) {
        update_page("orders=check_for_member&name=" + name,"add_mem_error_message");
        var error = document.getElementById("add_mem_error_message").innerText;
        return error === "";
    }

    function add_member() {
        document.getElementById("add_mem_error_message").innerHTML = "";
        document.getElementById("add_mem_error_message").innerText = "";

        var first_name = document.getElementById("add_mem_first_name").value;
        var last_name = document.getElementById("add_mem_last_name").value;
        var full_name = first_name + " " + last_name;
        if(first_name === "" || last_name === "") {
            document.getElementById("add_mem_error_message").innerText = "ERROR: Make sure you enter a FIRST and LAST NAME"
        }
        else {
            var check = check_for_member(full_name);
            if(check === true) {
                console.log("going to back end");
                update_page("orders=add_member&first_name=" + first_name + "&last_name=" + last_name, "add_mem_error_message");
            }
        }
        get_members();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Send Alert

    function room_selected(room) {
        if(cur_room_full_name === "") {
            cur_room_id = room;
            cur_room_full_name = document.getElementById("sa_" + room).innerText;
            change_to_selected_color("sa_" + room);
            if(cur_name === "") {
                var message_to_get_person = cur_room_full_name + " has been selected.<br><strong style=\"color:blue\">Choose a person as well...</strong>";
                document.getElementById("send_alert_error_message").innerHTML = message_to_get_person;
            }
            else {
                var message_to_get_person = cur_name + " and " + cur_room_full_name + " has been selected.<br><strong style=\"color:green\">This is read to send!</strong>";
                document.getElementById("send_alert_error_message").innerHTML = message_to_get_person;
            }
        }
        else {
            change_to_unselected_color("sa_" + cur_room_id);
            change_to_selected_color("sa_" + room);
            cur_room_id = room;
            cur_room_full_name = document.getElementById("sa_" + room).innerText;
            if(cur_name === "") {
                var message_to_get_person = cur_room_full_name + " has been selected.<br><strong style=\"color:blue\">Choose a person as well...</strong>";
                document.getElementById("send_alert_error_message").innerHTML = message_to_get_person;
            }
            else {
                var message_to_get_person = cur_name + " and " + cur_room_full_name + " has been selected.<br><strong style=\"color:green\">This is read to send!</strong>";
                document.getElementById("send_alert_error_message").innerHTML = message_to_get_person;
            }
        }
    }

    function send_alert() {
        if(cur_name === "") {
            document.getElementById("send_alert_error_message").innerHTML = "<strong style='colod:red'>ALERT NOT SENT: CHOOSE A PERSON</strong>";
        }
        else if(cur_room_full_name === "") {
            document.getElementById("send_alert_error_message").innerHTML = "<strong style='colod:red'>ALERT NOT SENT: CHOOSE A PERSON</strong>";
        }
        else {
            var p1 = "orders=send_alert";
            var p2 = "&person_id=" + cur_id;
            var p3 = "&person_name=" + cur_name;
            var p4 = "&room_id=" + cur_room_id;
            var p5 = "&room_name=" + cur_room_full_name;
            var full_alert = p1 + p2 + p3 + p4 + p5;
            update_page(full_alert, "current_alert_message");
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // complete alert button

    function complete_alert() {
        update_page("orders=complete_alert", "current_alert_message");
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // complete alert button
    function check_if_alert_already_set() {
        update_page("orders=check_for_already_set_alert", "current_alert_message")
    }


</script>
</html>



























