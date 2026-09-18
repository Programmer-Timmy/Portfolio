import { Alert, Card, Group, SimpleGrid, Skeleton, Stack, Text, Title } from '@mantine/core'
import { IconAlertTriangle } from '@tabler/icons-react'
import { useStats } from '../hooks'
import type { AdminStats } from '../lib/types'

const TILES: { key: keyof AdminStats; label: string }[] = [
  { key: 'projects', label: 'Projects' },
  { key: 'projectsPinned', label: 'Pinned' },
  { key: 'projectsInProgress', label: 'In progress' },
  { key: 'videos', label: 'Videos' },
  { key: 'openSourceProjects', label: 'Open-source repos' },
  { key: 'pullRequests', label: 'Pull requests' },
]

export function DashboardPage() {
  const { data, isLoading, isError, error } = useStats()

  return (
    <Stack gap="lg">
      <div>
        <Title order={1} fz="h2">
          Dashboard
        </Title>
        <Text c="dimmed" fz="sm" mt={4}>
          An overview of what's published on the site.
        </Text>
      </div>

      {isError && (
        <Alert
          color="red"
          icon={<IconAlertTriangle size={18} />}
          title="Couldn't load the stats"
        >
          {error instanceof Error ? error.message : 'Something went wrong.'}
        </Alert>
      )}

      <SimpleGrid cols={{ base: 1, xs: 2, md: 3 }} spacing="md">
        {TILES.map((tile) => (
          <Card key={tile.key} withBorder radius="md" padding="lg">
            <Text fz="xs" tt="uppercase" fw={600} c="dimmed">
              {tile.label}
            </Text>
            {isLoading ? (
              <Skeleton height={34} width={64} mt="sm" />
            ) : (
              <Group align="baseline" mt={4}>
                <Text fz={34} fw={700} ff="heading" lh={1}>
                  {data ? data[tile.key] : '-'}
                </Text>
              </Group>
            )}
          </Card>
        ))}
      </SimpleGrid>
    </Stack>
  )
}
