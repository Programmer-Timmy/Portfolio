import { useCallback, useEffect, useMemo, useRef } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { zodResolver } from '@hookform/resolvers/zod'
import { Controller, useForm } from 'react-hook-form'
import { useDebouncedValue } from '@mantine/hooks'
import { notifications } from '@mantine/notifications'
import {
  Alert,
  Button,
  Center,
  Divider,
  Group,
  Loader,
  Stack,
  Switch,
  Text,
  TextInput,
  Title,
} from '@mantine/core'
import { IconAlertTriangle, IconArrowLeft, IconDeviceFloppy } from '@tabler/icons-react'
import { ApiError } from '@/lib/api'
import { QuillEditor } from '../components/QuillEditor'
import { LanguageRows } from '../components/LanguageRows'
import { ContributorList } from '../components/ContributorList'
import { ImageManager } from '../components/ImageManager'
import { GitHubAutofill, type GitHubAutofillData } from '../components/GitHubAutofill'
import { useCreateProject, useProject, useUpdateProject } from '../hooks'
import {
  projectFormSchema,
  type ProjectFormOutput,
  type ProjectFormValues,
} from '../lib/schemas'
import type { DeltaOp, ProjectEditable } from '../lib/types'

const EMPTY: ProjectFormValues = {
  name: '',
  link: '',
  github: '',
  pinned: false,
  inProgress: false,
  privateRepo: null,
  description: [],
  languages: [],
  contributors: [],
  existingImages: [],
  removedImages: [],
  newFiles: [],
}

function toFormValues(p: ProjectEditable): ProjectFormValues {
  return {
    name: p.name,
    link: p.link,
    github: p.github,
    pinned: p.flags.pinned,
    inProgress: p.flags.inProgress,
    privateRepo: p.flags.privateRepo,
    description: p.description ?? [],
    languages: p.languages.map((l) => ({
      programmingLanguageId: l.programmingLanguageId,
      percentage: l.percentage ?? 0,
    })),
    contributors: p.contributors,
    existingImages: p.imagePaths,
    removedImages: [],
    newFiles: [],
  }
}

export function ProjectFormPage() {
  const { id } = useParams<{ id: string }>()
  const isEdit = !!id && id !== 'new'
  const navigate = useNavigate()

  const project = useProject(id)
  const create = useCreateProject()
  const update = useUpdateProject(id ?? '')

  const form = useForm<ProjectFormValues, unknown, ProjectFormOutput>({
    resolver: zodResolver(projectFormSchema),
    defaultValues: EMPTY,
  })
  const {
    control,
    register,
    handleSubmit,
    reset,
    setValue,
    setError,
    watch,
    formState: { errors },
  } = form

  useEffect(() => {
    if (project.data) reset(toFormValues(project.data))
  }, [project.data, reset])

  const githubTouched = useRef(false)
  const githubReg = register('github')

  const github = watch('github')
  const [debouncedGithub] = useDebouncedValue(github, 500)

  const thumbs = useMemo<Record<string, string>>(() => {
    const map: Record<string, string> = {}
    project.data?.imagePaths.forEach((path, i) => {
      const img = project.data?.images[i]
      if (img) map[path] = img.src
    })
    return map
  }, [project.data])

  const onGitHubApply = useCallback(
    (data: GitHubAutofillData) => {
      // On the edit screen, don't overwrite saved languages/contributors unless
      // the user actually changed the repo URL.
      if (isEdit && !githubTouched.current) return

      setValue('privateRepo', data.private)
      if (!data.private && data.languages.length > 0) {
        setValue(
          'languages',
          data.languages.map((l) => ({
            programmingLanguageId: l.programmingLanguageId,
            percentage: l.percentage,
          })),
          { shouldValidate: true },
        )
      }
      if (!data.private && data.contributors.length > 0) {
        setValue('contributors', data.contributors, { shouldValidate: true })
      }
    },
    [isEdit, setValue],
  )

  const onSubmit = handleSubmit(async (values) => {
    try {
      if (isEdit) {
        await update.mutateAsync(values)
        notifications.show({ message: 'Project saved.', color: 'teal' })
      } else {
        await create.mutateAsync(values)
        notifications.show({ message: 'Project created.', color: 'teal' })
      }
      navigate('/admin/projects')
    } catch (err) {
      if (err instanceof ApiError && err.fields) {
        for (const [field, message] of Object.entries(err.fields)) {
          setError(field as keyof ProjectFormValues, { message })
        }
        notifications.show({
          title: 'Check the form',
          message: 'Some fields need attention.',
          color: 'red',
        })
      } else {
        notifications.show({
          title: isEdit ? 'Save failed' : 'Create failed',
          message: err instanceof ApiError || err instanceof Error ? err.message : 'Try again.',
          color: 'red',
        })
      }
    }
  })

  if (isEdit && project.isLoading) {
    return (
      <Center py="xl">
        <Loader />
      </Center>
    )
  }
  if (isEdit && project.isError) {
    return (
      <Alert color="red" icon={<IconAlertTriangle size={18} />} title="Couldn't load the project">
        {project.error instanceof Error ? project.error.message : 'Something went wrong.'}
      </Alert>
    )
  }

  const saving = create.isPending || update.isPending

  return (
    <form onSubmit={onSubmit} noValidate>
      <Stack gap="lg" maw={760}>
        <Group justify="space-between" align="flex-start">
          <div>
            <Button
              variant="subtle"
              size="compact-sm"
              px={0}
              leftSection={<IconArrowLeft size={14} />}
              onClick={() => navigate('/admin/projects')}
            >
              Projects
            </Button>
            <Title order={1} fz="h2">
              {isEdit ? (project.data?.name ?? 'Edit project') : 'New project'}
            </Title>
          </div>
          <Button type="submit" leftSection={<IconDeviceFloppy size={16} />} loading={saving}>
            {isEdit ? 'Save' : 'Create'}
          </Button>
        </Group>

        <TextInput
          label="Title"
          maxLength={20}
          error={errors.name?.message}
          {...register('name')}
        />

        <Group grow align="flex-start">
          <TextInput
            label="Live URL"
            placeholder="https://…"
            error={errors.link?.message}
            {...register('link')}
          />
          <TextInput
            label="GitHub repository"
            placeholder="https://github.com/owner/repo"
            error={errors.github?.message}
            {...githubReg}
            onChange={(e) => {
              githubTouched.current = true
              void githubReg.onChange(e)
            }}
          />
        </Group>
        <GitHubAutofill url={debouncedGithub} onApply={onGitHubApply} />

        <Group>
          <Controller
            control={control}
            name="pinned"
            render={({ field }) => (
              <Switch
                label="Pinned"
                checked={field.value}
                onChange={(e) => field.onChange(e.currentTarget.checked)}
              />
            )}
          />
          <Controller
            control={control}
            name="inProgress"
            render={({ field }) => (
              <Switch
                label="Work in progress"
                checked={field.value}
                onChange={(e) => field.onChange(e.currentTarget.checked)}
              />
            )}
          />
          <Controller
            control={control}
            name="privateRepo"
            render={({ field }) => (
              <Switch
                label="Private repo"
                checked={!!field.value}
                onChange={(e) => field.onChange(e.currentTarget.checked)}
              />
            )}
          />
        </Group>

        <Controller
          control={control}
          name="description"
          render={({ field }) => (
            <QuillEditor
              label="Description"
              value={field.value as DeltaOp[]}
              onChange={field.onChange}
              error={errors.description?.message as string | undefined}
            />
          )}
        />

        <Divider />

        <div>
          <Text fw={600} fz="sm" mb="xs">
            Languages
          </Text>
          <LanguageRows control={control} />
        </div>

        <div>
          <Text fw={600} fz="sm" mb="xs">
            Contributors
          </Text>
          <ContributorList control={control} />
        </div>

        <Divider />

        <ImageManager
          existing={watch('existingImages')}
          removed={watch('removedImages')}
          newFiles={watch('newFiles')}
          thumbs={thumbs}
          onExistingChange={(v) => setValue('existingImages', v, { shouldValidate: true })}
          onRemovedChange={(v) => setValue('removedImages', v, { shouldValidate: true })}
          onNewFilesChange={(v) => setValue('newFiles', v, { shouldValidate: true })}
          error={
            (errors.newFiles?.message as string | undefined) ??
            (errors.existingImages?.message as string | undefined)
          }
        />
      </Stack>
    </form>
  )
}
