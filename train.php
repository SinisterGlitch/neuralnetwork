<?php
require_once('classes/includes.php');
require_once('classes/neural.php');
require_once('classes/gameMaker.php');

$boardGame = new BoardGame(3,3,0);
$boardGame->addPlayer('germain', 'O ', -1);
$boardGame->addPlayer('lisa', 'O ', 1);

$neural = new NeuralNetwork([9, 80, 2]);
$neural->setVerbose(false);

foreach ($boardGame->getGames(1000) as $game) {
    $data = $boardGame->convertToNeuralData($game);
    $neural->addTestData($data, [$game['condition']]);
}

echo "\033[35m\n\nTraining...\033[37m";

$trained = $neural->train(1000, 0.001);
$neural->save('train-data');

echo "\n\n\033[33mScript complete\033[37m\n\n";