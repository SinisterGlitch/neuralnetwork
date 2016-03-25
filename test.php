<?php
require_once('classes/includes.php');
require_once('classes/neural.php');
require_once('classes/gameMaker.php');

$boardGame = new BoardGame(3,3,0);
$boardGame->addPlayer('germain', 'X ', -1);
$boardGame->addPlayer('lisa', '| ', 1);

$neural = new NeuralNetwork([9, 80, 3]);
$neural->setVerbose(false);
$neural->load('train-data');

foreach ($boardGame->getGames(4) as $game) {
    $data = $boardGame->convertToNeuralData($game);
    $value = $neural->calculate($data);

    echo $boardGame->drawBoard($game['data']);
    echo $game['condition']."\n";

    print_r($value);
    echo "\n\n";
}

echo "\n\n\033[33mScript complete\033[37m\n\n";