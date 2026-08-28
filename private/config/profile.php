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
        [
            'title' => 'Section leader, Beaver Scouts',
            'organization' => 'Scouting Het ZuiderKruis',
            'current' => true,
        ],
    ],

    /**
     * "Who I work with" section on the home page. One entry per organisation
     * you've built for. `kind` is the badge (Volunteer, Client, Freelance...).
     * `tags` are the chips: put "Built pro bono" (or similar) there for work
     * you did unpaid. Add an entry to list another client.
     */
    'clients' => [
        [
            'name' => 'Scouting Het ZuiderKruis',
            'url' => 'https://www.zuiderkruis.nl/',
            'kind' => 'Volunteer',
            'summary' => "Three years as a section leader for the Beavers, the youngest group. "
                . "I built the soos, where members order drinks and food on bar evenings, and "
                . "I'm building an activities site where people sign up and pay for camps and events.",
            'tags' => [
                'Built pro bono',
                'Ordering site (the soos)',
                'Activity & camp sign-ups',
                'Online payments',
            ],
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
