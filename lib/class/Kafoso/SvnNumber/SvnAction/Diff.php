<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber\Bash\Styling as BashStyling;

class Diff extends AbstractSvnAction {
    public function getOutputForFilePaths(array $filePaths) {
        $diff = array();
        $filePaths = array_map(function($filePath){
            return escapeshellarg($filePath);
        }, $filePaths);
        $cmd = sprintf(
            "svn diff %s %s",
            implode(" ", $filePaths),
            $this->svnNumber->getAdditionalArgsStr()
        );
        $diffLinesArray = $this->svnNumber->exec($cmd);
        return implode(PHP_EOL, $this->stylizeDiffLines($diffLinesArray));
    }

    public function getOutputAll(){
        $cmd = "svn diff " . $this->svnNumber->getAdditionalArgsStr();
        $diffLinesArray = $this->svnNumber->exec($cmd);
        return implode(PHP_EOL . PHP_EOL, $this->stylizeDiffLines($diffLinesArray));
    }

    protected function stylizeDiffLines(array $diffLinesArray){
        foreach ($diffLinesArray as &$line) {
            if (preg_match("/^Index: /", $line)) {
                $line = PHP_EOL . $line;
            }
            $line = $this->stylizeDiff($line);
        }
        return $diffLinesArray;
    }

    protected function stylizeDiff($diff){
        $lineRegexToColor = array(
            '/^(Index: .+)$/' => array(static::COLOR_CODE_YELLOW, null),
            '/^(\=+(\s*))$/' => array(static::COLOR_CODE_GRAY, null),
            '/^(\+\+\+\s+.*)$/' => array(static::COLOR_CODE_GREEN, null),
            '/^(---\s+.*)$/' => array(static::COLOR_CODE_RED, null),
            '/^(\+.*)$/' => array(static::COLOR_CODE_GREEN, null),
            '/^(-.*)$/' => array(static::COLOR_CODE_RED, null),
            '/^(@@ .+ @@).*$/' => array(static::COLOR_CODE_TEAL, null),
        );
        $bashStyling = new BashStyling;
        foreach ($lineRegexToColor as $regex => $colors) {
            if (preg_match($regex, ltrim($diff), $match)) {
                list($foreground, $background) = $colors;
                $count = 1;
                $diff = str_replace(
                    $match[1],
                    $bashStyling->normal($match[1], $foreground, $background, true),
                    $diff,
                    $count
                );
                break;
            }
        }
        return $diff;
    }
}
