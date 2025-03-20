<?php

namespace simplerest\core\libs;

/*
    WordPress compatible metadata parser
*/
class MetadataParser
{
    /*
        De momento funciona en algunos casos y falla en otros

        Funciona:
        $path = 'D:\www\woo2\wp-content\themes\twentytwenty\style.css';

        Falla:
        $path = 'D:\www\woo2\wp-content\themes\shoptimizer\style.css';
    */
    static function getMultilineComments(string $str, bool $content_only = true, $occurrences = 1) {
        $pattern = '/\/\*\*([\s\S]*?)(?:\*\/|$)|\/\*([\s\S]*?)(?:\*\/|$)/';
        preg_match_all($pattern, $str, $matches);

        $comments = $matches[1]; // Using capturing groups to get content without comment structure

        if ($content_only) {
            $cleanedComments = [];
            foreach ($comments as $comment) {
                $lines = explode("\n", $comment);
                $cleanedComment = '';
                foreach ($lines as $line) {
                    $trimmedLine = ltrim($line, " *\t");
                    if (!empty($trimmedLine)) {
                        $cleanedComment .= $trimmedLine . "\n";
                    }
                }
                $cleanedComments[] = trim($cleanedComment);
            }
            $comments = $cleanedComments;
        }
        
        if ($occurrences === 1) {
            return $comments[0];
        } elseif ($occurrences === 'all') {
            return $comments;
        } else {
            return array_slice($comments, 0, $occurrences);
        }
    }

    // Get metadata in WordPress format-like
    static function get(string $str, $attrs){
        if (strlen($str) <= 255 && Strings::contains(DIRECTORY_SEPARATOR, $str)){
            $str = Files::getContent($str);
        }

        $content = static::getMultilineComments($str);
        $lines   = Strings::lines($content);

        $fields = []; // assoc. array
        foreach ($lines as $line){
            if (preg_match('/^\s*([a-zA-Z][a-zA-Z _-]*):\s*(.*)/', $line, $fieldMatches)) {
                $fields[$fieldMatches[1]] = $fieldMatches[2];
            }
        }

        // If not empty $attrs then filter by those keys
        if (!empty($attrs)) {
            $filteredFields = [];
            foreach ($attrs as $attr) {
                if (isset($fields[$attr])) {
                    $filteredFields[$attr] = $fields[$attr];
                }
            }
            return $filteredFields;
        }

        return $fields;
    }
}


