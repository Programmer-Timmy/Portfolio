import { Container } from '@/components/ui/Container'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import { about } from '@/content/about'

export function AboutPage() {
  useDocumentTitle('About')

  return (
    <>
      <AboutHeader />
      <AboutBody />
    </>
  )
}

function AboutHeader() {
  return (
    <Container as="section" className="py-14 sm:py-20">
      <div className="grid items-center gap-10 md:grid-cols-[1.5fr_1fr] lg:gap-16">
        <div>
          <p className="mb-3 font-mono text-xs uppercase tracking-widest text-teal">
            {about.eyebrow}
          </p>
          <h1 className="text-h1">{about.title}</h1>
          <div className="mt-6 max-w-xl space-y-4 text-lg text-ink-secondary">
            {about.lead.map((paragraph) => (
              <p key={paragraph}>{paragraph}</p>
            ))}
          </div>
        </div>

        <img
          src={about.photo.src}
          srcSet={about.photo.srcSet}
          sizes="(min-width: 768px) 20rem, 14rem"
          alt={about.photo.alt}
          className="aspect-[4/5] w-56 rounded-card border border-line object-cover md:ml-auto md:w-full md:max-w-xs"
        />
      </div>
    </Container>
  )
}

function AboutBody() {
  return (
    <Container as="section" className="pb-24">
      <div className="max-w-2xl divide-y divide-line border-t border-line">
        {about.sections.map((section) => (
          <section key={section.title} className="py-10">
            <h2 className="text-h3">{section.title}</h2>
            <div className="mt-4 space-y-4 text-ink-secondary">
              {section.paragraphs.map((paragraph) => (
                <p key={paragraph}>{paragraph}</p>
              ))}
            </div>
          </section>
        ))}
      </div>
    </Container>
  )
}
