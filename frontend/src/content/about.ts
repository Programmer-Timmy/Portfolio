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
        'I like to build things that solve a real problem for the people using them. When I start a new project, I always want to find out what the real problem is and how I can solve it in a practical and making the scope as small as possible. Software should not be build in one go, It should be build in small steps and be improved over time. This allows for a better understanding of the problem and a better maintainable solution.',

        'Code will never be maintained by the same person who wrote it. It will be maintained by someone else, possibly years later. This means that the code must be easy to understand and maintain. That is why I always try to make my code as readable as possible by using common methods to make code easy to read, maintain and extend. This is also why I try to write as little code as possible, because less code means less complexity and less chance of bugs.',

        'A lot of how I think about code comes from open source. Reading through other people\'s projects is one of the best ways to learn how things are really built, and contributing back means my own work gets reviewed by people who have no reason to go easy on it. I contribute where I can and try to work in the open, because the tools I get the most value from were built that way.',
      ],
    },
    {
      title: 'How I work',
      paragraphs: [
        'Working with me comes down to communication. I keep scopes small and clearly defined, talk in plain language instead of jargon, and check in often so nothing comes as a surprise later on. Even on a project I do alone I try to get as much feedback as I can, because that is usually what turns an okay solution into a good one.',

        'When something is finished I make sure it is documented and properly handed over, not just dropped. And I do not disappear afterwards. If something breaks or needs changing down the line, you can come back to me.',
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
