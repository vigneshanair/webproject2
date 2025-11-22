<?php
// includes/cases_data.php

$CASES = [
    'case1' => [
        'id'        => 'case1',
        'title'     => 'The Midnight Museum Heist',
        'difficulty'=> 1,
        'summary'   => 'A priceless artifact vanished from the City Museum during a stormy night.',
        'objective' => 'Identify the thief, the time of the heist, and how they got in.',
        'status'    => 'unlocked',
        'depends_on'=> [],

        'suspects' => [
            'Alex Mercer' => 'Night security guard with access to cameras. Claims he was fixing a power outage.',
            'Dr. Evelyn Hart' => 'Curator of the artifact wing. Deeply familiar with display security.',
            'Liam Cross' => 'Freelance electrician called to check emergency lights earlier that day.'
        ],

        'crime_scene_areas' => [
            'gallery' => [
                'title' => 'Main Gallery',
                'description' => 'The artifact case sits empty with faint glass scratches.',
                'clue' => 'Fine glass dust near the skylight reflection on the floor.'
            ],
            'roof' => [
                'title' => 'Museum Roof',
                'description' => 'Wet footprints near the skylight, partly washed by rain.',
                'clue' => 'Distinct boot print with a worn heel pattern by the skylight.'
            ],
            'security_room' => [
                'title' => 'Security Room',
                'description' => 'Monitors flicker and logs show a 10-minute blackout.',
                'clue' => 'Manual override log triggered from inside the museum.'
            ]
        ],

        'forensics' => [
            'question' => 'Which forensic sample best links the suspect to the skylight entry?',
            'options_easy' => [
                'A glove fiber from the gallery carpet.',
                'A boot print from the roof matching Alex’s work boots.',
                'A random smudge on a hallway wall.'
            ],
            'options_medium' => [
                'Smudged fingerprints on the display glass.',
                'Boot print with worn heel that matches Alex’s shift boots.',
                'A visitor ticket stub found near the emergency exit.'
            ],
            'options_hard' => [
                'Partial boot pattern lifted from the wet roof edge, matching Alex’s issued boots.',
                'Dust from the artifact plinth showing curator fingerprints.',
                'A generic glove fiber found in the lobby trash.'
            ],
            'correct_index' => 1,
            'clue' => 'Boot print pattern confirms someone with Alex’s issued boots used the skylight.'
        ],

        'interrogations' => [
            'Alex Mercer' => [
                'trait'       => 'Loyal on the surface, but hides details when nervous.',
                'key_question'=> 'Where were you during the camera blackout?',
                'best_answer' => 'He says he was on the roof checking the skylight sensor.',
                'note'        => 'Alex admits being alone on the roof during the blackout.'
            ],
            'Dr. Evelyn Hart' => [
                'trait'       => 'Perfectionist, emotionally attached to the collection.',
                'key_question'=> 'Why did you stay late in the gallery?',
                'best_answer' => 'Preparing labels for a new exhibit.',
                'note'        => 'No direct link to roof access or power controls.'
            ],
            'Liam Cross' => [
                'trait'       => 'Straightforward and cooperative.',
                'key_question'=> 'Did you modify the emergency power system?',
                'best_answer' => 'Only tested backups and left before closing.',
                'note'        => 'System logs confirm he left hours before the blackout.'
            ]
        ],

        'solution' => [
            'culprit' => 'Alex Mercer',
            'method'  => 'Rooftop skylight',
            'time'    => '01:30 AM'
        ],

        'possible_methods' => [
            'Rooftop skylight',
            'Emergency exit door',
            'Loading bay side door'
        ],

        'possible_times' => [
            '11:45 PM',
            '12:15 AM',
            '01:30 AM',
            '03:00 AM'
        ]
    ],

    'case2' => [
        'id'        => 'case2',
        'title'     => 'Echoes of the First Heist',
        'difficulty'=> 2,
        'summary'   => 'Weeks after the museum heist, a copycat crime targets a private gallery.',
        'objective' => 'Decide whether this is a copycat or the same mastermind continuing the plan.',
        'depends_on'=> ['case1'],

        'suspects' => [
            'Nora Vale'  => 'Gallery manager, previously worked at the City Museum archives.',
            'Ethan Cole' => 'Security consultant who reviewed the museum incident.',
            'Alex Mercer'=> 'Former museum guard from the first case, now missing.'
        ],

        'crime_scene_areas' => [
            'lobby' => [
                'title' => 'Gallery Lobby',
                'description' => 'Immaculate entrance, no clear signs of forced entry.',
                'clue' => 'Digital logs show a midnight visit from Ethan using a temporary badge.'
            ],
            'vault' => [
                'title' => 'Private Vault',
                'description' => 'Vault code changed last week. No pry marks.',
                'clue' => 'Keypad smudges match Nora’s typical access pattern.'
            ],
            'loading_bay' => [
                'title' => 'Loading Bay',
                'description' => 'An open crate, straw packing scattered across the floor.',
                'clue' => 'Crate label connects to an anonymous order placed after the museum heist.'
            ]
        ],

        'forensics' => [
            'question' => 'Which forensic clue best links this gallery crime to the museum heist?',
            'options_easy' => [
                'Random visitor fingerprint on a brochure.',
                'Boot tread pattern matching the museum roof samples.',
                'Smudge on a coffee mug in the staff lounge.'
            ],
            'options_medium' => [
                'Fiber match between crate straw and old museum packing.',
                'Boot tread pattern identical to the museum roof sample.',
                'Ticket stub from the museum gift shop in the trash.'
            ],
            'options_hard' => [
                'Same rare boot tread + matching crate fibers across both heists.',
                'Similar camera glitch in the recording.',
                'Shared delivery vendor used for many unrelated shipments.'
            ],
            'correct_index' => 1,
            'clue' => 'Forensic match suggests the same perpetrator or a direct collaborator across both heists.'
        ],

        'interrogations' => [
            'Nora Vale' => [
                'trait'       => 'Meticulous but anxious when questioned.',
                'key_question'=> 'Why did you change the vault code last week?',
                'best_answer' => 'Routine security best practice, according to her.',
                'note'        => 'She stayed late the night before the break-in.'
            ],
            'Ethan Cole' => [
                'trait'       => 'Calm, analytical, deflects direct accusations.',
                'key_question'=> 'Why does your badge show a midnight visit?',
                'best_answer' => 'He claims he was stress-testing security off the record.',
                'note'        => 'Unscheduled late-night visit matches heist timing.'
            ],
            'Alex Mercer' => [
                'trait'       => 'Defensive and evasive, now missing from town.',
                'key_question'=> 'Did you contact anyone at the gallery after the museum heist?',
                'best_answer' => 'Rumors suggest contact with both Nora and Ethan.',
                'note'        => 'Alex’s disappearance links the two crimes narratively.'
            ]
        ],

        'solution' => [
            'culprit' => 'Ethan Cole',
            'method'  => 'Inside access through digital badge and vault codes',
            'time'    => '12:00 AM'
        ],

        'possible_methods' => [
            'Forced loading bay entry',
            'Inside access through digital badge and vault codes',
            'Roof entry using skylight ropes'
        ],

        'possible_times' => [
            '10:30 PM',
            '12:00 AM',
            '02:15 AM'
        ]
    ]
];
