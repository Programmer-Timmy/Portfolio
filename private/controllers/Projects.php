<?php

class Projects {

    public static function loadProjects($limit) {
        $results = Database::getAll('projects', ['*'], [], ['removed' => 0], 'pinned DESC, date DESC LIMIT ' . $limit);
        foreach ($results as $key => $project) {
            $results[$key]->project_languages = self::loadProjectLanguages($project->id, 3);
            $results[$key]->project_contributors = self::loadProjectContributors($project->id);
        }
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    public static function loadProject($id) {
        $results = Database::get('projects', ['*'], [], ['id' => $id, 'removed' => 0]);
        $results->project_languages = self::loadProjectLanguages($id);
        $results->project_contributors = self::loadProjectContributors($id);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    public static function loadProjectImg($id) {
        $results = Database::getAll('project_images', ['img'], [], ['project_id' => $id]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    public static function addProject($name, $description, $path, $github, $files, $pinned, $workInProcess, $privateRepo) {
        $date = date('Y-m-d H:i:s');
        $img = self::uploadImage($files);

        if (isset($img['error'])) {
            if (is_array($img['images'])) {
                foreach ($img['images'] as $image) {
                    self::deleteImage($image);
                }
            }
            return $img['error'];
        }

        try {
            $database = Database::beginTransaction();

            $results = Database::insert('projects', ['name', 'description', 'date', 'path', 'github', 'img', 'pinned', 'in_progress', 'private_repo'], [$name, $description, $date, $path, $github, $img[0], $pinned, $workInProcess, $privateRepo], $database);

            array_shift($img);
            if ($results && !empty($img)) {
                $id = $database->lastInsertId();
                foreach ($img as $image) {
                    Database::insert('project_images', ['project_id', 'img'], [$id, $image], $database);
                }
            }

            $database->commit($database);

            if (!$results) {
                foreach ($img as $image) {
                    self::deleteImage($image);
                }
                return "There was an error adding your project.";
            }
        } catch (Exception $e) {
            $database->rollBack($database);
            self::deleteImage($img[0]);
            return "There was an error adding your project.";
        }
        return $id ?? "";


    }

    public static function uploadImage($files) {
        $urlArray = [];
        $count = 0;
        foreach ($files as $file) {
            $file_name = $file['name'];
            $file_name = explode(':', $file_name);
            $file_name = $file_name[0];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];
            $file_ext = explode('.', $file_name);
            $file_ext = strtolower(end($file_ext));

            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed)) {
                if ($file_error === 0) {
                    if ($file_size <= 2097152) {
                        $file_name_new = uniqid('', true) . '.' . $file_ext;
                        $file_destination = '../public_html/img/' . $file_name_new;
                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            $urlArray[$count] = "img/" . $file_name_new;
                            $count++;
                        } else {
                            return ["error" => "There was an error uploading your image number " . $count, "images" => $urlArray];
                        }
                    } else {
                        return ["error" => "Your image " . $count . " is too large.", "images" => $urlArray];
                    }
                } else {
                    return ["error" => "There was an error uploading your image number " . $count, "images" => $urlArray];
                }
            } else {
                return ["error" => "File type of file number " . $count . " is not allowed.", "images" => $urlArray];
            }

        }
        return $urlArray;
    }

    private static function deleteImage($img) {
        if (file_exists($img)) {
            unlink('../public_html/' . $img);
        }
    }

    public static function deleteProject($id) {
        try {
            Database::update('projects', ['removed'], [1], ['id' => $id]);
            return "";
        } catch (Exception $e) {
            return "There was an error removing your project.";
        }

    }

    public static function hardDeleteProject($id) {
        try {
            Database::delete('projects', ['id' => $id]);
            return "";
        } catch (Exception $e) {
            return "There was an error removing your project.";
        }
    }

    public static function updateProject($name, $description, $path, $github, $files, $pinned, $workInProcess, $id, $privateRepo) {
        $existing = Database::get('projects', ['img'], [], ['id' => $id]);
        $img = [$existing->img];
        if (!empty($files)) {
            foreach ($img as $image) {
                self::deleteImage($image);
            }


            $img = self::uploadImage($files);
            if (isset($img['error'])) {
                if (is_array($img['images'])) {
                    foreach ($img['images'] as $image) {
                        var_dump($image);
                        self::deleteImage($image);
                    }
                }
                return $img['error'];
            }
        }


        try {
            $database = Database::beginTransaction();

            Database::update('projects', ['name', 'description', 'path', 'github', 'img', 'pinned', 'in_progress', 'private_repo'], [$name, $description, $path, $github, $img[0], $pinned, $workInProcess, $privateRepo], ['id' => $id], $database);

            if (!empty($files)) {
                array_shift($img);
                if (!empty($img)) {
                    foreach ($img as $image) {
                        Database::insert('project_images', ['project_id', 'img'], [$id, $image], $database);
                    }
                }
            }

            $database->commit($database);

        } catch (Exception $e) {
            foreach ($img as $image) {
                self::deleteImage($image);

            }
            $database->rollBack($database);
            return "There was an error updating your project." . $e->getMessage();
        }
        return "";
    }

    private static function loadProjectLanguages($id, $limit = 100) {
        $results = Database::getAll(table: 'project_languages', columns: ['name', 'color', 'percentage', 'programming_languages_id'], join: ['programming_languages' => 'programming_languages.id = project_languages.programming_languages_id'], where: ['projects_id' => $id], orderBy: 'percentage DESC LIMIT ' . $limit);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    public static function updateProjectLanguages(mixed $project_languages, $id) {
        try {
            $database = Database::beginTransaction();

            $project_languages = json_decode($project_languages, true);

            Database::delete('project_languages', ['projects_id' => $id], $database);

            foreach ($project_languages as $language) {
                Database::insert('project_languages', ['projects_id', 'programming_languages_id', 'percentage'], [$id, $language['programming_languages_id'], $language['percentage']], $database);
            }

            $database->commit($database);

        } catch (Exception $e) {
            $database->rollBack($database);
            return "There was an error updating your project languages.";
        }
        return "";
    }

    public static function addProjectLanguages($project_languages, $id) {
        try {
            $database = Database::beginTransaction();
            $project_languages = json_decode($project_languages, true);


            foreach ($project_languages as $language) {
                Database::insert('project_languages', ['projects_id', 'programming_languages_id', 'percentage'], [$id, $language['programming_languages_id'], $language['percentage']], $database);
            }

            $database->commit($database);

        } catch (Exception $e) {
            print_r($e);
            $database->rollBack($database);
            return "There was an error adding your project languages.";
        }
        return "";
    }

    public static function deleteProjectLanguages($id) {
        try {
            Database::delete('project_languages', ['projects_id' => $id]);
            return "";
        } catch (Exception $e) {
            return "There was an error removing your project languages.";
        }
    }

    public static function updateProjectContributors($contributors, $id,) {
        $database = Database::beginTransaction();
        $status = self::deleteProjectContributors($id, $database);
        if ($status) {
            return $status;
        }
        $contributors = json_decode($contributors, true);
        try {
            foreach ($contributors as $contributor) {
                $user = Database::get('github_user', ['*'], [], ['id' => $contributor['user']['id']]);
                if (!$user) {
                    Database::insert('github_user', ['id', 'login', 'avatar_url', 'html_url'], [$contributor['user']['id'], $contributor['user']['login'], $contributor['user']['avatar_url'], $contributor['user']['html_url']], $database);
                }
                Database::insert('project_contributors', ['projects_id', 'github_user_id', 'contributions'], [$id, $contributor['user']['id'], $contributor['contributions']], $database);

            }
        } catch (Exception $e) {
            $database->rollBack($database);
            return "There was an error adding your project contributors.";
        }
    }

    public static function addProjectContributors($id, $contributors) {

    }

    public static function deleteProjectContributors($id, $database = new Database()) {
        $database = $database::beginTransaction();
        try {
            Database::delete('project_contributors', ['projects_id' => $id], $database);
            return "";
        } catch (Exception $e) {
            var_dump($e);
            $database->rollBack($database);
            return "There was an error removing your project contributors.";
        }
    }

    private static function loadProjectContributors($id) {
        $results = Database::getAll('project_contributors', ['*'], ['github_user' => 'github_user.id = project_contributors.github_user_id'], ['projects_id' => $id], 'contributions DESC');
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }
}