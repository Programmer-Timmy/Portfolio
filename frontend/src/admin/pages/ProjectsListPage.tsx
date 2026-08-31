import { useState } from 'react'
import {
  ActionIcon,
  Alert,
  Badge,
  Box,
  Button,
  Center,
  Group,
  Loader,
  Stack,
  Switch,
  Table,
  Text,
  Title,
  Tooltip,
} from '@mantine/core'
import { modals } from '@mantine/modals'
import { notifications } from '@mantine/notifications'
import {
  IconAlertTriangle,
  IconArrowBackUp,
  IconBrandGithub,
  IconExternalLink,
  IconLock,
  IconPencil,
  IconPin,
  IconPlus,
  IconProgress,
  IconTrash,
} from '@tabler/icons-react'
import { ApiError } from '@/lib/api'
import { useAdminProjects, useDeleteProject, useRestoreProject } from '../hooks'
import type { ProjectAdminRow } from '../lib/types'

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

export function ProjectsListPage() {
  const [showDeleted, setShowDeleted] = useState(false)
  const query = useAdminProjects(showDeleted)
  const del = useDeleteProject()
  const restore = useRestoreProject()

  function confirmDelete(project: ProjectAdminRow) {
    modals.openConfirmModal({
      title: 'Delete project',
      children: (
        <Text size="sm">
          Hide <strong>{project.name}</strong> from the site? It stays in the database and can be
          restored from the deleted list.
        </Text>
      ),
      labels: { confirm: 'Delete', cancel: 'Cancel' },
      confirmProps: { color: 'red' },
      onConfirm: () =>
        del.mutate(project.id, {
          onSuccess: () =>
            notifications.show({ message: `"${project.name}" deleted.`, color: 'gray' }),
          onError: (err) =>
            notifications.show({ title: 'Delete failed', message: errorMessage(err), color: 'red' }),
        }),
    })
  }

  function handleRestore(project: ProjectAdminRow) {
    restore.mutate(project.id, {
      onSuccess: () =>
        notifications.show({ message: `"${project.name}" restored.`, color: 'teal' }),
      onError: (err) =>
        notifications.show({ title: 'Restore failed', message: errorMessage(err), color: 'red' }),
    })
  }

  return (
    <Stack gap="lg">
      <Group justify="space-between" align="flex-end" wrap="wrap">
        <div>
          <Title order={1} fz="h2">
            Projects
          </Title>
          <Text c="dimmed" fz="sm" mt={4}>
            {query.data ? `${query.data.length} shown` : ' '}
          </Text>
        </div>
        <Group gap="md">
          <Switch
            label="Show deleted"
            checked={showDeleted}
            onChange={(e) => setShowDeleted(e.currentTarget.checked)}
          />
          <Tooltip label="Adding and editing projects arrives in the next step">
            <Button leftSection={<IconPlus size={16} />} disabled>
              Add project
            </Button>
          </Tooltip>
        </Group>
      </Group>

      {query.isError && (
        <Alert color="red" icon={<IconAlertTriangle size={18} />} title="Couldn't load projects">
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
                <Table.Th>Name</Table.Th>
                <Table.Th>Links</Table.Th>
                <Table.Th>Updated</Table.Th>
                <Table.Th w={120} />
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {query.data.length === 0 && (
                <Table.Tr>
                  <Table.Td colSpan={4}>
                    <Text c="dimmed" ta="center" py="md">
                      {showDeleted ? 'No projects.' : 'No projects. (Toggle "Show deleted" to see removed ones.)'}
                    </Text>
                  </Table.Td>
                </Table.Tr>
              )}
              {query.data.map((project) => (
                <Table.Tr key={project.id} opacity={project.removed ? 0.55 : 1}>
                  <Table.Td>
                    <Group gap="xs" wrap="wrap">
                      <Text fw={500}>{project.name}</Text>
                      {project.flags.pinned && (
                        <Badge size="xs" variant="light" leftSection={<IconPin size={11} />}>
                          Pinned
                        </Badge>
                      )}
                      {project.flags.inProgress && (
                        <Badge
                          size="xs"
                          color="yellow"
                          variant="light"
                          leftSection={<IconProgress size={11} />}
                        >
                          In progress
                        </Badge>
                      )}
                      {project.flags.privateRepo && (
                        <Badge
                          size="xs"
                          color="gray"
                          variant="light"
                          leftSection={<IconLock size={11} />}
                        >
                          Private
                        </Badge>
                      )}
                      {project.removed && (
                        <Badge size="xs" color="red" variant="light">
                          Deleted
                        </Badge>
                      )}
                    </Group>
                  </Table.Td>
                  <Table.Td>
                    <Group gap="xs">
                      {project.links.repository && (
                        <Tooltip label={project.links.repository}>
                          <ActionIcon
                            component="a"
                            href={project.links.repository}
                            target="_blank"
                            rel="noreferrer"
                            variant="subtle"
                            color="gray"
                          >
                            <IconBrandGithub size={16} />
                          </ActionIcon>
                        </Tooltip>
                      )}
                      {project.links.live && (
                        <Tooltip label={project.links.live}>
                          <ActionIcon
                            component="a"
                            href={project.links.live}
                            target="_blank"
                            rel="noreferrer"
                            variant="subtle"
                            color="gray"
                          >
                            <IconExternalLink size={16} />
                          </ActionIcon>
                        </Tooltip>
                      )}
                      {!project.links.repository && !project.links.live && (
                        <Text c="dimmed" fz="sm">
                          -
                        </Text>
                      )}
                    </Group>
                  </Table.Td>
                  <Table.Td>
                    <Text fz="sm" c="dimmed">
                      {formatDate(project.updatedAt)}
                    </Text>
                  </Table.Td>
                  <Table.Td>
                    <Group gap={4} justify="flex-end" wrap="nowrap">
                      <Tooltip label="Editing arrives in the next step">
                        <ActionIcon variant="subtle" color="gray" disabled>
                          <IconPencil size={16} />
                        </ActionIcon>
                      </Tooltip>
                      {project.removed ? (
                        <Tooltip label="Restore">
                          <ActionIcon
                            variant="subtle"
                            color="teal"
                            loading={restore.isPending && restore.variables === project.id}
                            onClick={() => handleRestore(project)}
                          >
                            <IconArrowBackUp size={16} />
                          </ActionIcon>
                        </Tooltip>
                      ) : (
                        <Tooltip label="Delete">
                          <ActionIcon
                            variant="subtle"
                            color="red"
                            loading={del.isPending && del.variables === project.id}
                            onClick={() => confirmDelete(project)}
                          >
                            <IconTrash size={16} />
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
    </Stack>
  )
}
