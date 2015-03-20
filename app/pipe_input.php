#!/usr/bin/env php5
<?php
chdir(__DIR__);
require '../vendor/autoload.php';
require 'config.php'; // setup db & constants

$input = new DisposableEmail\Input(file_get_contents("php://stdin"));
$mail = $input->getBean();
$id = R::store( $mail );
