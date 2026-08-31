import { Button, Stack, Text, Title } from '@mantine/core'
import { Link } from 'react-router-dom'

export function AdminNotFound() {
  return (
    <Stack gap="sm" align="flex-start">
      <Title order={1} fz="h2">
        Not found
      </Title>
      <Text c="dimmed">That admin page doesn't exist.</Text>
      <Button component={Link} to="/admin" variant="light">
        Back to dashboard
      </Button>
    </Stack>
  )
}
