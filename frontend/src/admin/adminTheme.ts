import { createTheme, type MantineColorsTuple } from '@mantine/core'

/**
 * Mantine theme for the admin app only. Public pages never see this — the
 * MantineProvider is mounted inside the lazily-loaded admin subtree.
 *
 * Colours track the brand tokens from STYLEGUIDE.md: teal `#0B7F89` is the
 * primary (interactive) colour, navy `#1F2933` is for structure and text.
 */

const teal: MantineColorsTuple = [
  '#e6f7f8',
  '#d0eef0',
  '#a3dde1',
  '#72cbd1',
  '#4dbcc4',
  '#36b3bc',
  '#0b7f89', // primary — primaryShade 6
  '#0a6f78',
  '#075961',
  '#02434b',
]

const navy: MantineColorsTuple = [
  '#f3f5f7',
  '#e4e7ea',
  '#c7cdd4',
  '#a8b2bd',
  '#8e99a7',
  '#7d8998',
  '#5b6b76',
  '#3a4551',
  '#28313b',
  '#1f2933',
]

export const adminTheme = createTheme({
  primaryColor: 'teal',
  primaryShade: 6,
  colors: { teal, navy },
  fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif',
  fontFamilyMonospace: '"JetBrains Mono", ui-monospace, monospace',
  headings: {
    fontFamily: 'Poppins, ui-sans-serif, system-ui, sans-serif',
    fontWeight: '600',
  },
  defaultRadius: 'md',
})
