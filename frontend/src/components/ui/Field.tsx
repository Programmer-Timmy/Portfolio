import { useId } from 'react'
import { cn } from '@/lib/cn'

type FieldProps = {
  label: string
  name: string
  value: string
  onChange: (value: string) => void
  /** Input type; ignored when `multiline`. */
  type?: string
  error?: string
  /** Small helper text shown under the label. */
  hint?: string
  required?: boolean
  placeholder?: string
  autoComplete?: string
  /** Render a <textarea> instead of an <input>. */
  multiline?: boolean
  rows?: number
  className?: string
}

/**
 * A labelled form control (input or textarea) with hint and error text, wired
 * up for screen readers. Fully controlled: pass `value` and get the next value
 * back from `onChange`.
 */
export function Field({
  label,
  name,
  value,
  onChange,
  type = 'text',
  error,
  hint,
  required,
  placeholder,
  autoComplete,
  multiline,
  rows = 6,
  className,
}: FieldProps) {
  const id = useId()
  const hintId = hint ? `${id}-hint` : undefined
  const errorId = error ? `${id}-error` : undefined
  const describedBy = [hintId, errorId].filter(Boolean).join(' ') || undefined

  const control = cn(
    'mt-1.5 block w-full rounded-lg border bg-surface px-3 py-2 text-ink shadow-card',
    'placeholder:text-ink-muted',
    error ? 'border-status-down' : 'border-line',
    multiline && 'resize-y',
  )

  return (
    <div className={className}>
      <label htmlFor={id} className="text-sm font-medium text-ink">
        {label}
        {required && (
          <span className="text-ink-muted" aria-hidden="true">
            {' '}
            *
          </span>
        )}
      </label>
      {hint && (
        <p id={hintId} className="mt-1 text-xs text-ink-secondary">
          {hint}
        </p>
      )}
      {multiline ? (
        <textarea
          id={id}
          name={name}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          required={required}
          placeholder={placeholder}
          rows={rows}
          aria-invalid={error ? true : undefined}
          aria-describedby={describedBy}
          className={control}
        />
      ) : (
        <input
          id={id}
          name={name}
          type={type}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          required={required}
          placeholder={placeholder}
          autoComplete={autoComplete}
          aria-invalid={error ? true : undefined}
          aria-describedby={describedBy}
          className={control}
        />
      )}
      {error && (
        <p id={errorId} className="mt-1 text-xs text-status-down">
          {error}
        </p>
      )}
    </div>
  )
}
