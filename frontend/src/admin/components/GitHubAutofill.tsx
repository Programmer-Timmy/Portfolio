import { useEffect, useRef } from 'react'
import { Alert, Badge, Group, Loader, Stack, Text } from '@mantine/core'
import { IconAlertTriangle } from '@tabler/icons-react'
import { ApiError } from '@/lib/api'
import { useGitHubContributors, useGitHubLanguages, useGitHubRepo } from '../hooks'
import { isGitHubRepoUrl } from '../lib/github'
import type { GitHubContributor, GitHubLanguage } from '../lib/types'

export type GitHubAutofillData = {
  private: boolean
  languages: GitHubLanguage[]
  contributors: GitHubContributor[]
}

/**
 * Watches a GitHub repo URL and, when it resolves to a repo the server token
 * can read (public or private), calls `onApply` once with the fetched
 * languages + contributors and the private flag. Missing repos show a hint so
 * the form can switch to manual entry. Pass a debounced, stable `onApply`.
 */
export function GitHubAutofill({
  url,
  onApply,
}: {
  url: string
  onApply: (data: GitHubAutofillData) => void
}) {
  const valid = isGitHubRepoUrl(url)
  const repo = useGitHubRepo(url)

  const exists = repo.data?.exists === true
  const isPrivate = repo.data?.exists === true && repo.data.private

  // The server-side GITHUB_TOKEN can read private repos it has access to, so
  // languages + contributors are fetched for those too.
  const languages = useGitHubLanguages(url, exists)
  const contributors = useGitHubContributors(url, exists)

  const appliedFor = useRef<string | null>(null)

  useEffect(() => {
    if (!valid || !exists || appliedFor.current === url) return

    if (languages.data && contributors.data) {
      appliedFor.current = url
      onApply({
        private: isPrivate,
        languages: languages.data.languages,
        contributors: contributors.data,
      })
    }
  }, [valid, exists, isPrivate, url, languages.data, contributors.data, onApply])

  if (!valid) return null

  const err = repo.error ?? languages.error ?? contributors.error
  if (err) {
    return (
      <Alert color="orange" icon={<IconAlertTriangle size={16} />} py="xs">
        {err instanceof ApiError || err instanceof Error
          ? err.message
          : "Couldn't reach GitHub."}{' '}
        Fill in languages and contributors manually.
      </Alert>
    )
  }

  const loading =
    repo.isFetching || (exists && (languages.isFetching || contributors.isFetching))
  if (loading) {
    return (
      <Group gap="xs">
        <Loader size="xs" />
        <Text fz="sm" c="dimmed">
          Checking GitHub…
        </Text>
      </Group>
    )
  }

  if (repo.data?.exists === false) {
    return (
      <Text fz="sm" c="dimmed">
        No public repository at that URL — add languages and contributors manually.
      </Text>
    )
  }

  if (exists && languages.data && contributors.data) {
    return (
      <Stack gap={2}>
        <Group gap="xs">
          {isPrivate && (
            <Badge color="gray" variant="light">
              Private
            </Badge>
          )}
          <Text fz="sm" c="teal">
            Filled from GitHub: {languages.data.languages.length} languages,{' '}
            {contributors.data.length} contributors.
          </Text>
        </Group>
        {languages.data.unmapped.length > 0 && (
          <Text fz="xs" c="dimmed">
            Not in the catalogue (counted as "Other"): {languages.data.unmapped.join(', ')}
          </Text>
        )}
      </Stack>
    )
  }

  return null
}
