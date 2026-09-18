/** Shared helpers for the GitHub-lookup hooks + form validation. */

const GITHUB_REPO_RE = /^https?:\/\/(www\.)?github\.com\/[\w.-]+\/[\w.-]+\/?$/i

/** True when `url` looks like `https://github.com/owner/repo` (query/hash tolerated). */
export function isGitHubRepoUrl(url: string): boolean {
  return GITHUB_REPO_RE.test(url.trim().replace(/[?#].*$/, ''))
}
