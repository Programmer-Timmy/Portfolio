/**
 * Static content for the About page. It's about who I am and how I work,
 * not a pitch. Edit the copy here.
 */

export type AboutSection = {
  title: string
  /** One string per paragraph. */
  paragraphs: string[]
}

export const about = {
  eyebrow: 'About',
  title: "Hi, I'm Tim",

  /** Intro paragraphs shown in the header, next to the photo. */
  lead: [
    "I'm a software developer from Hilversum. I study Software Development at the HU in Utrecht and work as a junior digital engineer at BAM Infra.",
    'Alongside that I build web apps, contribute to open source, and run a Beaver section at Scouting Het ZuiderKruis. Most of what I make starts as a way to solve a real problem for the people around me.',
  ],

  photo: {
    src: '/img/profielfoto.webp',
    srcSet:
      '/img/profielfoto-400w.webp 400w, /img/profielfoto-800w.webp 800w, /img/profielfoto-1200w.webp 1200w',
    alt: 'Tim van der Kloet',
  },

  /**
   * Body sections, rendered in order below the header. Replace the placeholder
   * paragraphs with your own copy; add or remove sections freely.
   */
  sections: [
    {
      title: 'How I think about software',
      paragraphs: [
        'PLACEHOLDER: what you value in the software you build. Practical, solves a real problem, readable code, does not fall over, a non-technical person can operate it.',
        'PLACEHOLDER: why open source and building for your Scouting group fits that.',
      ],
    },
    {
      title: 'How I work',
      paragraphs: [
        'PLACEHOLDER: an honest description of what working with you is like. Small clear scopes, plain language, frequent check-ins.',
        'PLACEHOLDER: you leave a project documented and handed over, and you do not disappear if something breaks later.',
      ],
    },
    {
      title: 'Outside of code',
      paragraphs: [
        'PLACEHOLDER: Scouting (leading the Beavers, helping organise camps), flying your drone and posting the videos, studies.',
      ],
    },
    {
      title: 'Reaching me',
      paragraphs: [
        'PLACEHOLDER: one line pointing people at the contact page if they want to talk or have a question.',
      ],
    },
  ] satisfies AboutSection[],
}
