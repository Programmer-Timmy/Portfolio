/**
 * Static content for the Contact page. Page copy and the contact-form strings
 * live here; the list of ways to reach me directly is read from
 * `profile.socials` (src/content/profile.ts), so there's one place to edit it.
 *
 * The form posts to `POST /api/contact` (name, email, message + a honeypot).
 * Field rules are enforced by the API too; see private/api/Controllers/ContactApi.php.
 */

export const contact = {
  eyebrow: 'Contact',
  title: 'Get in touch',

  /** Intro paragraphs shown above the form. */
  lead: [
    "Questions, feedback, or an idea you want built: I'm happy to hear it. Fill in the " +
      'form and it lands straight in my inbox, or reach me directly through any of the ' +
      'channels listed here.',
  ],

  form: {
    heading: 'Send a message',
    name: {
      label: 'Your name',
      placeholder: 'Jane Doe',
    },
    email: {
      label: 'Email',
      placeholder: 'you@example.com',
      hint: "So I can reply. I won't use it for anything else.",
    },
    message: {
      label: 'Message',
      placeholder: 'What would you like to talk about?',
    },
    submit: 'Send message',
    submitting: 'Sending...',
    /** Shown in place of the form after a successful send. */
    successTitle: 'Message sent',
    successBody:
      "Thanks for reaching out! I'll get back to you as soon as I can. In the meantime, feel free to check out my projects or follow me on social media.",
    /** Fallback when the API returns an error without per-field details. */
    genericError:
      'Something went wrong sending your message. Please try again in a moment, or email me directly.',
  },

  directHeading: 'Reach me directly',
}
