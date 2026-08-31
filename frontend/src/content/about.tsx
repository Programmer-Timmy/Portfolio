import type { ReactNode } from 'react'
import { TextLink } from '@/components/ui/TextLink'

/**
 * Static content for the About page. It's about who I am and how I work,
 * not a pitch. Edit the copy here.
 */

export type AboutSection = {
  title: string
  /** One node per paragraph. Plain strings are fine; wrap links in <TextLink>. */
  paragraphs: ReactNode[]
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

  /** Body sections, rendered in order below the header. Add or remove freely. */
  sections: [
    {
      title: 'How I think about software',
      paragraphs: [
        'I like to build things that solve a real problem for the people using them. When I start a new project, I always want to find out what the real problem is and how I can solve it in a practical and making the scope as small as possible. Software should not be build in one go, It should be build in small steps and be improved over time. This allows for a better understanding of the problem and a better maintainable solution.',

        'Code will never be maintained by the same person who wrote it. It will be maintained by someone else, possibly years later. This means that the code must be easy to understand and maintain. That is why I always try to make my code as readable as possible by using common methods to make code easy to read, maintain and extend. This is also why I try to write as little code as possible, because less code means less complexity and less chance of bugs.',

        'I recently started to contribute more to OpenSource projects in my free time. I find it important that there is free software available for everyone to use, and I want to contribute to that. I also find it really interesting you work on a project with people from all over the world, and learn from their code and their way of thinking.',
      ],
    },
    {
      title: 'How I work',
      paragraphs: [
        'I like to work in small steps and get feedback as soon as possible. This allows me to make a great working product that is easy to use for everyone. Because of this I like to work in an agile way, with sprints. This allows me to work on a small part of the project and get feedback on that part before moving on to the next part. This way I can make sure that the project is always moving forward and that the end result is a great product.',

        'When i am done with a project I try to make sure everything is well documented, so that it is easy to maintain and extend in the future, even for a other developer.',
      ],
    },
    {
      title: 'Outside of code',
      paragraphs: [
        'When I am not coding for work or for fun, I like to spend time at scouting. Most of the time I am buzzy managing things for the Beaver Colony, which I am the group leader of. I also am part of the "klusjesmannen" (handymen) within my scouting organisation, where we work on projects like making storage solutions fixing our boats and other things that need to be fixed or built. I also like to spend time on gaming, mostly factory building games like Satisfactory, but I also like to play other games like city or logistic building games. I also like to spend time with my friends and family, and I like to go out for a walk, preferably in the mountains',
      ],
    },
    {
      title: 'Reaching me',
      paragraphs: [
        <>
          If you want to reach me, you can do so via the{' '}
          <TextLink to="/contact">contact page</TextLink>. I am always happy to
          help others out with their projects, or to discuss new ideas. I am also
          open to new opportunities, so if you have a project in mind that you
          think I would be a good fit for, please feel free to reach out to me.
        </>,
      ],
    },
  ] satisfies AboutSection[],
}
