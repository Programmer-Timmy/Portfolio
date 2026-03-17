<?php
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/controllers/Database.php';
require_once __DIR__ . '/controllers/OpenSource.php';

// Load environment variables
$env = parse_ini_file(__DIR__ . '/../.env');

// GitHub Username to fetch contributions for
// Change this if your GitHub username is different
$githubUser = 'timvanderkloet';

// GitHub API Headers
$headers = [
    'User-Agent: programmer-timmy',
    'Authorization: token ' . $env['GITHUB_TOKEN']
];

echo "Starting contributions update for user: $githubUser\n";

// Fetch all open source projects
$projects = Database::getAll('opensource_projects');

if (!$projects) {
    echo "No open source projects found in database.\n";
    exit;
}

foreach ($projects as $project) {
    echo "Processing project: {$project->name}...\n";

    // Parse repo owner and name from URL
    // URL format: https://github.com/owner/repo
    if (!preg_match('#github\.com/([^/]+)/([^/]+)#', $project->url, $matches)) {
        echo "  Invalid GitHub URL: {$project->url}\n";
        continue;
    }
    
    $owner = $matches[1];
    $repo = $matches[2];
    
    // Fetch PRs from GitHub
    $prs = fetchPRs($owner, $repo, $githubUser, $headers);
    
    if ($prs === false) {
        echo "  Failed to fetch PRs for {$owner}/{$repo}\n";
        continue;
    }
    
    $newCount = 0;
    $updatedCount = 0;
    
    foreach ($prs as $item) {
        $url = $item['html_url'];
        
        $status = ucfirst($item['state']);
        if (isset($item['pull_request']['merged_at']) && $item['pull_request']['merged_at']) {
            $status = 'Merged';
        }
        
        $title = $item['title'];
        $date = date('Y-m-d H:i:s', strtotime($item['created_at']));
        $description = $item['body'] ?? $title;
        // Truncate description if too long
        if (strlen($description) > 500) {
            $description = substr($description, 0, 497) . '...';
        }
        
        // Check if PR exists
        $existingPr = Database::get('opensource_prs', ['id', 'status'], [], ['url' => $url]);
        
        if (!$existingPr) {
            try {
                Database::insert('opensource_prs', 
                    ['project_id', 'title', 'url', 'status', 'date', 'description'], 
                    [$project->id, $title, $url, $status, $date, $description]
                );
                $newCount++;
            } catch (Exception $e) {
                echo "  Error inserting PR: " . $e->getMessage() . "\n";
            }
        } else {
            if ($existingPr->status !== $status) {
                try {
                    // Update status
                    $sql = "UPDATE opensource_prs SET status = :status WHERE id = :id";
                    $stmt = Database::prepare($sql);
                    $stmt->execute(['status' => $status, 'id' => $existingPr->id]);
                    $updatedCount++;
                } catch (Exception $e) {
                    echo "  Error updating PR: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "  Added $newCount new PRs, updated $updatedCount PRs.\n";
}

echo "Done.\n";

function fetchPRs($owner, $repo, $username, $headers) {
    $url = "https://api.github.com/search/issues?q=repo:$owner/$repo+is:pr+author:$username";
    
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Portfolio-App');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        echo "  Curl error: " . curl_error($ch) . "\n";
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    if ($httpCode !== 200) {
        echo "  GitHub API returned HTTP $httpCode\n";
        return false;
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['items'])) {
        return false;
    }
    
    return $data['items'];
}
?>