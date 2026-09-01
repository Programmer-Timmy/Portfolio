<?php

class OpenSource {

    public static function getAll() {
        $projects = Database::getAll('opensource_projects');
        // We only need the project info for the main list, not all PRs (unless we want a count)
        // Let's add a count for display purposes
        if ($projects) {
            foreach ($projects as $key => $project) {
                // count PRs
                $prs = Database::getAll('opensource_prs', ['id'], [], ['project_id' => $project->id]);
                $projects[$key]->pr_count = count($prs);
            }
        }
        return $projects;
    }

    public static function getProject($id) {
        $project = Database::get('opensource_projects', ['*'], [], ['id' => $id]);
        if ($project) {
            $project->prs = Database::getAll('opensource_prs', ['*'], [], ['project_id' => $id], 'date DESC');
            return $project;
        }
        return false;
    }

    public static function addProject($repoUrl, $username): string {
        $parsed = GitHub::parseRepo($repoUrl);
        if (!$parsed) {
            return "That doesn't look like a GitHub repository URL.";
        }
        $owner = $parsed['owner'];
        $repo = $parsed['repo'];
        $fullName = "$owner/$repo";

        $existing = Database::get('opensource_projects', ['id'], [], ['name' => $fullName]);
        $projectId = $existing ? $existing->id : null;

        if (!$projectId) {
            $description = '';
            try {
                $meta = GitHub::repo($repoUrl);
                $description = ($meta['exists'] ?? false) ? (string) ($meta['description'] ?? '') : '';
            } catch (Throwable $e) {
                // add it without a description
            }

            try {
                $projectId = Database::insert('opensource_projects',
                    ['name', 'url', 'description'],
                    [$fullName, $repoUrl, $description]
                );
            } catch (Exception $e) {
                return "Could not add the repository.";
            }
        }

        try {
            $prs = GitHub::authoredPullRequests($owner, $repo, $username);
        } catch (Throwable $e) {
            return "Repository added, but fetching pull requests failed: " . $e->getMessage();
        }

        foreach ($prs as $pr) {
            if (empty($pr['url']) || Database::get('opensource_prs', ['id'], [], ['url' => $pr['url']])) {
                continue;
            }
            try {
                Database::insert('opensource_prs',
                    ['project_id', 'title', 'url', 'status', 'date', 'description'],
                    [$projectId, $pr['title'], $pr['url'], $pr['status'], $pr['date'], $pr['description']]
                );
            } catch (Exception $e) {
                // skip this PR
            }
        }

        return "";
    }

    public static function deleteProject($id) {
         try {
            // Manual delete of PRs first just in case cascade is not set in DB engine
            Database::delete('opensource_prs', ['project_id' => $id]);
            Database::delete('opensource_projects', ['id' => $id]);
            return "";
        } catch (Exception $e) {
            return "Error deleting project.";
        }
    }
}
