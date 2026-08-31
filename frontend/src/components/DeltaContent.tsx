import type { ReactNode } from 'react'
import { cn } from '@/lib/cn'
import { TextLink } from '@/components/ui/TextLink'
import type { DeltaOp } from '@/lib/types'

/**
 * Renders a Quill Delta (the format project descriptions are stored in) as
 * React elements. Covers what the descriptions actually use: paragraphs,
 * headers, bullet / ordered lists, blockquotes, code blocks, text alignment,
 * and inline bold / italic / underline / strike / code / links.
 *
 * Embeds (images, video) are ignored here; the project gallery handles images.
 */

type Segment = { text: string; attrs: Record<string, unknown> }
type Line = { segments: Segment[]; block: Record<string, unknown> }

function buildLines(ops: DeltaOp[]): Line[] {
  const lines: Line[] = []
  let current: Segment[] = []

  const flush = (block: Record<string, unknown>) => {
    lines.push({ segments: current, block })
    current = []
  }

  for (const op of ops) {
    if (typeof op.insert !== 'string') continue
    const attrs = op.attributes ?? {}

    // A newline-only insert carries block formatting for the line it closes.
    if (op.insert.length > 0 && op.insert.replace(/\n/g, '') === '') {
      for (let n = 0; n < op.insert.length; n++) flush(attrs)
      continue
    }

    const parts = op.insert.split('\n')
    parts.forEach((part, i) => {
      if (part) current.push({ text: part, attrs })
      if (i < parts.length - 1) flush({})
    })
  }
  if (current.length > 0) flush({})
  return lines
}

function renderSegment(seg: Segment, key: number): ReactNode {
  const a = seg.attrs
  let node: ReactNode = seg.text

  if (a.code) {
    node = (
      <code className="rounded bg-line/60 px-1 py-0.5 font-mono text-[0.9em]">
        {node}
      </code>
    )
  }
  if (a.bold) node = <strong>{node}</strong>
  if (a.italic) node = <em>{node}</em>
  if (a.underline) node = <u>{node}</u>
  if (a.strike) node = <s>{node}</s>
  if (typeof a.link === 'string') node = <TextLink to={a.link}>{node}</TextLink>

  return <span key={key}>{node}</span>
}

function alignClass(align: unknown): string | undefined {
  if (align === 'center') return 'text-center'
  if (align === 'right') return 'text-right'
  if (align === 'justify') return 'text-justify'
  return undefined
}

export function DeltaContent({
  ops,
  className,
}: {
  ops: DeltaOp[]
  className?: string
}) {
  const lines = buildLines(ops)
  const blocks: ReactNode[] = []
  let i = 0
  let key = 0

  while (i < lines.length) {
    const { segments, block } = lines[i]
    const isEmpty =
      segments.length === 0 &&
      !block.header &&
      !block.list &&
      !block.blockquote &&
      !block['code-block']

    if (isEmpty) {
      i++
      continue
    }

    if (block.list === 'bullet' || block.list === 'ordered') {
      const listType = block.list
      const items: ReactNode[] = []
      while (i < lines.length && lines[i].block.list === listType) {
        items.push(
          <li key={key++}>
            {lines[i].segments.map((s, k) => renderSegment(s, k))}
          </li>,
        )
        i++
      }
      const cls = cn(
        'my-4 space-y-1 pl-5',
        listType === 'ordered' ? 'list-decimal' : 'list-disc',
      )
      blocks.push(
        listType === 'ordered' ? (
          <ol key={key++} className={cls}>
            {items}
          </ol>
        ) : (
          <ul key={key++} className={cls}>
            {items}
          </ul>
        ),
      )
      continue
    }

    if (block['code-block']) {
      const rows: string[] = []
      while (i < lines.length && lines[i].block['code-block']) {
        rows.push(lines[i].segments.map((s) => s.text).join(''))
        i++
      }
      blocks.push(
        <pre
          key={key++}
          className="my-4 overflow-x-auto rounded-lg border border-line bg-surface p-4 font-mono text-sm text-ink"
        >
          <code>{rows.join('\n')}</code>
        </pre>,
      )
      continue
    }

    const content = segments.map((s, k) => renderSegment(s, k))

    if (block.header) {
      // The project name is the page's h1, so headings inside the description
      // start at h2 (big) / h3 (everything else).
      const Tag = Number(block.header) <= 1 ? 'h2' : 'h3'
      blocks.push(
        <Tag key={key++} className="mt-8 mb-2 text-h3">
          {content}
        </Tag>,
      )
      i++
      continue
    }

    if (block.blockquote) {
      blocks.push(
        <blockquote
          key={key++}
          className="my-4 border-l-2 border-teal pl-4 italic"
        >
          {content}
        </blockquote>,
      )
      i++
      continue
    }

    blocks.push(
      <p key={key++} className={cn('my-4 leading-relaxed', alignClass(block.align))}>
        {content}
      </p>,
    )
    i++
  }

  return <div className={cn('text-ink-secondary', className)}>{blocks}</div>
}
