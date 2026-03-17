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

    public static function addProject($repoUrl, $username) {
        // Parse repo
        if (!preg_match('#github\.com/([^/]+)/([^/]+)#', $repoUrl, $matches)) {
            return "Invalid GitHub URL. Format: https://github.com/owner/repo";
        }
        $owner = $matches[1];
        $repo = $matches[2];
        $fullName = "$owner/$repo";

        // Check if project exists
        $project = Database::get('opensource_projects', ['id'], [], ['name' => $fullName]);
        $projectId = $project ? $project->id : null;

        if (!$projectId) {
            // Fetch repo description
            $repoApiUrl = "https://api.github.com/repos/$owner/$repo";
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Portfolio-App'
                    ]
                ]
            ];
            $context = stream_context_create($opts);
            $repoResponse = @file_get_contents($repoApiUrl, false, $context);
            $repoDesc = '';
            
            if ($repoResponse) {
                $repoJson = json_decode($repoResponse, true);
                $repoDesc = $repoJson['description'] ?? '';
            }

            try {
                $projectId = Database::insert('opensource_projects', 
                    ['name', 'url', 'description'], 
                    [$fullName, $repoUrl, $repoDesc]
                );
            } catch (Exception $e) {
                return "Error adding project: " . $e->getMessage();
            }
        }

        // Fetch PRs
        $apiUrl = "https://api.github.com/search/issues?q=repo:$owner/$repo+is:pr+author:$username";
        
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Portfolio-App'
                ]
            ]
        ];
        $context = stream_context_create($opts);
        $response = @file_get_contents($apiUrl, false, $context);
        
        if (!$response) return "Project added, but failed to fetch PRs from GitHub API.";
        
        $json = json_decode($response, true);
        if (!isset($json['items'])) return "Project added, but no PRs found.";
        
        $added = 0;

        foreach ($json['items'] as $item) {
            $url = $item['html_url'];
            
            // Check if PR exists
            $exists = Database::get('opensource_prs', ['id'], [], ['url' => $url]);
            if ($exists) continue;
            
            $title = $item['title'];
            $status = ucfirst($item['state']);
            if (isset($item['pull_request']['merged_at']) && $item['pull_request']['merged_at']) {
                $status = 'Merged';
            }
            
            $date = date('Y-m-d H:i:s', strtotime($item['created_at']));
            $description = $item['body'] ?? $title;
            // Truncate description if too long
            if (strlen($description) > 500) $description = substr($description, 0, 497) . '...';
            
            try {
                Database::insert('opensource_prs', 
                    ['project_id', 'title', 'url', 'status', 'date', 'description'], 
                    [$projectId, $title, $url, $status, $date, $description]
                );
                $added++;
            } catch (Exception $e) {
                // Ignore
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
