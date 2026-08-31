import { useEffect, useRef } from 'react'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'
import { Input } from '@mantine/core'
import type { DeltaOp } from '@/lib/types'

// Kept in step with what src/components/DeltaContent.tsx can render — no image,
// video or formula buttons.
const TOOLBAR = [
  [{ header: [1, 2, false] }],
  ['bold', 'italic', 'underline', 'strike', 'code'],
  [{ list: 'ordered' }, { list: 'bullet' }],
  ['blockquote', 'code-block'],
  [{ align: [] }],
  ['link'],
  ['clean'],
]

function sameOps(a: DeltaOp[], b: DeltaOp[]): boolean {
  return JSON.stringify(a) === JSON.stringify(b)
}

/**
 * Controlled Quill editor producing delta ops (the format project descriptions
 * are stored in). Raw `quill@2` — `react-quill` doesn't support React 19.
 */
export function QuillEditor({
  value,
  onChange,
  error,
  label,
}: {
  value: DeltaOp[]
  onChange: (ops: DeltaOp[]) => void
  error?: string
  label?: string
}) {
  const hostRef = useRef<HTMLDivElement>(null)
  const quillRef = useRef<Quill | null>(null)
  const onChangeRef = useRef(onChange)
  onChangeRef.current = onChange

  // Init once. The ref guard survives StrictMode's double-mount.
  useEffect(() => {
    if (quillRef.current || !hostRef.current) return

    const quill = new Quill(hostRef.current, {
      theme: 'snow',
      modules: { toolbar: TOOLBAR },
    })
    quillRef.current = quill

    if (value.length > 0) {
      quill.setContents({ ops: value } as never, 'silent')
    }
    quill.on('text-change', () => {
      onChangeRef.current(quill.getContents().ops as DeltaOp[])
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  // Adopt external value changes (loading an existing project) without
  // stomping the caret while the user types.
  useEffect(() => {
    const quill = quillRef.current
    if (!quill) return
    if (!sameOps(quill.getContents().ops as DeltaOp[], value)) {
      quill.setContents({ ops: value } as never, 'silent')
    }
  }, [value])

  return (
    <Input.Wrapper label={label} error={error}>
      <div
        ref={hostRef}
        style={{ minHeight: 180 }}
        data-error={error ? 'true' : undefined}
      />
    </Input.Wrapper>
  )
}
