<?php

/**
 * Structured "about me" content, served by GET /api/profile and GET /api/skills.
 * This is the single source of truth for the bio. Edit here, not in a view.
 * Dates drive the computed `age` / `scouting.years` fields in MetaApi.
 */

return [
    'name' => 'Tim van der Kloet',
    'headline' => 'Web development & technology',
    'summary' => 'I build practical software for clients, in the open, and for Scouting.',
    'location' => 'Utrecht, Netherlands',
    'birthDate' => '2005-08-03',
    'timezone' => 'Europe/Amsterdam',

    'roles' => [
        [
            'title' => 'Software Development student',
            'organization' => 'HU University of Applied Sciences Utrecht',
            'current' => true,
        ],
        [
            'title' => 'Junior Digital Engineer',
            'organization' => 'BAM Infra',
            'current' => true,
        ],
    ],

    'scouting' => [
        'group' => 'Scouting Het ZuiderKruis',
        'groupUrl' => 'https://www.zuiderkruis.nl/',
        'role' => 'Section leader for the Beavers (youngest group)',
        'since' => '2013-11-17',
        'blurb' => 'I lead the youngest group and help organise events and activities. '
            . 'A lot of what I build lately is aimed at making Scouting work run smoother.',
    ],

    'bio' => [
        'I focus on server management and cybersecurity alongside my studies, because I think '
            . 'those areas matter most for where technology is heading.',
        'Outside of study I build personal projects, contribute to open source, and run a Beaver '
            . 'section at my Scouting group, which has taught me as much about leadership and '
            . 'organisation as about code.',
    ],

    'cv' => [
        'label' => 'Curriculum Vitae (PDF)',
        'url' => '/doc/CV.pdf',
    ],

    'socials' => [
        ['label' => 'Email', 'handle' => 'tim.vanderkloet@gmail.com', 'url' => 'mailto:tim.vanderkloet@gmail.com', 'icon' => 'mail'],
        ['label' => 'GitHub', 'handle' => 'Programmer-Timmy', 'url' => 'https://github.com/Programmer-Timmy', 'icon' => 'github'],
        ['label' => 'LinkedIn', 'handle' => 'Tim van der Kloet', 'url' => 'https://www.linkedin.com/in/tim-van-der-kloet', 'icon' => 'linkedin'],
        ['label' => 'YouTube', 'handle' => '@Tim-van-der-Kloet', 'url' => 'https://www.youtube.com/@Tim-van-der-Kloet', 'icon' => 'youtube'],
    ],

    'skills' => [
        [
            'group' => 'Web',
            'items' => ['HTML5', 'CSS3 / SCSS', 'JavaScript', 'TypeScript', 'React', 'Tailwind CSS', 'jQuery', 'WordPress'],
        ],
        [
            'group' => 'Backend',
            'items' => ['PHP', 'Laravel', 'Symfony', 'Python', 'C#', 'REST APIs'],
        ],
        [
            'group' => 'Data',
            'items' => ['MySQL', 'MariaDB', 'phpMyAdmin'],
        ],
        [
            'group' => 'Infrastructure',
            'items' => ['Ubuntu', 'Webmin', 'Server management', 'Git', 'GitHub'],
        ],
        [
            'group' => 'Game development',
            'items' => ['Unreal Engine', 'Unity'],
        ],
    ],
];
