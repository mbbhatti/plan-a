<?php

namespace App\Parsers;

class ModelParser implements ParserInterface
{
    /**
     * get template for parsing
     *
     * @return string
     */
    public function getTemplate(): string
    {
        $path = __DIR__ . "/template.php";
        if (file_exists($path)) {
            $content = file_get_contents($path);
        } else {
            $content = "File not found!";
        }

        return $content;
    }

    /**
     * Manage data collection to replace template keys
     *
     * @param array $collection
     * @return array
     */
    public function manageCollection(array $collection): array
    {
        $response = [];
        $namespace = 'App/Models';
        foreach ($collection as $key => $value) {
            if ($key === 'scope') {
                foreach ($value as $data) {
                    $path = preg_replace('/[^a-zA-Z0-9\']/', ' ', $data);
                    $path = ucwords($path);
                    $path = str_replace(' ', '', $path);
                    $namespace .= '/'. $path;
                }
                $response['namespace'] = str_replace('/', '\\', $namespace);
            } elseif ($key === 'name') {
                $className = preg_replace('/[^a-zA-Z0-9\']/', ' ', $value);
                $className = ucwords($className);
                $className = str_replace(' ', '', $className);
                $response['class_name'] = $className;
                $response['table_name'] = $value;
            }
        }

        return $response;
    }

    /**
     * Parse template content and generate file
     *
     * @param array $data
     * @return bool
     */
    public function parse(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        // Get managed data collection
        $collection = $this->manageCollection($data);
        $filePath = $collection['namespace'] ?? '';
        $fileName = $collection['class_name'] ?? '';

        if (empty($filePath) || empty($fileName)) {
            return false;
        }

        // Created path based on data collection
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }

        // Parse template
        $content = $this->getTemplate();
        foreach ($collection as $placeholder_key => $placeholder_value) {
            $content = str_replace('{' . $placeholder_key . '}', $placeholder_value, $content);
            $content = preg_replace('/\{\}/is','',$content);
        }

        // Generate file
        $fullPath = $filePath . '/' . $fileName . '.php';
        $file = fopen($fullPath, "wb") or die("Unable to open file!");
        fwrite($file, $content);
        fclose($file);

        return true;
    }
}
