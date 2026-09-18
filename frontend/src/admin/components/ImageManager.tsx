import { useEffect, useMemo } from 'react'
import { ActionIcon, Badge, Group, Image, Input, Paper, Stack, Text } from '@mantine/core'
import { Dropzone, IMAGE_MIME_TYPE } from '@mantine/dropzone'
import {
  IconArrowBackUp,
  IconChevronDown,
  IconChevronUp,
  IconPhotoPlus,
  IconStar,
  IconTrash,
  IconX,
} from '@tabler/icons-react'

type Props = {
  /** Kept stored image paths, in display order (index 0 = cover). */
  existing: string[]
  /** Stored paths flagged for deletion on save. */
  removed: string[]
  newFiles: File[]
  /** path -> preview URL, from the loaded project. */
  thumbs: Record<string, string>
  onExistingChange: (next: string[]) => void
  onRemovedChange: (next: string[]) => void
  onNewFilesChange: (next: File[]) => void
  error?: string
}

function move<T>(arr: T[], from: number, to: number): T[] {
  if (to < 0 || to >= arr.length) return arr
  const copy = [...arr]
  const [item] = copy.splice(from, 1)
  copy.splice(to, 0, item)
  return copy
}

export function ImageManager({
  existing,
  removed,
  newFiles,
  thumbs,
  onExistingChange,
  onRemovedChange,
  onNewFilesChange,
  error,
}: Props) {
  const previews = useMemo(() => newFiles.map((f) => URL.createObjectURL(f)), [newFiles])
  useEffect(() => () => previews.forEach((url) => URL.revokeObjectURL(url)), [previews])

  const keptExisting = existing.filter((p) => !removed.includes(p))
  const coverPath = keptExisting[0] ?? null
  const coverIsNewFile = !coverPath && newFiles.length > 0

  return (
    <Input.Wrapper label="Images" error={error} description="The first image is the cover.">
      <Stack gap="xs" mt={6}>
        {existing.map((path, index) => {
          const isRemoved = removed.includes(path)
          return (
            <Paper key={path} withBorder p="xs" opacity={isRemoved ? 0.5 : 1}>
              <Group gap="sm" wrap="nowrap">
                <Image src={thumbs[path]} alt="" w={72} h={48} radius="sm" fit="cover" />
                <Text fz="xs" c="dimmed" flex={1} truncate>
                  {path.split('/').pop()}
                </Text>
                {path === coverPath && !isRemoved && (
                  <Badge size="xs" leftSection={<IconStar size={10} />}>
                    Cover
                  </Badge>
                )}
                {!isRemoved && (
                  <>
                    <ActionIcon
                      variant="subtle"
                      color="gray"
                      disabled={index === 0}
                      onClick={() => onExistingChange(move(existing, index, index - 1))}
                      aria-label="Move up"
                    >
                      <IconChevronUp size={15} />
                    </ActionIcon>
                    <ActionIcon
                      variant="subtle"
                      color="gray"
                      disabled={index === existing.length - 1}
                      onClick={() => onExistingChange(move(existing, index, index + 1))}
                      aria-label="Move down"
                    >
                      <IconChevronDown size={15} />
                    </ActionIcon>
                  </>
                )}
                <ActionIcon
                  variant="subtle"
                  color={isRemoved ? 'teal' : 'red'}
                  onClick={() =>
                    onRemovedChange(
                      isRemoved ? removed.filter((p) => p !== path) : [...removed, path],
                    )
                  }
                  aria-label={isRemoved ? 'Keep image' : 'Remove image'}
                >
                  {isRemoved ? <IconArrowBackUp size={15} /> : <IconTrash size={15} />}
                </ActionIcon>
              </Group>
            </Paper>
          )
        })}

        {newFiles.map((file, index) => (
          <Paper key={`${file.name}-${index}`} withBorder p="xs">
            <Group gap="sm" wrap="nowrap">
              <Image src={previews[index]} alt="" w={72} h={48} radius="sm" fit="cover" />
              <Text fz="xs" c="dimmed" flex={1} truncate>
                {file.name} <Badge size="xs" variant="light" color="teal">new</Badge>
              </Text>
              {coverIsNewFile && index === 0 && (
                <Badge size="xs" leftSection={<IconStar size={10} />}>
                  Cover
                </Badge>
              )}
              <ActionIcon
                variant="subtle"
                color="gray"
                disabled={index === 0}
                onClick={() => onNewFilesChange(move(newFiles, index, index - 1))}
                aria-label="Move up"
              >
                <IconChevronUp size={15} />
              </ActionIcon>
              <ActionIcon
                variant="subtle"
                color="gray"
                disabled={index === newFiles.length - 1}
                onClick={() => onNewFilesChange(move(newFiles, index, index + 1))}
                aria-label="Move down"
              >
                <IconChevronDown size={15} />
              </ActionIcon>
              <ActionIcon
                variant="subtle"
                color="red"
                onClick={() => onNewFilesChange(newFiles.filter((_, i) => i !== index))}
                aria-label="Remove"
              >
                <IconX size={15} />
              </ActionIcon>
            </Group>
          </Paper>
        ))}

        <Dropzone
          accept={IMAGE_MIME_TYPE}
          onDrop={(files) => onNewFilesChange([...newFiles, ...files])}
          maxSize={10 * 1024 * 1024}
          py="lg"
        >
          <Group justify="center" gap="xs" style={{ pointerEvents: 'none' }}>
            <IconPhotoPlus size={20} />
            <Text fz="sm" c="dimmed">
              Drop images here or click to choose
            </Text>
          </Group>
        </Dropzone>
      </Stack>
    </Input.Wrapper>
  )
}
