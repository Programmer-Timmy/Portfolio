import { useState } from 'react'
import { Alert, Button, Card, Group, Loader, Select, Stack, Table, Text, TextInput, Title } from '@mantine/core'
import { notifications } from '@mantine/notifications'
import { ApiError } from '@/lib/api'
import { useAdminStatusPage, useAdminStatusSlugs, useSaveStatus } from '../hooks'
import type { AdminStatusPage, StatusAudience } from '../lib/types'

const AUDIENCES = [
  { value: 'hidden', label: 'Hidden' },
  { value: 'private', label: 'Home network and admin' },
  { value: 'public', label: 'Everyone' },
]

function SlugEditor({ data }: { data: AdminStatusPage }) {
  const save = useSaveStatus(data.slug)
  const [page, setPage] = useState<StatusAudience>(data.audience)
  const [monitors, setMonitors] = useState(
    Object.fromEntries(data.groups.flatMap((g) => g.monitors.map((m) => [m.id, m.audience]))),
  )

  const onSave = () =>
    save.mutate(
      { page, monitors },
      {
        onSuccess: () => notifications.show({ message: `${data.title} saved`, color: 'teal' }),
        onError: (err) =>
          notifications.show({ message: err instanceof ApiError ? err.message : 'Saving failed.', color: 'red' }),
      },
    )

  return (
    <Card withBorder>
      <Stack>
        <Group justify="space-between">
          <Title order={4}>
            {data.title} <Text span c="dimmed">({data.slug})</Text>
          </Title>
          <Button onClick={onSave} loading={save.isPending}>Save</Button>
        </Group>
        <Select
          label="Status page"
          data={AUDIENCES}
          value={page}
          onChange={(v) => setPage((v as StatusAudience) ?? 'hidden')}
          allowDeselect={false}
        />
        {page !== 'hidden' && (
          <Table>
            <Table.Tbody>
              {data.groups.flatMap((g) =>
                g.monitors.map((m) => (
                  <Table.Tr key={m.id}>
                    <Table.Td>
                      {g.name} / {m.name}
                    </Table.Td>
                    <Table.Td>
                      <Select
                        data={AUDIENCES}
                        value={monitors[m.id]}
                        onChange={(v) => setMonitors({ ...monitors, [m.id]: (v as StatusAudience) ?? 'hidden' })}
                        allowDeselect={false}
                      />
                    </Table.Td>
                  </Table.Tr>
                )),
              )}
            </Table.Tbody>
          </Table>
        )}
      </Stack>
    </Card>
  )
}

function Slug({ slug }: { slug: string }) {
  const query = useAdminStatusPage(slug)

  if (query.isPending) return <Loader size="sm" />
  if (query.isError) return <Alert color="red" title={slug}>{query.error.message}</Alert>
  return <SlugEditor data={query.data} />
}

export function StatusPage() {
  const slugs = useAdminStatusSlugs()
  const [added, setAdded] = useState<string[]>([])
  const [draft, setDraft] = useState('')
  const all = [...new Set([...(slugs.data ?? []), ...added])]

  const add = () => {
    if (draft.trim()) setAdded([...added, draft.trim()])
    setDraft('')
  }

  return (
    <Stack>
      <Title order={2}>Status</Title>
      <Text c="dimmed">Kuma has no public list of status pages, so add each slug here. Anything not allowed stays hidden.</Text>
      <Group align="end">
        <TextInput label="Kuma status page slug" value={draft} onChange={(e) => setDraft(e.currentTarget.value)} />
        <Button onClick={add}>Add</Button>
      </Group>
      {all.map((slug) => (
        <Slug key={slug} slug={slug} />
      ))}
    </Stack>
  )
}
