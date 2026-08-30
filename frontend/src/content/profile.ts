/**
 * Static "about me" content. This ships with the frontend rather than coming
 * from the API, because none of it is database-backed or changes per request.
 * Edit here; it renders instantly with no fetch or loading state.
 *
 * (DB-backed data - projects, open source, videos - still comes from /api.)
 */

export type Role = {
  title: string
  organization: string
  current: boolean
}

export type Social = {
  label: string
  handle: string
  url: string
  /** Font Awesome class, e.g. "fa-brands fa-github". */
  icon: string
}

export type SkillGroup = {
  group: string
  items: string[]
}

export type Client = {
  name: string
  url: string
  /** Relationship badge: "Volunteer", "Client", "Freelance", ... */
  kind: string
  summary: string
  /** Chips. A tag like "Built pro bono" gets an accent style on the card. */
  tags: string[]
}

export type Profile = {
  name: string
  headline: string
  summary: string
  location: string
  roles: Role[]
  clients: Client[]
  bio: string[]
  cv: { label: string; url: string }
  socials: Social[]
  skills: SkillGroup[]
}

export const profile: Profile = {
  name: 'Tim van der Kloet',
  headline: 'Web development & technology',
  summary: 'Software that actually helps.',
  location: 'Hilversum, Netherlands',

  roles: [
    {
      title: 'Software Development student',
      organization: 'HU University of Applied Sciences Utrecht',
      current: true,
    },
    {
      title: 'Junior Digital Engineer',
      organization: 'BAM Infra',
      current: true,
    },
    {
      title: 'Section leader, Beaver Scouts',
      organization: 'Scouting Het ZuiderKruis',
      current: true,
    },
  ],

  clients: [
    {
      name: 'Scouting Het ZuiderKruis',
      url: 'https://www.zuiderkruis.nl/',
      kind: 'Volunteer',
      summary:
        'Three years as a section leader for the Beavers, the youngest group. ' +
        'I built the soos, where members order drinks and food on bar evenings, ' +
        "and I'm building an activities site where people sign up and pay for camps and events.",
      tags: [
        'Built pro bono',
        'Ordering site (the soos)',
        'Activity & camp sign-ups',
        'Online payments',
      ],
    },
  ],

  bio: [
    'I focus on server management and cybersecurity alongside my studies, because I think ' +
      'those areas matter most for where technology is heading.',
    'Outside of study I build personal projects, contribute to open source, and run a Beaver ' +
      'section at my Scouting group, which has taught me as much about leadership and ' +
      'organisation as about code.',
  ],

  cv: {
    label: 'Curriculum Vitae (PDF)',
    url: '/doc/CV.pdf',
  },

  socials: [
    { label: 'Email', handle: 'tim.vanderkloet@gmail.com', url: 'mailto:tim.vanderkloet@gmail.com', icon: 'fa-solid fa-envelope' },
    { label: 'GitHub', handle: 'Programmer-Timmy', url: 'https://github.com/Programmer-Timmy', icon: 'fa-brands fa-github' },
    { label: 'LinkedIn', handle: 'Tim van der Kloet', url: 'https://www.linkedin.com/in/tim-van-der-kloet', icon: 'fa-brands fa-linkedin-in' },
    { label: 'YouTube', handle: '@Tim-van-der-Kloet', url: 'https://www.youtube.com/@Tim-van-der-Kloet', icon: 'fa-brands fa-youtube' },
  ],

  skills: [
    { group: 'Web', items: ['HTML5', 'CSS3 / SCSS', 'JavaScript', 'TypeScript', 'React', 'Tailwind CSS', 'jQuery', 'Mantine'] },
    { group: 'Backend', items: ['PHP', 'Laravel', 'Java', 'Python', 'REST APIs'] },
    { group: 'Data', items: ['MySQL', 'MariaDB', 'Redis', 'PostgreSQL'] },
    { group: 'Infrastructure', items: ['Ubuntu', 'Virtualmin', 'Proxmox', 'Docker', 'Git', 'GitHub'] },
  ],
}
