<?php

require 'vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();

$app->add(
    new \Jehaby\Quest\Command(
        new \Jehaby\Quest\Quest()
    )
);

$app->run();

