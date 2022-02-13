<?php

namespace App\Service;

class PasswordGenerator
{

    public function passwordAleatoire(int $length): string
    {

        $uppercaseLetters = $this->generateCharactersWithCharCodeRange([65, 90]);

        $lowercaseLetters = $this->generateCharactersWithCharCodeRange([97, 122]);

        $numbers = $this->generateCharactersWithCharCodeRange([48, 57]);

        $symbols = $this->generateCharactersWithCharCodeRange([33, 47, 58, 64, 91, 96, 123, 126]);

        // dd($uppercaseLetters, $lowercaseLetters, $numbers, $symbols);

        $allCharacters = array_merge($uppercaseLetters, $lowercaseLetters, $numbers, $symbols);

        // dd($allCharacters);


        //On va melanger tous ces caractères spécieux avec la fonction shuffle()
        $isArrayShuffled = shuffle($allCharacters);

        if(!$isArrayShuffled) {

            throw new \LogicException("La génération du mot de passe aléatoire a échoué, veuillez rééesayer.");
        }

        // dd($allCharacters);

        return implode('',array_slice($allCharacters, 0, $length));


    }


    private function generateCharactersWithCharCodeRange(array $range): array
    {

        if(count($range) === 2){

            return range(chr($range[0]), chr($range[1]));

        }else{

            return array_merge(...array_map(fn($range) => range(chr($range[0]), chr($range[1])), array_chunk($range, 2)));

        }


    }
} 