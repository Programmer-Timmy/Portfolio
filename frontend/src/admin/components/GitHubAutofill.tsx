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
 * Watches a GitHub repo URL and, when it resolves to a public repo, calls
 * `onApply` once with the fetched languages + contributors (and the private
 * flag). Private / missing repos still report their state so the form can
 * switch to manual entry. Pass a debounced, stable `onApply`.
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
  const isPublic = exists && !isPrivate

  const languages = useGitHubLanguages(url, isPublic)
  const contributors = useGitHubContributors(url, isPublic)

  const appliedFor = useRef<string | null>(null)

  useEffect(() => {
    if (!valid || !exists || appliedFor.current === url) return

    if (isPrivate) {
      appliedFor.current = url
      onApply({ private: true, languages: [], contributors: [] })
      return
    }
    if (languages.data && contributors.data) {
      appliedFor.current = url
      onApply({
        private: false,
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
    repo.isFetching || (isPublic && (languages.isFetching || contributors.isFetching))
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

  if (isPrivate) {
    return (
      <Group gap="xs">
        <Badge color="gray" variant="light">
          Private
        </Badge>
        <Text fz="sm" c="dimmed">
          Private repo — languages and contributors won't autofill.
        </Text>
      </Group>
    )
  }

  if (isPublic && languages.data && contributors.data) {
    return (
      <Stack gap={2}>
        <Text fz="sm" c="teal">
          Filled from GitHub: {languages.data.languages.length} languages,{' '}
          {contributors.data.length} contributors.
        </Text>
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
