<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber\Bash\Styling as BashStyling;

class Diff extends AbstractSvnAction {
    public function getOutputForFilePaths(array $filePaths) {
        $diff = array();
        foreach ($filePaths as $filePath) {
            $cmd = "svn di " . escapeshellarg($filePath) . " " . $this->svnNumber->getAdditionalArgsStr();
            $output = $this->svnNumber->exec($cmd);
            $diff[] = implode(PHP_EOL, $this->stylize($output));
        }
        return implode(PHP_EOL, $diff);
    }

    public function getOutputAll(){
        $cmd = "svn di " . $this->svnNumber->getAdditionalArgsStr();
        $output = $this->svnNumber->exec($cmd);
        return implode(PHP_EOL, $this->stylize($output));
    }

    protected function stylize(array $svnDiff){
        $lineRegexToColor = array(
            '/^(\=+(\s*))$/' => array(242, null),
            '/^(\+\+\+\s+.*)$/' => array(40, null),
            '/^(---\s+.*)$/' => array(160, null),
            '/^(\+.*)$/' => array(40, null),
            '/^(-.*)$/' => array(160, null),
            '/^(@@ .+ @@).*$/' => array(33, null),
            '/^(Index: .+)$/' => array(226, null)
        );
        $bashStyling = new BashStyling;
        foreach ($svnDiff as &$line) {
            foreach ($lineRegexToColor as $regex => $colors) {
                if (preg_match($regex, ltrim($line), $match)) {
                    list($foreground, $background) = $colors;
                    $count = 1;
                    $line = str_replace(
                        $match[1],
                        $bashStyling->normal($match[1], $foreground, $background, true),
                        $line,
                        $count
                    );
                    break;
                }
            }
        }
        return $svnDiff;
    }
}
