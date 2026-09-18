import {
  ActionIcon,
  Button,
  ColorSwatch,
  Group,
  NumberInput,
  Select,
  Stack,
  Text,
} from '@mantine/core'
import { IconPlus, IconTrash } from '@tabler/icons-react'
import { Controller, useFieldArray, useWatch, type Control } from 'react-hook-form'
import { useLanguageOptions } from '../hooks'
import type { ProjectFormValues } from '../lib/schemas'

export function LanguageRows({ control }: { control: Control<ProjectFormValues> }) {
  const { fields, append, remove } = useFieldArray({ control, name: 'languages' })
  const options = useLanguageOptions()

  const selectData = (options.data ?? []).map((l) => ({ value: String(l.id), label: l.name }))
  const colorById = new Map((options.data ?? []).map((l) => [l.id, l.color]))

  return (
    <Stack gap="xs">
      {fields.map((field, index) => (
        <Group key={field.id} gap="xs" wrap="nowrap" align="flex-start">
          <Controller
            control={control}
            name={`languages.${index}.programmingLanguageId`}
            render={({ field: f, fieldState }) => (
              <Select
                flex={1}
                searchable
                placeholder="Language"
                data={selectData}
                value={f.value ? String(f.value) : null}
                onChange={(v) => f.onChange(v ? Number(v) : 0)}
                error={fieldState.error?.message}
                leftSection={
                  f.value ? (
                    <ColorSwatch size={14} color={colorById.get(Number(f.value)) ?? '#ccc'} />
                  ) : undefined
                }
              />
            )}
          />
          <Controller
            control={control}
            name={`languages.${index}.percentage`}
            render={({ field: f, fieldState }) => (
              <NumberInput
                w={110}
                suffix="%"
                min={0}
                max={100}
                clampBehavior="strict"
                value={f.value as number}
                onChange={(v) => f.onChange(typeof v === 'number' ? v : 0)}
                error={fieldState.error?.message}
              />
            )}
          />
          <ActionIcon
            variant="subtle"
            color="red"
            mt={4}
            onClick={() => remove(index)}
            aria-label="Remove language"
          >
            <IconTrash size={16} />
          </ActionIcon>
        </Group>
      ))}

      <Group justify="space-between">
        <Button
          size="xs"
          variant="light"
          leftSection={<IconPlus size={14} />}
          onClick={() => append({ programmingLanguageId: 0, percentage: 0 })}
        >
          Add language
        </Button>
        {fields.length > 0 && <PercentTotal control={control} />}
      </Group>
    </Stack>
  )
}

function PercentTotal({ control }: { control: Control<ProjectFormValues> }) {
  const rows = useWatch({ control, name: 'languages' }) ?? []
  const total = rows.reduce((sum, r) => sum + (Number(r?.percentage) || 0), 0)
  return (
    <Text fz="xs" c={Math.round(total) === 100 ? 'dimmed' : 'orange'}>
      {total.toFixed(0)}% total
    </Text>
  )
}
