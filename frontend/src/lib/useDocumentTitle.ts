import { useEffect } from 'react'

const SUFFIX = 'Tim van der Kloet'

/** Sets `document.title` for the lifetime of the component. */
export function useDocumentTitle(title?: string) {
  useEffect(() => {
    document.title = title ? `${title} | ${SUFFIX}` : `${SUFFIX} | Web Development & Technology`
  }, [title])
}
