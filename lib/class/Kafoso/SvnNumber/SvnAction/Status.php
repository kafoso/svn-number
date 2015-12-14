<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber;
use Kafoso\SvnNumber\Bash\Styling as BashStyling;
use Kafoso\SvnNumber\SvnAction\Status\Line;

class Status extends AbstractSvnAction {
    const COLUMN_INDENTATION_COUNT_FILEPATH = 12;
    const COLUMN_DEFAULT_COUNT = 128;

    protected $svnStatus;
    protected $statusTypesRegex = '/^(U|G|M|C|\?|\!|A\s*\+|A|D\s+C|D|S|I|X|~|R|L|E)\s+(.+)$/i';
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
        $maxColumns = $this->svnNumber->getBashCommand()->getMaxTerminalColumns()
            - self::COLUMN_INDENTATION_COUNT_FILEPATH;
        foreach ($statusLines as $line) {
            if ($match = $this->validateLine($line)) {
                if ($requestedNumbers && false == in_array($fileNumber, $requestedNumbers)) {
                    $line = "";
                    $fileNumber++;
                    continue;
                }
                $backgroundColor = null;
                if ($fileNumber%2 == 0) {
                    $backgroundColor = static::COLOR_CODE_GRAY_DARK;
                }
                $line = trim($line);
                $replacedLine = $bashStyling->bold(
                    " " . str_pad($fileNumber, 4, " ", STR_PAD_LEFT) . "  ",
                    null,
                    $backgroundColor
                );
                $padding = $bashStyling->normal(
                    str_repeat(" ", substr_count(str_pad($match[1], 5), " ")),
                    null,
                    $backgroundColor,
                    true
                );
                $filePathPaddingRight = min(
                    (self::COLUMN_DEFAULT_COUNT - self::COLUMN_INDENTATION_COUNT_FILEPATH),
                    $maxColumns
                );
                $filePath = str_pad(str_replace("\\", "/", $match[2]), $filePathPaddingRight);
                switch (preg_replace('/\s+/', ' ', strtoupper($match[1]))) {
                    case "A +":
                        $match[1] = "A+";
                    case "A":
                    case "A+":
                        $replacedLine .= $bashStyling->bold($match[1], static::COLOR_CODE_GREEN, $backgroundColor)
                            . $padding
                            . $bashStyling->normal($filePath, static::COLOR_CODE_GREEN, $backgroundColor);
                        break;
                    case "D C":
                        $replacedLine .= $bashStyling->bold("D", static::COLOR_CODE_RED, $backgroundColor)
                            . $bashStyling->bold("C", static::COLOR_CODE_ORANGE, $backgroundColor)
                            . "   "
                            . $bashStyling->escape()
                            . $bashStyling->normal($filePath, static::COLOR_CODE_ORANGE, $backgroundColor);
                        break;
                    case "C":
                    case "!":
                        $replacedLine .= $bashStyling->bold($match[1], static::COLOR_CODE_ORANGE, $backgroundColor)
                            . $padding
                            . $bashStyling->normal($filePath, static::COLOR_CODE_ORANGE, $backgroundColor);
                        break;
                    case "D":
                        $replacedLine .= $bashStyling->bold($match[1], static::COLOR_CODE_RED, $backgroundColor)
                            . $padding
                            . $bashStyling->normal($filePath, static::COLOR_CODE_RED, $backgroundColor);
                        break;
                    case "E":
                    case "I":
                    case "X":
                    case "?":
                        $replacedLine .= $bashStyling->bold($match[1], static::COLOR_CODE_GRAY_LIGHT, $backgroundColor)
                            . $padding
                            . $bashStyling->normal($filePath, static::COLOR_CODE_GRAY_LIGHT, $backgroundColor);
                        break;
                    case "L":
                        $replacedLine .= $bashStyling->bold($match[1], static::COLOR_CODE_YELLOW, $backgroundColor)
                            . $padding
                            . $bashStyling->normal($filePath, static::COLOR_CODE_YELLOW, $backgroundColor);
                        break;
                    case "M":
                    case "R":
                        $replacedLine .= $bashStyling->bold($match[1], static::COLOR_CODE_BLUE, $backgroundColor)
                            . $padding
                            . $bashStyling->normal($filePath, static::COLOR_CODE_BLUE, $backgroundColor);
                        break;
                    default:
                        $replacedLine .= $bashStyling->bold($match[1], static::COLOR_CODE_WHITE, $backgroundColor)
                            . $padding
                            . $filePath;
                        break;
                }
                $outputLines[] = $bashStyling->escape($replacedLine);
                $fileNumber++;
            } else if (preg_match('/^\s+\>\s+(.+)$/', $line, $match)) {
                $leftHandStr = str_repeat(" ", (self::COLUMN_INDENTATION_COUNT_FILEPATH+1)) . "> ";
                $line = $leftHandStr . str_replace("\\", "/", $match[1]);
                $outputLines[] = $bashStyling->normal(
                    str_pad($line, min(self::COLUMN_DEFAULT_COUNT, $maxColumns)),
                    static::COLOR_CODE_WHITE,
                    $backgroundColor,
                    true
                );
            } else {
                $outputLines[] = $bashStyling->escape() . $bashStyling->normal(
                    str_pad(str_replace("\\", "/", $line), min(self::COLUMN_DEFAULT_COUNT, $maxColumns)),
                    null,
                    null,
                    true
                );
            }
        }
        if ($outputLines) {
            array_unshift($outputLines, "");
            $outputLines[] = "";
        }
        return implode(PHP_EOL, $outputLines);
    }

    public function getLines(){
        if (!$this->lines) {
            $this->lines = array();
            $statusLines = $this->svnStatus;
            $fileNumber = 1;
            foreach ($statusLines as $line) {
                if ($match = $this->validateLine($line)) {
                    $this->lines[$fileNumber] = new Line(
                        $fileNumber,
                        $match[2],
                        $match[1]
                    );
                    $fileNumber++;
                }
            }
        }
        return $this->lines;
    }

    public function getLineInformationFromFileNumbers(array $numbers){
        $intersection = array_intersect_key(
            $this->getLines(),
            array_flip($numbers)
        );
        if ($intersection) {
            return $intersection;
        }
        throw new \InvalidArgumentException(sprintf(
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

    protected function validateLine($line) {
        if (preg_match($this->statusTypesRegex, trim($line), $match)) {
            return $match;
        }
        return null;
    }
}
