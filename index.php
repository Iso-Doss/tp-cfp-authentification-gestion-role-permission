<?php
require_once 'app/fonctions.php';

$ressource = (!empty($_GET['ressource'])) ? $_GET['ressource'] : '';
$action = (!empty($_GET['action'])) ? $_GET['action'] : '';

router($ressource, $action);