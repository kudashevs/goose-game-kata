# The Goose Game Kata ![test workflow](https://github.com/kudashevs/goose-game-kata/actions/workflows/run-tests.yml/badge.svg)
Goose game is a game where two or more players move pieces around a track by rolling a die. The aim of the game is to reach square number sixty-three before any of the other players and avoid obstacles. ([wikipedia](https://en.wikipedia.org/wiki/Game_of_the_Goose))

This kata has been invented by [Matteo Vaccari](https://github.com/xpmatteo), you can find the original slides [here](https://www.slideshare.net/pierodibello/il-dilettevole-giuoco-delloca-coding-dojo).

## Usage
The game doesn't require any compilation step. However, it requires a PHP interpreter to run.  
Clone the repository, change directory, install all of the dependencies, and run the application.
```shell
git clone https://github.com/kudashevs/goose-game-kata
cd goose-game-kata
composer install
bin/goose-game.php
```

If you want to make sure that everything works as expected, you can run unit tests.
```bash
composer test
```

## Features

### 1. Add players
As a player, I want to add me to the game so that I can play.

**Scenarios:**
1. Add Player
```cucumber
If there is no participant
the user writes: "add player Pippo"
the system responds: "players: Pippo"
the user writes: "add player Pluto"
the system responds: "players: Pippo, Pluto"
```

2. Duplicated Player
```cucumber
If there is already a participant "Pippo"
the user writes: "add player Pippo"
the system responds: "Pippo: already existing player"
```

### 2. Move a player
As a player, I want to move the marker on the board to make the game progress

**Scenarios:**
1. Start
```cucumber
If there are two participants "Pippo" and "Pluto" on space "Start"
the user writes: "move Pippo 4, 2"
the system responds: "Pippo rolls 4, 2. Pippo moves from Start to 6"
the user writes: "move Pluto 2, 2"
the system responds: "Pluto rolls 2, 2. Pluto moves from Start to 4"
the user writes: "move Pippo 2, 3"
the system responds: "Pippo rolls 2, 3. Pippo moves from 6 to 11"
```

### 3. Win
As a player, I win the game if I land on space "63"

**Scenarios:**
1. Victory
```cucumber
If there is one participant "Pippo" on space "60"
the user writes: "move Pippo 1, 2"
the system responds: "Pippo rolls 1, 2. Pippo moves from 60 to 63. Pippo Wins!!"
```

2. Winning with the exact dice shooting
```cucumber
If there is one participant "Pippo" on space "60"
the user writes: "move Pippo 3, 2"
the system responds: "Pippo rolls 3, 2. Pippo moves from 60 to 63. Pippo bounces! Pippo returns to 61"
```

### 4. The game throws the dice
As a player, I want the game throws the dice for me to save effort

**Scenarios:**
1. Dice roll
```cucumber
If there is one participant "Pippo" on space "4"
assuming that the dice get 1 and 2
when the user writes: "move Pippo"
the system responds: "Pippo rolls 1, 2. Pippo moves from 4 to 7"
```

### 5. Space "6" is "The Bridge"
As a player, when I get to the space "The Bridge", I jump to the space "12"

**Scenarios:**
1. Get to "The Bridge"
```cucumber
If there is one participant "Pippo" on space "4"
assuming that the dice get 1 and 1
when the user writes: "move Pippo"
the system responds: "Pippo rolls 1, 1. Pippo moves from 4 to The Bridge. Pippo jumps to 12"
```

### 6. If you land on "The Goose", move again
As a player, when I get to a space with a picture of "The Goose", I move forward again by the sum of the two dice rolled before

The spaces 5, 9, 14, 18, 23, 27 have a picture of "The Goose"

**Scenarios:**
1. Single Jump
```cucumber
If there is one participant "Pippo" on space "3"
assuming that the dice get 1 and 1
when the user writes: "move Pippo"
the system responds: "Pippo rolls 1, 1. Pippo moves from 3 to 5, The Goose. Pippo moves again and goes to 7"
```

2. Multiple Jump
```cucumber
If there is one participant "Pippo" on space "10"
assuming that the dice get 2 and 2
when the user writes: "move Pippo"
the system responds: "Pippo rolls 2, 2. Pippo moves from 10 to 14, The Goose. Pippo moves again and goes to 18, The Goose. Pippo moves again and goes to 22"
```

### 7. Prank (Optional Step)
As a player, when I land on a space occupied by another player, I send him to my previous position so that the game can be more entertaining.

**Scenarios:**
1. Prank
```cucumber
If there are two participants "Pippo" and "Pluto" respectively on spaces "15" and "17"
assuming that the dice get 1 and 1
when the user writes: "move Pippo"
the system responds: "Pippo rolls 1, 1. Pippo moves from 15 to 17. On 17 there is Pluto, who returns to 15"
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
