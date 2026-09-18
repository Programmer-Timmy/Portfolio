import { useNavigate } from 'react-router-dom'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { notifications } from '@mantine/notifications'
import { Alert, Button, Group, Stack, Text, TextInput, Title } from '@mantine/core'
import { IconAlertTriangle, IconArrowLeft } from '@tabler/icons-react'
import { ApiError } from '@/lib/api'
import { useAddOpenSource } from '../hooks'
import { openSourceSchema, type OpenSourceValues } from '../lib/schemas'

export function OpenSourceAddPage() {
  const navigate = useNavigate()
  const add = useAddOpenSource()

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors },
  } = useForm<OpenSourceValues>({
    resolver: zodResolver(openSourceSchema),
    defaultValues: { repoUrl: '', username: 'Programmer-Timmy' },
  })

  const onSubmit = handleSubmit(async (values) => {
    try {
      const project = await add.mutateAsync(values)
      notifications.show({
        title: 'Repository added',
        message: `${project.name} — ${project.pullRequestCount ?? 0} pull requests imported.`,
        color: 'teal',
      })
      navigate('/admin/opensource')
    } catch (err) {
      if (err instanceof ApiError && err.fields) {
        for (const [field, message] of Object.entries(err.fields)) {
          setError(field as keyof OpenSourceValues, { message })
        }
      } else {
        setError('root', {
          message: err instanceof ApiError || err instanceof Error ? err.message : 'Try again.',
        })
      }
    }
  })

  return (
    <form onSubmit={onSubmit} noValidate>
      <Stack gap="lg" maw={520}>
        <div>
          <Button
            variant="subtle"
            size="compact-sm"
            px={0}
            leftSection={<IconArrowLeft size={14} />}
            onClick={() => navigate('/admin/opensource')}
          >
            Open source
          </Button>
          <Title order={1} fz="h2">
            Add repository
          </Title>
          <Text c="dimmed" fz="sm" mt={4}>
            We'll fetch the repo details and import every pull request the given user authored there.
          </Text>
        </div>

        {errors.root?.message && (
          <Alert color="red" icon={<IconAlertTriangle size={18} />} py="xs">
            {errors.root.message}
          </Alert>
        )}

        <TextInput
          label="Repository URL"
          placeholder="https://github.com/owner/repo"
          error={errors.repoUrl?.message}
          {...register('repoUrl')}
        />
        <TextInput
          label="GitHub username"
          description="Whose pull requests to import"
          error={errors.username?.message}
          {...register('username')}
        />

        <Group>
          <Button type="submit" loading={add.isPending}>
            Add &amp; import
          </Button>
        </Group>
      </Stack>
    </form>
  )
}
