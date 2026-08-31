import { QueryClient } from '@tanstack/react-query'

/**
 * One QueryClient for the whole admin subtree. Mounted by AdminApp inside a
 * QueryClientProvider, so nothing on the public site touches it.
 */
export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 30_000,
      retry: 1,
      refetchOnWindowFocus: false,
    },
  },
})
