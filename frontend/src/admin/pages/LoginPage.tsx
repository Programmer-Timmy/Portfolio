import { useEffect } from 'react'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { useLocation, useNavigate } from 'react-router-dom'
import {
  Alert,
  Box,
  Button,
  Center,
  Paper,
  PasswordInput,
  Stack,
  Text,
  TextInput,
  Title,
} from '@mantine/core'
import { IconAlertTriangle } from '@tabler/icons-react'
import { ApiError } from '@/lib/api'
import { useLogin, useSession } from '../hooks'
import { loginSchema, type LoginValues } from '../lib/schemas'

/** Admin sign-in. Posts to `/api/auth/login`; the PHP session is the source of truth. */
export function LoginPage() {
  const navigate = useNavigate()
  const location = useLocation()
  const session = useSession()
  const login = useLogin()

  const from = (location.state as { from?: string } | null)?.from ?? null

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors },
  } = useForm<LoginValues>({
    resolver: zodResolver(loginSchema),
    defaultValues: { username: '', password: '' },
  })

  // Already signed in as admin — skip the form.
  useEffect(() => {
    if (session.data?.admin) {
      navigate(from ?? '/admin', { replace: true })
    }
  }, [session.data?.admin, from, navigate])

  const onSubmit = handleSubmit(async (values) => {
    try {
      const result = await login.mutateAsync(values)
      if (!result.admin) {
        setError('root', { message: "This account doesn't have admin access." })
        return
      }
      navigate(from ?? result.redirect ?? '/admin', { replace: true })
    } catch (err) {
      if (err instanceof ApiError && err.fields) {
        for (const [field, message] of Object.entries(err.fields)) {
          setError(field as keyof LoginValues, { message })
        }
      } else if (err instanceof ApiError) {
        setError('root', { message: err.message })
      } else {
        setError('root', { message: 'Something went wrong. Try again.' })
      }
    }
  })

  return (
    <Center mih="100dvh" bg="var(--mantine-color-gray-0)" p="md">
      <Paper withBorder shadow="sm" radius="md" p="xl" w={380} maw="100%">
        <Stack gap="lg">
          <Box>
            <Title order={1} fz="h3">
              Admin
            </Title>
            <Text c="dimmed" fz="sm" mt={4}>
              Sign in to manage projects, videos and open-source work.
            </Text>
          </Box>

          {errors.root?.message && (
            <Alert color="red" icon={<IconAlertTriangle size={18} />} py="xs">
              {errors.root.message}
            </Alert>
          )}

          <form onSubmit={onSubmit} noValidate>
            <Stack gap="md">
              <TextInput
                label="Username"
                autoComplete="username"
                error={errors.username?.message}
                {...register('username')}
              />
              <PasswordInput
                label="Password"
                autoComplete="current-password"
                error={errors.password?.message}
                {...register('password')}
              />
              <Button type="submit" fullWidth mt="xs" loading={login.isPending}>
                Sign in
              </Button>
            </Stack>
          </form>
        </Stack>
      </Paper>
    </Center>
  )
}
