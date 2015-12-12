<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber;
use Kafoso\SvnNumber\Bash\Styling as BashStyling;

class Status extends AbstractSvnAction {
    protected $svnStatus;
    protected $statusTypesRegex = '/^(U|G|M|C|\?|\!|A\s*\+|A|D|S|I|X|~|R|L|E)\s+(.+)$/i';
    protected $lines = array();
    protected $statusHints = array( // Source: http://stackoverflow.com/a/2036/1879194
        "U" => "Working file was updated",
        "G" => "Changes on the repo were automatically merged into the working copy",
        "M" => "Working copy is modified",
        "C" => "This file conflicts with the version in the repo",
        "?" => "This file is not under version control",
        "!" => "This file is under version control but is missing or incomplete",
        "A" => "This file will be added to version control (after commit)",
        "A+" => "This file will be moved (after commit)",
        "D" => "This file will be deleted (after commit)",
        "S" => "This signifies that the file or directory has been switched from the path of the rest of the working copy (using svn switch) to a branch",
        "I" => "Ignored",
        "X" => "External definition",
        "~" => "Type changed",
        "R" => "Item has been replaced in your working copy. This means the file was scheduled for deletion, and then a new file with the same name was scheduled for addition in its place.",
        "L" => "Item is locked",
        "E" => "Item existed, as it would have been created, by an svn update.",
        "> moved" => "Item was moved"
    );

    public function __construct(SvnNumber $svnNumber){
        parent::__construct($svnNumber);
        $this->svnStatus = $svnNumber->getBashCommand()->exec("svn st");
    }

    public function getOutput(array $requestedNumbers = null){
        $bashStyling = new BashStyling;
        $statusLines = $this->svnStatus;
        $fileNumber = 1;
        $outputLines = array();
        $maxColumns = $bashStyling->getMaxTerminalColumns();
        foreach ($statusLines as $line) {
            if (preg_match($this->statusTypesRegex, trim($line), $match)) {
                if ($requestedNumbers && false == in_array($fileNumber, $requestedNumbers)) {
                    $line = "";
                    $fileNumber++;
                    continue;
                }
                $backgroundColor = null;
                if ($fileNumber%2 == 0) {
                    $backgroundColor = 234;
                }
                $line = trim($line);
                $replacedLine = $bashStyling->bold(" " . str_pad($fileNumber, 4, " ", STR_PAD_LEFT) . "  ", 231, $backgroundColor);
                $padding = $bashStyling->normal(
                    str_repeat(" ", substr_count(str_pad($match[1], 5), " ")),
                    null,
                    $backgroundColor
                );
                $filePath = str_pad(str_replace("\\", "/", $match[2]), min(128, $maxColumns));
                switch (preg_replace('/\s+/', ' ', strtoupper($match[1]))) {
                    case "A +":
                        $match[1] = "A+";
                    case "A":
                    case "A+":
                        $color = 40;
                        $replacedLine .= $bashStyling->bold($match[1], $color, $backgroundColor) . $padding
                            . $bashStyling->normal($filePath, $color, $backgroundColor);
                        break;
                    case "C":
                    case "!":
                        $color = 208;
                        $replacedLine .= $bashStyling->bold($match[1], $color, $backgroundColor) . $padding
                            . $bashStyling->normal($filePath, $color, $backgroundColor);
                        break;
                    case "D":
                        $color = 160;
                        $replacedLine .= $bashStyling->bold($match[1], $color, $backgroundColor) . $padding
                            . $bashStyling->normal($filePath, $color, $backgroundColor);
                        break;
                    case "E":
                    case "I":
                    case "X":
                    case "?":
                        $color = 246;
                        $replacedLine .= $bashStyling->bold($match[1], $color, $backgroundColor) . $padding
                            . $bashStyling->normal($filePath, $color, $backgroundColor);
                        break;
                    case "L":
                        $color = 226;
                        $replacedLine .= $bashStyling->bold($match[1], $color, $backgroundColor) . $padding
                            . $bashStyling->normal($filePath, $color, $backgroundColor);
                        break;
                    case "M":
                    case "R":
                        $color = 33;
                        $replacedLine .= $bashStyling->bold($match[1], $color, $backgroundColor) . $padding
                            . $bashStyling->normal($filePath, $color, $backgroundColor);
                        break;
                    default:
                        $color = 231;
                        $replacedLine .= $bashStyling->bold($match[1], $color, $backgroundColor) . $padding . $filePath;
                        break;
                }
                $outputLines[] = $replacedLine;
                $fileNumber++;
            } else if (preg_match('/\>\s+moved/', ltrim($line))) {
                $line = str_repeat(" ", 13) . ltrim($line);
                $outputLines[] = $bashStyling->normal(
                    str_pad(str_replace("\\", "/", $line), min(128, $maxColumns) + 12),
                    231,
                    $backgroundColor
                );
            } else {
                $outputLines[] = $bashStyling->normal(
                    str_pad(str_replace("\\", "/", $line), min(128, $maxColumns) + 12),
                    231,
                    $backgroundColor
                );
            }
        }
        if ($outputLines) {
            array_unshift($outputLines, "");
            $outputLines[] = "";
        }
        return implode(PHP_EOL, $outputLines);
    }

    public function getNumberedLinesArray(){
        if (!$this->numberedLinesArray) {
            $this->numberedLinesArray = array();
            $statusLines = $this->svnStatus;
            $fileNumber = 1;
            foreach ($statusLines as $line) {
                if (preg_match($this->statusTypesRegex, trim($line), $match)) {
                    $this->numberedLinesArray[$fileNumber] = array(
                        "statusType" => $match[1],
                        "filePath" =>  str_replace("\\", "/", $match[2]),
                    );
                    $fileNumber++;
                }
            }
        }
        return $this->numberedLinesArray;
    }

    public function getLineInformationFromFileNumbers(array $numbers){
        $intersection = array_intersect_key(
            $this->getNumberedLinesArray(),
            array_flip($numbers)
        );
        if ($intersection) {
            return $intersection;
        }
        throw new \RuntimeException(sprintf(
            "No line exists for numbers: [%s]",
            implode(",", $numbers)
        ));
    }

    public function getStatusHints(){
        return $this->statusHints;
    }

    public function getSvnStatus(){
        return $this->svnStatus;
    }
}
