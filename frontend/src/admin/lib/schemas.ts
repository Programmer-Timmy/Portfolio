import { z } from 'zod'
import type { DeltaOp } from '@/lib/types'

export const loginSchema = z.object({
  username: z.string().min(1, 'Enter your username.'),
  password: z.string().min(1, 'Enter your password.'),
})

export type LoginValues = z.infer<typeof loginSchema>

// --- project form ----------------------------------------------------------

const GITHUB_REPO = /^https?:\/\/(www\.)?github\.com\/[\w.-]+\/[\w.-]+\/?$/i

/** A Quill delta has text once the concatenated inserts are non-blank. */
export function deltaHasText(ops: unknown): boolean {
  if (!Array.isArray(ops)) return false
  let text = ''
  for (const op of ops) {
    if (op && typeof op === 'object' && typeof (op as DeltaOp).insert === 'string') {
      text += (op as DeltaOp).insert as string
    }
  }
  return text.trim().length > 0
}

const contributorSchema = z.object({
  id: z.number().int(),
  login: z.string().nullable(),
  avatarUrl: z.string().nullable(),
  profileUrl: z.string().nullable(),
  contributions: z.number().int().nonnegative(),
})

export const projectFormSchema = z
  .object({
    name: z
      .string()
      .trim()
      .min(1, 'Enter a title.')
      .max(20, 'Keep the title to 20 characters or fewer.'),
    link: z.union([z.literal(''), z.string().url('Enter a valid URL (including https://).')]),
    github: z.union([
      z.literal(''),
      z.string().regex(GITHUB_REPO, 'Enter a GitHub repository URL.'),
    ]),
    pinned: z.boolean(),
    inProgress: z.boolean(),
    privateRepo: z.boolean().nullable(),
    description: z.array(z.any()).refine(deltaHasText, 'Add a description.'),
    languages: z.array(
      z.object({
        programmingLanguageId: z.number().int().positive('Pick a language.'),
        percentage: z.coerce.number().min(0).max(100),
      }),
    ),
    contributors: z.array(contributorSchema),
    existingImages: z.array(z.string()),
    removedImages: z.array(z.string()),
    newFiles: z.array(z.instanceof(File)),
  })
  .refine((v) => v.existingImages.length + v.newFiles.length > 0, {
    path: ['newFiles'],
    message: 'Add at least one image.',
  })

export type ProjectFormValues = z.input<typeof projectFormSchema>
export type ProjectFormOutput = z.output<typeof projectFormSchema>
