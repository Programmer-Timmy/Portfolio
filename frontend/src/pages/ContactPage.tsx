import { useState } from 'react'
import { Container } from '@/components/ui/Container'
import { Button } from '@/components/ui/Button'
import { Field } from '@/components/ui/Field'
import { Icon } from '@/components/ui/Icon'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import { api, ApiError } from '@/lib/api'
import { contact } from '@/content/contact'
import { profile } from '@/content/profile'

export function ContactPage() {
  useDocumentTitle('Contact')

  return (
    <>
      <ContactHeader />
      <Container as="section" className="pb-24">
        <div className="grid gap-12 md:grid-cols-[1.4fr_1fr] md:gap-16">
          <ContactForm />
          <ContactMethods />
        </div>
      </Container>
    </>
  )
}

function ContactHeader() {
  return (
    <Container as="section" className="py-14 sm:py-20">
      <p className="mb-3 font-mono text-xs uppercase tracking-widest text-teal">
        {contact.eyebrow}
      </p>
      <h1 className="text-h1">{contact.title}</h1>
      <div className="mt-6 max-w-2xl space-y-4 text-lg text-ink-secondary">
        {contact.lead.map((paragraph) => (
          <p key={paragraph}>{paragraph}</p>
        ))}
      </div>
    </Container>
  )
}

type FieldErrors = Partial<Record<'name' | 'email' | 'message', string>>

function validate(values: { name: string; email: string; message: string }): FieldErrors {
  const errors: FieldErrors = {}
  if (!values.name.trim()) {
    errors.name = 'Please enter your name.'
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email.trim())) {
    errors.email = 'Please enter a valid email address.'
  }
  if (values.message.trim().length < 10) {
    errors.message = 'Please enter a message of at least 10 characters.'
  }
  return errors
}

function ContactForm() {
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [message, setMessage] = useState('')
  const [company, setCompany] = useState('') // honeypot, real people leave this empty

  const [errors, setErrors] = useState<FieldErrors>({})
  const [formError, setFormError] = useState<string | null>(null)
  const [status, setStatus] = useState<'idle' | 'submitting' | 'success'>('idle')

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault()

    const nextErrors = validate({ name, email, message })
    setErrors(nextErrors)
    setFormError(null)
    if (Object.keys(nextErrors).length > 0) return

    setStatus('submitting')
    try {
      await api.post('/contact', { name, email, message, company })
      setStatus('success')
    } catch (err) {
      setStatus('idle')
      if (err instanceof ApiError && err.fields) {
        setErrors(err.fields as FieldErrors)
      } else if (err instanceof ApiError) {
        setFormError(err.message)
      } else {
        setFormError(contact.form.genericError)
      }
    }
  }

  if (status === 'success') {
    return (
      <div className="rounded-card border border-status-up/40 bg-status-up-bg p-6">
        <p className="flex items-center gap-2 font-medium text-status-up-text">
          <Icon name="fa-solid fa-circle-check" />
          {contact.form.successTitle}
        </p>
        <p className="mt-2 text-sm text-status-up-text">{contact.form.successBody}</p>
      </div>
    )
  }

  return (
    <form noValidate onSubmit={handleSubmit} className="space-y-5">
      <h2 className="text-h3">{contact.form.heading}</h2>

      {formError && (
        <p
          role="alert"
          className="rounded-lg border border-status-down/40 bg-status-down-bg px-3 py-2 text-sm text-status-down-text"
        >
          {formError}
        </p>
      )}

      <Field
        label={contact.form.name.label}
        name="name"
        value={name}
        onChange={setName}
        error={errors.name}
        placeholder={contact.form.name.placeholder}
        autoComplete="name"
        required
      />
      <Field
        label={contact.form.email.label}
        name="email"
        type="email"
        value={email}
        onChange={setEmail}
        error={errors.email}
        hint={contact.form.email.hint}
        placeholder={contact.form.email.placeholder}
        autoComplete="email"
        required
      />
      <Field
        label={contact.form.message.label}
        name="message"
        value={message}
        onChange={setMessage}
        error={errors.message}
        placeholder={contact.form.message.placeholder}
        multiline
        required
      />

      {/* Honeypot: hidden from people, tempting to bots. Kept out of the tab order. */}
      <div aria-hidden="true" className="hidden">
        <label htmlFor="contact-company">Company</label>
        <input
          id="contact-company"
          name="company"
          type="text"
          tabIndex={-1}
          autoComplete="off"
          value={company}
          onChange={(e) => setCompany(e.target.value)}
        />
      </div>

      <Button type="submit" disabled={status === 'submitting'}>
        {status === 'submitting' ? contact.form.submitting : contact.form.submit}
      </Button>
    </form>
  )
}

function ContactMethods() {
  return (
    <div>
      <h2 className="text-h3">{contact.directHeading}</h2>
      <ul className="mt-4 space-y-1">
        {profile.socials.map((social) => {
          const external = social.url.startsWith('http')
          return (
            <li key={social.label}>
              <a
                href={social.url}
                {...(external
                  ? { target: '_blank', rel: 'noopener noreferrer' }
                  : {})}
                className="group -mx-2 flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-teal/8"
              >
                <Icon
                  name={social.icon}
                  className="w-5 text-center text-teal"
                />
                <span className="min-w-0">
                  <span className="block text-sm font-medium text-ink">
                    {social.label}
                  </span>
                  <span className="block truncate text-sm text-ink-secondary group-hover:text-teal">
                    {social.handle}
                  </span>
                </span>
              </a>
            </li>
          )
        })}
      </ul>

      <p className="mt-6 text-sm text-ink-secondary">
        Based in {profile.location}.
      </p>
    </div>
  )
}
