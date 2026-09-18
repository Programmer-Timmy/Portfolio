import { useState } from 'react'
import {
  ActionIcon,
  Alert,
  Anchor,
  Badge,
  Box,
  Button,
  Center,
  Group,
  Image,
  Loader,
  Stack,
  Switch,
  Table,
  Text,
  TextInput,
  Title,
  Tooltip,
} from '@mantine/core'
import { modals } from '@mantine/modals'
import { notifications } from '@mantine/notifications'
import {
  IconAlertTriangle,
  IconArrowBackUp,
  IconBrandYoutube,
  IconCheck,
  IconPencil,
  IconRefresh,
  IconTrash,
  IconX,
} from '@tabler/icons-react'
import { ApiError } from '@/lib/api'
import {
  useAdminVideos,
  useDeleteVideo,
  useRenameVideo,
  useRestoreVideo,
  useSyncVideos,
  useToggleVideoPin,
} from '../hooks'
import type { AdminVideo } from '../lib/types'

function formatDate(iso: string | null): string {
  if (!iso) return '-'
  const d = new Date(iso)
  return Number.isNaN(d.getTime())
    ? '-'
    : d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

function errorMessage(err: unknown): string {
  return err instanceof ApiError || err instanceof Error ? err.message : 'Something went wrong.'
}

export function VideosListPage() {
  const [showDeleted, setShowDeleted] = useState(false)
  const [editingId, setEditingId] = useState<number | null>(null)
  const [draft, setDraft] = useState('')

  const query = useAdminVideos(showDeleted)
  const sync = useSyncVideos()
  const togglePin = useToggleVideoPin()
  const rename = useRenameVideo()
  const del = useDeleteVideo()
  const restore = useRestoreVideo()

  function runSync() {
    sync.mutate(undefined, {
      onSuccess: (s) =>
        notifications.show({
          title: 'Synced with YouTube',
          message: `${s.added} added, ${s.updated} updated, ${s.deleted} removed.`,
          color: 'teal',
        }),
      onError: (err) =>
        notifications.show({ title: 'Sync failed', message: errorMessage(err), color: 'red' }),
    })
  }

  function startEdit(video: AdminVideo) {
    setEditingId(video.id)
    setDraft(video.title)
  }

  function saveEdit(video: AdminVideo) {
    const title = draft.trim()
    if (!title || title === video.title) {
      setEditingId(null)
      return
    }
    rename.mutate(
      { id: video.id, title },
      {
        onSuccess: () => {
          setEditingId(null)
          notifications.show({ message: 'Title updated.', color: 'teal' })
        },
        onError: (err) =>
          notifications.show({ title: 'Rename failed', message: errorMessage(err), color: 'red' }),
      },
    )
  }

  function confirmDelete(video: AdminVideo) {
    modals.openConfirmModal({
      title: 'Hide video',
      children: (
        <Text size="sm">
          Hide <strong>{video.title}</strong> from the site? The record stays and can be restored.
        </Text>
      ),
      labels: { confirm: 'Hide', cancel: 'Cancel' },
      confirmProps: { color: 'red' },
      onConfirm: () =>
        del.mutate(video.id, {
          onSuccess: () => notifications.show({ message: 'Video hidden.', color: 'gray' }),
          onError: (err) =>
            notifications.show({ title: 'Failed', message: errorMessage(err), color: 'red' }),
        }),
    })
  }

  return (
    <Stack gap="lg">
      <Group justify="space-between" align="flex-end" wrap="wrap">
        <div>
          <Title order={1} fz="h2">
            Videos
          </Title>
          <Text c="dimmed" fz="sm" mt={4}>
            Imported from YouTube. Pin the ones you want featured.
          </Text>
        </div>
        <Group gap="md">
          <Switch
            label="Show hidden"
            checked={showDeleted}
            onChange={(e) => setShowDeleted(e.currentTarget.checked)}
          />
          <Button
            leftSection={<IconRefresh size={16} />}
            loading={sync.isPending}
            onClick={runSync}
          >
            Sync from YouTube
          </Button>
        </Group>
      </Group>

      {query.isError && (
        <Alert color="red" icon={<IconAlertTriangle size={18} />} title="Couldn't load videos">
          {errorMessage(query.error)}
        </Alert>
      )}

      {query.isLoading && (
        <Center py="xl">
          <Loader />
        </Center>
      )}

      {query.data && (
        <Box style={{ overflowX: 'auto' }}>
          <Table striped highlightOnHover verticalSpacing="sm" miw={720}>
            <Table.Thead>
              <Table.Tr>
                <Table.Th w={90} />
                <Table.Th>Title</Table.Th>
                <Table.Th w={130}>Published</Table.Th>
                <Table.Th w={90}>Pinned</Table.Th>
                <Table.Th w={90} />
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {query.data.length === 0 && (
                <Table.Tr>
                  <Table.Td colSpan={5}>
                    <Text c="dimmed" ta="center" py="md">
                      No videos. Try "Sync from YouTube".
                    </Text>
                  </Table.Td>
                </Table.Tr>
              )}
              {query.data.map((video) => (
                <Table.Tr key={video.id} opacity={video.deleted ? 0.55 : 1}>
                  <Table.Td>
                    <Anchor href={video.url} target="_blank" rel="noreferrer">
                      <Image
                        src={video.thumbnailUrl}
                        alt=""
                        w={72}
                        h={40}
                        radius="sm"
                        fit="cover"
                        fallbackSrc="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'/%3E"
                      />
                    </Anchor>
                  </Table.Td>
                  <Table.Td>
                    {editingId === video.id ? (
                      <Group gap={4} wrap="nowrap">
                        <TextInput
                          size="xs"
                          flex={1}
                          value={draft}
                          autoFocus
                          onChange={(e) => setDraft(e.currentTarget.value)}
                          onKeyDown={(e) => {
                            if (e.key === 'Enter') saveEdit(video)
                            if (e.key === 'Escape') setEditingId(null)
                          }}
                        />
                        <ActionIcon
                          variant="subtle"
                          color="teal"
                          loading={rename.isPending}
                          onClick={() => saveEdit(video)}
                          aria-label="Save"
                        >
                          <IconCheck size={15} />
                        </ActionIcon>
                        <ActionIcon
                          variant="subtle"
                          color="gray"
                          onClick={() => setEditingId(null)}
                          aria-label="Cancel"
                        >
                          <IconX size={15} />
                        </ActionIcon>
                      </Group>
                    ) : (
                      <Group gap="xs" wrap="wrap">
                        <Text fw={500}>{video.title}</Text>
                        {video.deleted && (
                          <Badge size="xs" color="red" variant="light">
                            Hidden
                          </Badge>
                        )}
                      </Group>
                    )}
                  </Table.Td>
                  <Table.Td>
                    <Text fz="sm" c="dimmed">
                      {formatDate(video.publishedAt)}
                    </Text>
                  </Table.Td>
                  <Table.Td>
                    <Switch
                      checked={video.pinned}
                      onChange={() => togglePin.mutate(video)}
                      aria-label="Pinned"
                    />
                  </Table.Td>
                  <Table.Td>
                    <Group gap={4} justify="flex-end" wrap="nowrap">
                      {!video.deleted && editingId !== video.id && (
                        <Tooltip label="Rename">
                          <ActionIcon
                            variant="subtle"
                            color="gray"
                            onClick={() => startEdit(video)}
                          >
                            <IconPencil size={15} />
                          </ActionIcon>
                        </Tooltip>
                      )}
                      {video.deleted ? (
                        <Tooltip label="Restore">
                          <ActionIcon
                            variant="subtle"
                            color="teal"
                            loading={restore.isPending && restore.variables === video.id}
                            onClick={() =>
                              restore.mutate(video.id, {
                                onSuccess: () =>
                                  notifications.show({ message: 'Video restored.', color: 'teal' }),
                              })
                            }
                          >
                            <IconArrowBackUp size={15} />
                          </ActionIcon>
                        </Tooltip>
                      ) : (
                        <Tooltip label="Hide">
                          <ActionIcon
                            variant="subtle"
                            color="red"
                            loading={del.isPending && del.variables === video.id}
                            onClick={() => confirmDelete(video)}
                          >
                            <IconTrash size={15} />
                          </ActionIcon>
                        </Tooltip>
                      )}
                    </Group>
                  </Table.Td>
                </Table.Tr>
              ))}
            </Table.Tbody>
          </Table>
        </Box>
      )}

      <Text fz="xs" c="dimmed">
        <IconBrandYoutube size={12} style={{ verticalAlign: -1 }} /> New uploads appear after a sync.
      </Text>
    </Stack>
  )
}
