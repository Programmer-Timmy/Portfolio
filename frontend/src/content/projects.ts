/**
 * Static copy for the Projects index page. The projects themselves come from
 * the API (`GET /api/projects`, see private/api/Controllers/ProjectsApi.php);
 * only the page heading and the link out to GitHub live here.
 */

export const projects = {
  eyebrow: 'Work',
  title: 'Projects',
  lead:
    "A selection of things I've built: web apps, internal tools, and side projects. " +
    'Most of them started as a real problem for someone around me.',

  /** "See everything" link shown above the grid. */
  source: {
    label: 'All repositories on GitHub',
    url: 'https://github.com/Programmer-Timmy',
  },

  /** Shown when the API returns an empty list. */
  empty: 'No projects to show yet. Check back soon.',
  /** Shown above the API error message. */
  errorTitle: "Projects couldn't be loaded",
}
