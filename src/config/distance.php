<?php

return [

    'base_unit' => 'centimeters',

    'display_unit' => null,

    'conversions' => [
        'centimeters:inches' => 0.393701,
        'centimeters:meters' => 0.01,
        'centimeters:kilometers' => 0.00001,
        'centimeters:miles' => 0.0000062137,
        'centimeters:footsteps' => 0.0145815106,
    ],

    'formatting' => [
        'precision' => [
            'footsteps' => 0,
            'inches' => 0,
        ],
        'translation' => [
            /* Set if you wish to use Laravel pluralization of unit strings */
            'choice' => true,
        ],
        'round' => [
            'footsteps' => \TeamChallengeApps\Distance\RoundingMode::CEILING,
        ],
    ]

];
