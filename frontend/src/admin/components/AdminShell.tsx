import {
  AppShell,
  Badge,
  Burger,
  Group,
  NavLink,
  ScrollArea,
  Text,
  Tooltip,
  UnstyledButton,
} from '@mantine/core'
import { useDisclosure } from '@mantine/hooks'
import {
  IconExternalLink,
  IconFolder,
  IconGitPullRequest,
  IconLayoutDashboard,
  IconLogout,
  IconVideo,
} from '@tabler/icons-react'
import { Link, Outlet, useLocation, useNavigate } from 'react-router-dom'
import { useLogout } from '../hooks'

type NavItem = {
  label: string
  to: string
  icon: typeof IconFolder
  ready: boolean
}

const NAV: NavItem[] = [
  { label: 'Dashboard', to: '/admin', icon: IconLayoutDashboard, ready: true },
  { label: 'Projects', to: '/admin/projects', icon: IconFolder, ready: true },
  { label: 'Videos', to: '/admin/videos', icon: IconVideo, ready: true },
  { label: 'Open source', to: '/admin/opensource', icon: IconGitPullRequest, ready: true },
]

export function AdminShell() {
  const [opened, { toggle, close }] = useDisclosure()
  const { pathname } = useLocation()
  const navigate = useNavigate()
  const logout = useLogout()

  async function handleLogout() {
    await logout.mutateAsync().catch(() => undefined)
    navigate('/admin/login', { replace: true })
  }

  return (
    <AppShell
      header={{ height: 56 }}
      navbar={{ width: 240, breakpoint: 'sm', collapsed: { mobile: !opened } }}
      padding="lg"
    >
      <AppShell.Header>
        <Group h="100%" px="md" justify="space-between" wrap="nowrap">
          <Group gap="sm" wrap="nowrap">
            <Burger opened={opened} onClick={toggle} hiddenFrom="sm" size="sm" />
            <Text fw={700} ff="heading">
              Admin
            </Text>
          </Group>
          <Group gap="xs" wrap="nowrap">
            <Tooltip label="Open the public site">
              <UnstyledButton
                component="a"
                href="/"
                target="_blank"
                rel="noreferrer"
                c="dimmed"
                fz="sm"
              >
                <Group gap={4} wrap="nowrap">
                  <IconExternalLink size={16} />
                  <Text fz="sm" visibleFrom="xs">
                    View site
                  </Text>
                </Group>
              </UnstyledButton>
            </Tooltip>
            <UnstyledButton onClick={handleLogout} c="dimmed" fz="sm" disabled={logout.isPending}>
              <Group gap={4} wrap="nowrap">
                <IconLogout size={16} />
                <Text fz="sm">Log out</Text>
              </Group>
            </UnstyledButton>
          </Group>
        </Group>
      </AppShell.Header>

      <AppShell.Navbar p="sm">
        <AppShell.Section grow component={ScrollArea}>
          {NAV.map((item) => {
            const Icon = item.icon
            const active =
              item.to === '/admin' ? pathname === '/admin' : pathname.startsWith(item.to)
            const shared = {
              label: item.label,
              leftSection: <Icon size={18} stroke={1.6} />,
            }
            return item.ready ? (
              <NavLink
                key={item.to}
                component={Link}
                to={item.to}
                onClick={close}
                active={active}
                {...shared}
              />
            ) : (
              <NavLink
                key={item.to}
                disabled
                {...shared}
                rightSection={
                  <Badge size="xs" variant="light" color="gray">
                    Soon
                  </Badge>
                }
              />
            )
          })}
        </AppShell.Section>
      </AppShell.Navbar>

      <AppShell.Main>
        <Outlet />
      </AppShell.Main>
    </AppShell>
  )
}
