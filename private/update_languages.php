<?php
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/controllers/Database.php';
require_once __DIR__ . '/controllers/Projects.php';



// GitHub API Headers
$headers = [
    'User-Agent: programmer-timmy',
];

// Fetch all projects with a GitHub link from the database
$projects = Database::query('SELECT id, github FROM projects WHERE github IS NOT NULL AND github != ""');
// Loop through each project and update its languages
foreach ($projects as $project) {
    if (empty($project->github) || !filter_var($project->github, FILTER_VALIDATE_URL)) {
        echo "Invalid GitHub link for project with ID: $project->id\n";
        continue;
    }

    $githubLink = $project->github;

    // Check if GitHub repo exists
    saveProjectLanguages($project->id, fetchLanguages($githubLink, $headers));


}

// Function to fetch the languages from the GitHub API
function fetchLanguages($githubLink, $headers) {
    $parts = explode('/', $githubLink);
    $repo = end($parts);
    $user = $parts[count($parts) - 2];
    $url = "https://api.github.com/repos/$user/$repo/languages";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);

    $languagesData = json_decode($response, true);
    return $languagesData;
}

// Function to save the project languages to the database
function saveProjectLanguages($projectId, $languagesData) {
    $totalAmount = array_sum($languagesData);
    $languages = [];
    $other = 0;

    foreach ($languagesData as $language => $value) {
        $value = (int)$value;
        $percentage = round(($value / $totalAmount) * 100, 1);

        if ($percentage < 1) {
            $other += $percentage;
            continue;
        }

        $languageRecord = Database::get('programming_languages', ['id'], [], ['name' => $language]);

        if ($languageRecord) {
            $languages[] = [
                'project_id' => $projectId,
                'programming_languages_id' => $languageRecord->id,
                'percentage' => $percentage
            ];
        }
    }

    if ($other > 0) {
        $languageRecord = Database::get('programming_languages', ['id'], [], ['name' => 'Other']);
        if ($languageRecord) {
            $languages[] = [
                'project_id' => $projectId,
                'programming_languages_id' => $languageRecord->id,
                'percentage' => $other
            ];
        }
    }


    // Now, save the languages data for the project
    if (!empty($languages)) {
        // Delete old project languages
        Projects::deleteProjectLanguages($projectId);

        Projects::addProjectLanguages(json_encode($languages), $projectId);
        echo "Languages updated for project with ID: $projectId\n";

    }
}
