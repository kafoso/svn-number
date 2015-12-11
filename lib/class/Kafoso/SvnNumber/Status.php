<?php
namespace Kafoso\SvnNumber;

class Status {
    protected $svnStatus;
    protected $statusTypesRegex;
    protected $numberedLinesArray;
    protected $statusTypes = array( // Source: http://stackoverflow.com/a/2036/1879194
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
    );

    public function __construct(){
        exec("svn st", $output);
        $this->svnStatus = $output;
        $this->statusTypesRegex = implode("|",array_map("preg_quote", array_keys($this->statusTypes)));
        $this->statusTypesRegex = "^({$this->statusTypesRegex})\s+(.+)$";
    }

    public function getOutput(array $requestedNumbers = null){
        $bashStyling = new BashStyling;
        $statusLines = $this->svnStatus;
        $fileNumber = 1;
        $outputLines = array();
        foreach ($statusLines as $line) {
            if (preg_match("/{$this->statusTypesRegex}/i", trim($line), $match)) {
                if ($requestedNumbers && false == in_array($fileNumber, $requestedNumbers)) {
                    $line = "";
                    $fileNumber++;
                    continue;
                }
                $line = trim($line);
                $replacedLine = "  " . $bashStyling->bold(str_pad($fileNumber, 4), 231) . " ";
                $padding = str_repeat(" ", substr_count(str_pad($match[1], 5), " "));
                $filePath = str_replace("\\", "/", $match[2]);
                switch (strtoupper($match[1])) {
                    case "A":
                    case "A+":
                        $color = 40;
                        $replacedLine .= $bashStyling->bold($match[1], $color) . $padding
                            . $bashStyling->normal($filePath, $color);
                        break;
                    case "C":
                    case "!":
                        $color = 208;
                        $replacedLine .= $bashStyling->bold($match[1], $color) . $padding
                            . $bashStyling->normal($filePath, $color);
                        break;
                    case "D":
                        $color = 160;
                        $replacedLine .= $bashStyling->bold($match[1], $color) . $padding
                            . $bashStyling->normal($filePath, $color);
                        break;
                    case "E":
                    case "I":
                    case "X":
                    case "?":
                        $color = 242;
                        $replacedLine .= $bashStyling->bold($match[1], $color) . $padding
                            . $bashStyling->normal($filePath, $color);
                        break;
                    case "L":
                        $color = 226;
                        $replacedLine .= $bashStyling->bold($match[1], $color) . $padding
                            . $bashStyling->normal($filePath, $color);
                        break;
                    case "M":
                    case "R":
                        $color = 33;
                        $replacedLine .= $bashStyling->bold($match[1], $color) . $padding
                            . $bashStyling->normal($filePath, $color);
                        break;
                    default:
                        $color = 231;
                        $replacedLine .= $bashStyling->bold($match[1], $color) . $padding . $filePath;
                        break;
                }
                $outputLines[] = $replacedLine;
                $fileNumber++;
            } else {
                $outputLines[] = $line;
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
                if (preg_match("/{$this->statusTypesRegex}/i", trim($line), $match)) {
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

    public function getStatusTypes(){
        return $this->statusTypes;
    }
}
