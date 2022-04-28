<?php

namespace App\Service;

class ServiceAccueil
{
    public function getMsg(): string
    {
        $messages = [
            'Les chèvres sont réputées pour réduire le stress',
            'https://www.youtube.com/watch?v=xvFZjo5PgG0',
            'La chèvre fait partie du top 1 des animaux les plus mignons',
            'Bonjour, je vous met combien de chèvres aujourd\'hui ?',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
}