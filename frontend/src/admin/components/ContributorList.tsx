import { useState } from 'react'
import {
  ActionIcon,
  Avatar,
  Button,
  Group,
  Stack,
  Text,
  TextInput,
} from '@mantine/core'
import { notifications } from '@mantine/notifications'
import { IconTrash, IconUserPlus } from '@tabler/icons-react'
import { useFieldArray, type Control } from 'react-hook-form'
import { ApiError } from '@/lib/api'
import { useGitHubUserLookup } from '../hooks'
import type { ProjectFormValues } from '../lib/schemas'

export function ContributorList({ control }: { control: Control<ProjectFormValues> }) {
  const { fields, append, remove } = useFieldArray({
    control,
    name: 'contributors',
    keyName: 'fieldId',
  })
  const lookup = useGitHubUserLookup()
  const [login, setLogin] = useState('')

  async function add() {
    const handle = login.trim()
    if (!handle) return
    try {
      const user = await lookup.mutateAsync(handle)
      if (fields.some((f) => f.id === user.id)) {
        notifications.show({ message: `@${user.login} is already listed.`, color: 'gray' })
        return
      }
      append({
        id: user.id,
        login: user.login,
        avatarUrl: user.avatarUrl,
        profileUrl: user.profileUrl,
        contributions: 1,
      })
      setLogin('')
    } catch (err) {
      notifications.show({
        title: "Couldn't add contributor",
        message: err instanceof ApiError || err instanceof Error ? err.message : 'Try again.',
        color: 'red',
      })
    }
  }

  return (
    <Stack gap="xs">
      {fields.length === 0 && (
        <Text fz="sm" c="dimmed">
          No contributors. Add a public GitHub repo above to pull them in, or add by username.
        </Text>
      )}

      {fields.map((field, index) => (
        <Group key={field.fieldId} gap="sm" wrap="nowrap">
          <Avatar src={field.avatarUrl ?? undefined} size="sm" radius="xl">
            {(field.login ?? '?').slice(0, 2)}
          </Avatar>
          <Text fz="sm" flex={1}>
            {field.login ?? `user ${field.id}`}
          </Text>
          <Text fz="xs" c="dimmed">
            {field.contributions} commits
          </Text>
          <ActionIcon
            variant="subtle"
            color="red"
            onClick={() => remove(index)}
            aria-label="Remove contributor"
          >
            <IconTrash size={16} />
          </ActionIcon>
        </Group>
      ))}

      <Group gap="xs" wrap="nowrap">
        <TextInput
          flex={1}
          size="xs"
          placeholder="GitHub username"
          value={login}
          onChange={(e) => setLogin(e.currentTarget.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter') {
              e.preventDefault()
              void add()
            }
          }}
        />
        <Button
          size="xs"
          variant="light"
          leftSection={<IconUserPlus size={14} />}
          loading={lookup.isPending}
          onClick={() => void add()}
        >
          Add
        </Button>
      </Group>
    </Stack>
  )
}
