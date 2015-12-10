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
        $this->svnStatus = trim("
            some message here
            ewrwer

            A       hat.txt
            A+       hat.txt
            C       www/foo/bar.txt
            M       www/foo/baz.txt
            D       www/foo/bim.txt
            E       www\\foo\\bim.txt
            L       www/foo/bim.txt
            ?       www/foo/bim.txt
            !       www/foo/bim.txt

            werwe
            dgs
        "); // TODO
        $this->statusTypesRegex = implode("|",array_map("preg_quote", array_keys($this->statusTypes)));
        $this->statusTypesRegex = "^({$this->statusTypesRegex})\s+(.+)$";
    }

    public function getOutput($requestedNumber){
        $bashStyling = new BashStyling;
        $statusLines = explode(PHP_EOL, $this->svnStatus);
        $fileNumber = 1;
        foreach ($statusLines as &$line) {
            if (preg_match("/{$this->statusTypesRegex}/i", trim($line), $match)) {
                if (is_int($requestedNumber) && $requestedNumber != $fileNumber) {
                    $line = "";
                    $fileNumber++;
                    continue;
                }
                $line = trim($line);
                $replacedLine = "  " . $bashStyling->bold(231, str_pad($fileNumber, 4)) . " ";
                $padding = str_repeat(" ", substr_count(str_pad($match[1], 5), " "));
                $filePath = str_replace("\\", "/", $match[2]);
                switch (strtoupper($match[1])) {
                    case "A":
                    case "A+":
                        $color = 40;
                        $replacedLine .= $bashStyling->bold($color, $match[1]) . $padding
                            . $bashStyling->normal($color, $filePath);
                        break;
                    case "C":
                    case "!":
                        $color = 208;
                        $replacedLine .= $bashStyling->bold($color, $match[1]) . $padding
                            . $bashStyling->normal($color, $filePath);
                        break;
                    case "D":
                        $color = 160;
                        $replacedLine .= $bashStyling->bold($color, $match[1]) . $padding
                            . $bashStyling->normal($color, $filePath);
                        break;
                    case "E":
                    case "I":
                    case "X":
                    case "?":
                        $color = 242;
                        $replacedLine .= $bashStyling->bold($color, $match[1]) . $padding
                            . $bashStyling->normal($color, $filePath);
                        break;
                    case "L":
                        $color = 226;
                        $replacedLine .= $bashStyling->bold($color, $match[1]) . $padding
                            . $bashStyling->normal($color, $filePath);
                        break;
                    case "M":
                    case "R":
                        $color = 33;
                        $replacedLine .= $bashStyling->bold($color, $match[1]) . $padding
                            . $bashStyling->normal($color, $filePath);
                        break;
                    default:
                        $color = 231;
                        $replacedLine .= $bashStyling->bold($color, $match[1]) . $padding . $filePath;
                        break;
                }
                $line = $replacedLine;
                $fileNumber++;
            }
        }
        exit(trim(implode(PHP_EOL, array_filter($statusLines))));
    }

    public function getNumberedLinesArray(){
        if (!$this->numberedLinesArray) {
            $this->numberedLinesArray = array();
            $fileNumber = 1;
            foreach ($statusLines as $line) {
                if (preg_match("/{$this->statusTypesRegex}/i", trim($line), $match)) {
                    $this->numberedLinesArray[$fileNumber] = array(
                        "statusType" => $match[1],
                        "filePath" => $match[2],
                    );
                    $fileNumber++;
                }
            }
        }
        return $this->numberedLinesArray;
    }

    public function getReferencedFileFromNumber($number){
        $numberedLinesArray = $this->getNumberedLinesArray();
        if (array_key_exists($number, $numberedLinesArray)) {
            return $numberedLinesArray[$number];
        }
        return null;
    }

    public function getStatusTypes(){
        return $this->statusTypes;
    }
}
