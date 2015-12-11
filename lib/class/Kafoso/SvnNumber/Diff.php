<?php
namespace Kafoso\SvnNumber;

use Kafoso\SvnNumber;

class Diff {
    protected $svnNumber;

    public function __construct(SvnNumber $svnNumber){
        $this->svnNumber = $svnNumber;
    }

    public function getOutputForFile($lineInformationer) {
        $cmd = "svn di " . $lineInformationer["filePath"] . " " . $this->svnNumber->getAdditionalArgsStr();
        exec($cmd, $output);
        return implode(PHP_EOL, $this->stylize($output));
    }

    public function getOutputAll(){
        $cmd = "svn di " . $this->svnNumber->getAdditionalArgsStr();
        exec($cmd, $output);
        return implode(PHP_EOL, $this->stylize($output));
    }

    protected function stylize(array $svnDiff){
        $lineRegexToColor = array(
            '/^\=+$/' => array(242, null),
            '/^\+\+\+\s+/' => array(40, null),
            '/^---\s+/' => array(160, null),
            '/^\+(\s*)$/' => array(40, null),
            '/^-(\s+|$)/' => array(160, null),
            '/^@+\s+/' => array(33, null),
        );
        $bashStyling = new BashStyling;
        foreach ($svnDiff as &$line) {
            foreach ($lineRegexToColor as $regex => $colors) {
                if (preg_match($regex, ltrim($line))) {
                    list($foreground, $background) = $colors;
                    $line = $bashStyling->normal($line, $foreground, $background);
                    break;
                }
            }
        }
        return $svnDiff;
    }
}
