<?php

/**
 * Structured "about me" content, served by GET /api/profile and GET /api/skills.
 * This is the single source of truth for the bio. Edit here, not in a view.
 * Dates drive the computed `age` / `scouting.years` fields in MetaApi.
 */

return [
    'name' => 'Tim van der Kloet',
    'headline' => 'Web development & technology',
    'summary' => 'Software that actually helps.',
    'location' => 'Hilversum, Netherlands',
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

    /**
     * Scouting section on the home page. `summary` is the paragraph, `tags`
     * are the chips beside it. `years` is filled in automatically from `since`.
     * To add another thing you built for Scouting, just add a line to `tags`.
     */
    'scouting' => [
        'group' => 'Scouting Het ZuiderKruis',
        'groupUrl' => 'https://www.zuiderkruis.nl/',
        'role' => 'Section leader for the Beavers (youngest group)',
        'since' => '2013-11-17',
        'summary' => "Twelve years as a section leader for the Beavers, the youngest group. "
            . "Alongside that I've built the soos, a site where members can order drinks and "
            . "food during the bar evenings, and I'm currently working on an activities site "
            . "where people can sign up and pay for camps and other events.",
        'tags' => [
            'Ordering site (the soos)',
            'Activity & camp sign-ups',
            'Online payments',
        ],
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
            'items' => ['HTML5', 'CSS3 / SCSS', 'JavaScript', 'TypeScript', 'React', 'Tailwind CSS', 'jQuery', 'Mantine'],
        ],
        [
            'group' => 'Backend',
            'items' => ['PHP', 'Laravel', 'Java', 'Python', 'REST APIs'],
        ],
        [
            'group' => 'Data',
            'items' => ['MySQL', 'MariaDB', 'Redis', 'PostgreSQL'],
        ],
        [
            'group' => 'Infrastructure',
            'items' => ['Ubuntu', 'Virtualmin', 'Proxmox', 'Docker', 'Git', 'Github'],
        ]
    ],
];
