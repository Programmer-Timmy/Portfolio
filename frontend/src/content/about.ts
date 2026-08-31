/**
 * Static content for the About page. It's about who I am and how I work,
 * not a pitch. Edit the copy here.
 */

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
}
