import { Link } from 'react-router-dom'
import {
  ActionIcon,
  Alert,
  Anchor,
  Box,
  Button,
  Center,
  Group,
  Loader,
  Stack,
  Table,
  Text,
  Title,
  Tooltip,
} from '@mantine/core'
import { modals } from '@mantine/modals'
import { notifications } from '@mantine/notifications'
import { IconAlertTriangle, IconBrandGithub, IconPlus, IconTrash } from '@tabler/icons-react'
import { ApiError } from '@/lib/api'
import { useAdminOpenSource, useDeleteOpenSource } from '../hooks'
import type { AdminOpenSourceProject } from '../lib/types'

function errorMessage(err: unknown): string {
  return err instanceof ApiError || err instanceof Error ? err.message : 'Something went wrong.'
}

export function OpenSourceListPage() {
  const query = useAdminOpenSource()
  const del = useDeleteOpenSource()

  function confirmDelete(project: AdminOpenSourceProject) {
    modals.openConfirmModal({
      title: 'Remove repository',
      children: (
        <Text size="sm">
          Remove <strong>{project.name}</strong> and its imported pull requests? This can't be
          undone (re-add it to pull them back in).
        </Text>
      ),
      labels: { confirm: 'Remove', cancel: 'Cancel' },
      confirmProps: { color: 'red' },
      onConfirm: () =>
        del.mutate(project.id, {
          onSuccess: () =>
            notifications.show({ message: `"${project.name}" removed.`, color: 'gray' }),
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
            Open source
          </Title>
          <Text c="dimmed" fz="sm" mt={4}>
            Repositories you've contributed to. Pull requests are imported from GitHub.
          </Text>
        </div>
        <Button component={Link} to="/admin/opensource/new" leftSection={<IconPlus size={16} />}>
          Add repository
        </Button>
      </Group>

      {query.isError && (
        <Alert color="red" icon={<IconAlertTriangle size={18} />} title="Couldn't load repositories">
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
          <Table striped highlightOnHover verticalSpacing="sm" miw={640}>
            <Table.Thead>
              <Table.Tr>
                <Table.Th>Repository</Table.Th>
                <Table.Th w={110}>PRs</Table.Th>
                <Table.Th w={60} />
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {query.data.length === 0 && (
                <Table.Tr>
                  <Table.Td colSpan={3}>
                    <Text c="dimmed" ta="center" py="md">
                      No repositories yet.
                    </Text>
                  </Table.Td>
                </Table.Tr>
              )}
              {query.data.map((project) => (
                <Table.Tr key={project.id}>
                  <Table.Td>
                    <Group gap={6} wrap="nowrap">
                      <IconBrandGithub size={15} />
                      {project.repositoryUrl ? (
                        <Anchor href={project.repositoryUrl} target="_blank" rel="noreferrer" fw={500}>
                          {project.name}
                        </Anchor>
                      ) : (
                        <Text fw={500}>{project.name}</Text>
                      )}
                    </Group>
                    {project.description && (
                      <Text fz="xs" c="dimmed" mt={2} lineClamp={1}>
                        {project.description}
                      </Text>
                    )}
                  </Table.Td>
                  <Table.Td>
                    <Text fz="sm">{project.pullRequestCount ?? 0}</Text>
                  </Table.Td>
                  <Table.Td>
                    <Tooltip label="Remove">
                      <ActionIcon
                        variant="subtle"
                        color="red"
                        loading={del.isPending && del.variables === project.id}
                        onClick={() => confirmDelete(project)}
                      >
                        <IconTrash size={16} />
                      </ActionIcon>
                    </Tooltip>
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
