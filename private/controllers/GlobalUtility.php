<?php

class GlobalUtility
{
    /**
     * @param array $data
     * @param array $shownTables ['*'] for all columns or ['name', 'date', 'removed'] for specific columns
     * @param array $customButtons [['class' => '{Style Classes}', 'action'=> '{page url whit get}={id wil always be at the end}', 'label' => '{The button text}'], [...]]
     * @param bool $bootstrap
     * @return string
     */
    public static function createTable(array $data, array $shownTables = ['*'], array $customButtons = [], bool $bootstrap = true): string
    {
        $tableClass = $bootstrap ? 'table table-striped table-hover table-responsive' : '';

        $table = '<div class="' . ($bootstrap ? 'table-responsive' : '') . '">';
        $table .= '<table class="' . $tableClass . '">';
        $table .= '<thead><tr>';

        if (!empty($data)) {
            foreach (get_object_vars($data[0]) as $column => $value) {
                if ($shownTables[0] == '*') {
                    $table .= '<th>' . $column . '</th>';
                } else {
                    if (in_array($column, $shownTables)) {
                        $table .= '<th>' . ucfirst($column) . '</th>';
                    }
                }
            }
            if (!empty($customButtons)) {
                foreach ($customButtons as $button) {
                    $table .= '<th></th>';
                }
            }

            $table .= '</tr></thead><tbody>';

            foreach ($data as $row) {
                $table .= '<tr>';
                foreach (get_object_vars($row) as $column => $value) {
                    if ($value == 1 || $value == 0) {
                        $value = $value ? 'True' : 'False';
                    }
                    if ($shownTables[0] == '*') {
                        $table .= '<td>' . $value . '</td>';
                    } else {
                        if (in_array($column, $shownTables)) {
                            $table .= '<td>' . $value . '</td>';
                        }
                    }

                }

                if (!empty($customButtons)) {
                    foreach ($customButtons as $button) {
                        $table .= '<td><a class="' . $button['class'] . '" href="' . $button['action'] . $row->id . '">' . $button['label'] . '</a></td>';
                    }
                }

                $table .= '</tr>';
            }

            $table .= '</tbody>';
        } else {
            $table .= '<th>No data</th>';
        }

        $table .= '</table>';
        $table .= '</div>';

        return $table;


    }

    public static function calculateTimeAgo(string $updated_at): string
    {
        // Convert updated_at to a DateTime object
        $updatedTime = new DateTime($updated_at);

        // Get the current time
        $currentTime = new DateTime('now');

        // Calculate the difference between the current time and updated time
        $interval = $currentTime->diff($updatedTime);

        // Format the time difference
        if ($interval->days > 0) {
            return $interval->format('%a days ago');
        } elseif ($interval->h > 0) {
            return $interval->format('%h hours ago');
        } elseif ($interval->i > 0) {
            return $interval->format('%i minutes ago');
        } else {
            return 'Just now';
        }
    }

    public static function applyInlineAttributes($content, $attributes)
    {
        $styles = '';
        if (isset($attributes->color)) {
            $styles .= 'color:' . htmlspecialchars($attributes->color) . ';';
        }
        if (isset($attributes->background)) {
            $styles .= 'background-color:' . htmlspecialchars($attributes->background) . ';';
        }
        if (isset($attributes->font)) {
            $styles .= 'font-family:' . htmlspecialchars($attributes->font) . ';';
        }
        if (isset($attributes->size)) {
            $styles .= 'font-size:' . htmlspecialchars($attributes->size) . ';';
        }

        if (!empty($styles)) {
            $content = '<span style="' . $styles . '">' . $content . '</span>';
        }
        if (isset($attributes->bold) && $attributes->bold) {
            $content = '<strong>' . $content . '</strong>';
        }
        if (isset($attributes->italic) && $attributes->italic) {
            $content = '<em>' . $content . '</em>';
        }
        if (isset($attributes->underline) && $attributes->underline) {
            $content = '<u>' . $content . '</u>';
        }
        if (isset($attributes->strike) && $attributes->strike) {
            $content = '<s>' . $content . '</s>';
        }
        if (isset($attributes->code) && $attributes->code) {
            $content = '<code>' . $content . '</code>';
        }
        if (isset($attributes->link)) {
            $content = '<a href="' . htmlspecialchars($attributes->link) . '">' . $content . '</a>';
        }
        return $content;
    }

// Function to apply block attributes to content
    public static function applyBlockAttributes($content, $attributes)
    {
        if (isset($attributes->header)) {
            $level = intval($attributes->header);
            var_dump($level);
            $content = '<h' . $level . '>' . $content . '</h' . $level . '>';
        }
        if (isset($attributes->blockquote)) {
            $content = '<blockquote>' . $content . '</blockquote>';
        }
        if (isset($attributes->list)) {
            if ($attributes->list == 'ordered') {
                $content = '<ol><li>' . $content . '</li></ol>';
            } else {
                $content = '<ul><li>' . $content . '</li></ul>';
            }
        }
        return $content;
    }

// Function to unpack and process the description
// todo needs to be fixed
    public static function unpackDescription($descriptionJson)
    {
        // Decode the JSON string to an array of objects
        $descriptionArray = json_decode($descriptionJson);
        $htmlOutput = '';

        // Iterate through the array and process the 'insert' values
        foreach ($descriptionArray as $item) {
            // Get the content and apply inline attributes if they exist
            $content = htmlspecialchars($item->insert);
            if (isset($item->attributes)) {
                $content = self::applyInlineAttributes($content, $item->attributes);
            }
            // Split the 'insert' text by newline characters
            $lines = explode("\n", $content);
            if (end($lines) == '') {
                array_pop($lines);
            }
            // Iterate through each line
            foreach ($lines as $line) {
                // Only create a <p> if the line is not empty
                $lineHtml = '<p>' . $line . '</p>';
                if (isset($item->attributes)) {
                    $lineHtml = self::applyBlockAttributes($lineHtml, $item->attributes);
                }
                $htmlOutput .= $lineHtml;

            }
        }

        return $htmlOutput;
    }
}
