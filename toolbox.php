#!/usr/bin/php
<?php

include('vendor/autoload.php');

$climate = new League\CLImate\CLImate;

showMenu();


function showMenu()
{
    $climate->br();
    $climate->tab()->out('DrawBot toolbox');
    $climate->tab()->out('===============');
    $climate->br();
    $climate->tab()->tab()->out('1. Turn off motors');
    $climate->tab()->tab()->out('2. Adjust left motor');
    $climate->tab()->tab()->out('3. Adjust right motor');
    $climate->tab()->tab()->out('4. Exit');
    $climate->br();
    $climate->tab()->tab()->out('Pick an option:');
    $climate->br();


}


class toolbox
{

}
