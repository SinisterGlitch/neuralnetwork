<?php

/**
 * Class BoardGame
 */
class BoardGame
{
    /**
     * @var array
     */
    private $players = [];

    /**
     * @var array
     */
    private $currentPlayer = [];

    /**
     * @var array
     */
    private $board = [];

    /**
     * @var array
     */
    private $games = [];

    /**
     * @var int
     */
    private $emptyValue;

    /**
     * @var integer
     */
    private $rowsY;

    /**
     * @var integer
     */
    private $rowsX;

    /**
     * @param integer $rowsY
     * @param integer $rowsX
     * @param int $emptyValue
     */
    public function __construct($rowsY, $rowsX, $emptyValue = 0)
    {
        $this->rowsY = $rowsY;
        $this->rowsX = $rowsX;
        $this->emptyValue = $emptyValue;
    }

    /**
     * @param int $times
     * @return array
     */
    public function getGames($times)
    {
        for ($i = 0; $i < $times; $i++) {
            $game = $this->playGame();
//            if ($game['condition']) {
                $this->games[] = $game;
//            }
        }

        return $this->games;
    }

    /**
     * @return array
     */
    public function playGame()
    {
        $win = false;
        $steps = [];
        $endGame = false;
        $this->resetBoard();

        while ($endGame == false) {
            $moves = $this->getAvailableMoves();
            $move = $moves[rand(0, count($moves) - 1)];

            if ($this->playerWon()) {
                $win = true;
            }

            if (empty($moves) || $win) {
                $endGame = true;
            } else {
                $this->setNextPlayer();
                $steps[] = $move[0].''.$move[1];
                $this->setMove($move[0], $move[1]);
            }
        }


        $player = $this->getCurrentPlayer();

        $condition = $endGame && !$win
            ? $this->emptyValue
            : $player[2];

        return [
            'data' => $this->getBoard(),
            'steps' => $steps,
            'condition' => $condition
        ];
    }

    /**
     * @return bool
     */
    public function playerWon()
    {
        if ($this->horizontalCheck()
            || $this->verticalCheck()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function horizontalCheck()
    {
        for ($x = $this->rowsX - 1; $x > -1; $x--) {
            $value = $this->board[$x][0];
            if ($this->board[$x][1] == $value
                && $this->board[$x][2] == $value
                && $value !== $this->emptyValue
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    private function verticalCheck()
    {
        $board = $this->board;
        for ($y = 0; $y < $this->rowsY; $y++) {
            $value = $board[0][$y];
            if ($board[1][$y] == $value
                && $board[2][$y] == $value
                && $value !== $this->emptyValue
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAvailableMoves()
    {
        $moves = [];
        for ($x = $this->rowsX-1; $x >= 0; $x--) {
            for ($y = 0; $y < $this->rowsY; $y++) {

                $allowedMove = true;
                if ($x !== $this->rowsX - 1 && $this->board[$x+1][$y] == 0) {
                    $allowedMove = false;
                }

                if ($this->board[$x][$y] === 0 && $allowedMove) {
                    $moves[] = [$x, $y];
                }

                if (count($moves) == $this->rowsY) {
                    return $moves;
                }
            }
        }

        return $moves;
    }

    /**
     * clear game history
     */
    public function clearGames()
    {
        $this->games = [];
    }

    /**
     * reset board;
     */
    public function resetBoard()
    {
        for ($x = 0; $x < $this->rowsX; $x++) {
            for ($y = 0; $y < $this->rowsY; $y++) {
                $this->board[$x][$y] = $this->emptyValue;
            }
        }
    }

    /**
     * @return array
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @param integer $x
     * @param integer $y
     */
    public function setMove($x, $y)
    {
        $this->board[$x][$y] = $this->currentPlayer[2];
    }

    /**
     * @param array $game
     * @return string
     */
    public function drawBoard($game)
    {
        $i = 0;
        $output = '';
        for ($x = 0; $x < $this->rowsX; $x++) {
            for ($y = 0; $y < $this->rowsY; $y++) {

                if ($game[$x][$y] == -1) {
                    $output .= $this->colorRed('O ');
                } elseif ($game[$x][$y] == 1) {
                    $output .= $this->colorBlue('O ');
                } elseif ($game[$x][$y] == 2) {
                    $output .= $this->colorBlue('O ');
                } elseif ($game[$x][$y] == 0) {
                    $output .= $this->colorBlack('O ');
                }

                $i++;
                if ($i == $this->rowsY) {
                    $output .= "\n";
                    $i = 0;
                }
            }
        }

        return "\n".$output."\n";
    }

    /**
     * Sets net current player
     */
    public function setNextPlayer()
    {
        $this->currentPlayer = current($this->players);
        array_shift($this->players);
        $this->players[] = $this->currentPlayer;
    }

    /**
     * @return array
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @return array
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    /**
     * @param string $name
     * @param string $symbol
     * @param integer $value
     */
    public function addPlayer($name, $symbol, $value)
    {
        $this->players[] = [$name, $symbol, $value];
    }

    /**
     * @param string $name
     */
    public function removePlayer($name)
    {
        if (array_key_exists($this->players, $name)) {
            unset($this->players[$name]);
        }
    }

    /**
     * @param array $game
     * @return array
     */
    public function convertToNeuralData(array $game)
    {
        $output = [];
        for ($x = 0; $x < $this->rowsX; $x++) {
            for ($y = 0; $y < $this->rowsY; $y++) {
                $output[] = $game['data'][$x][$y];
            }
        }

        return $output;
    }

    /**
     * @param $string
     * @return string
     */
    public function colorBlue($string)
    {
        return "\033[34m".$string."\033[37m";
    }

    /**
     * @param $string
     * @return string
     */
    public function colorBlack($string)
    {
        return "\033[30m".$string."\033[37m";
    }

    /**
     * @param $string
     * @return string
     */
    public function colorOrange($string)
    {
        return "\033[33m".$string."\033[37m";
    }

    /**
     * @param $string
     * @return string
     */
    public function colorRed($string)
    {
        return "\033[31m".$string."\033[37m";
    }
}