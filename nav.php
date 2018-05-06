<?php

function button_action($button) {

}

function create_buttons() {
    print "<a class='nav_button' id='logo_button' href='main.php'>Main</a>";
    print "<a class='nav_button' id='admin_button' href='admin.php'>Admin</a>";
}

function get_nav_css() {
    print "<link rel='stylesheet' type='text/css' href='nav.css'>";
    print "<link href=\"https://fonts.googleapis.com/css?family=Raleway\" rel=\"stylesheet\">";
}

function create_nav_bar() {
    print "<div class='nav_bar'>";
    get_nav_css();
    create_buttons();
    print "</div>";
}

