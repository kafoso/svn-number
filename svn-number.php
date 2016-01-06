#!/c/xampp/php/php
<?php
use Kafoso\SvnNumber;
use Kafoso\SvnNumber\Bash\Command as BashCommand;
use Kafoso\SvnNumber\SvnAction\Diff;
use Kafoso\SvnNumber\SvnAction\Status;
use Kafoso\SvnNumber\SvnAction\Status\Line;
use Kafoso\SvnNumber\SvnAction\Status\Staging;

require(readlink(dirname(__FILE__)) . "/lib/bootstrap.php");

try {
    $bashCommand = new BashCommand;
    $svnNumber = new SvnNumber($argv, $bashCommand);
    if (false == $svnNumber->hasAction()) {
        $svnNumber->exec("svn"); // To show help hints
        exit;
    }

    $staging = new Staging(__DIR__ . "/data/staging.txt");

    function printStatus(Status $status, $forceOutputAll = false){
        if (false === $forceOutputAll && $status->getSvnNumber()->hasRequestedNumbers()) {
            exit($status->getOutput($status->getSvnNumber()->getRequestedNumbers()));
        } else {
            exit($status->getOutput(null));
        }
    }

    if (in_array($svnNumber->getAction(), array("commit-staged", "stage", "stage-all", "unstage", "unstage-all"))) {
        switch ($svnNumber->getAction()) {
            case "commit-staged":
                $stagedFilePaths = $staging->getStagedFilePaths();
                $status = new Status($svnNumber, $staging);
                $commitingFilePaths = array();
                foreach ($status->getLines() as $line) {
                    if (in_array($line->getFilePath(), $stagedFilePaths)) {
                        $commitingFilePaths[] = escapeshellarg($line->getFilePath());
                    }
                }
                $svnNumber->exec(sprintf(
                    "svn commit %s %s",
                    implode(" ", $commitingFilePaths),
                    $svnNumber->getAdditionalArgsStr()
                ));
                $staging->clear()->save();
                $status = new Status($svnNumber, $staging);
                exit(printStatus($status, true));
            case "stage":
                if ($svnNumber->hasRequestedNumbers()) {
                    $status = new Status($svnNumber, $staging);
                    $lines = array_intersect_key(
                        $status->getLines(),
                        array_flip($svnNumber->getRequestedNumbers())
                    );
                    foreach ($lines as $line) {
                        echo "Staged file: " . $line->getFilePath() . PHP_EOL;
                        $staging->addLine($line);
                    }
                    $staging->save();
                    exit(printStatus($status, true));
                } else {
                    throw new \InvalidArgumentException("Command 'svn-number stage #' requires at least one number");
                }
                break;
            case "stage-all":
                $status = new Status($svnNumber, $staging);
                $staging->clear();
                foreach ($status->getLines() as $line) {
                    echo "Staged file: " . $line->getFilePath() . PHP_EOL;
                    $staging->addLine($line);
                }
                $staging->save();
                exit(printStatus($status));
            case "unstage":
                if ($svnNumber->hasRequestedNumbers()) {
                    $status = new Status($svnNumber, $staging);
                    $lines = array_intersect_key(
                        $status->getLines(),
                        array_flip($svnNumber->getRequestedNumbers())
                    );
                    foreach ($lines as $line) {
                        echo "Unstaged file: " . $line->getFilePath() . PHP_EOL;
                        $staging->removeLine($line);
                    }
                    $staging->save();
                    exit(printStatus($status, true));
                } else {
                    throw new \InvalidArgumentException("Command 'svn-number unstage #' requires at least one number");
                }
                break;
            case "unstage-all":
                $staging->clear()->save();
                echo "Unstaged all files." . PHP_EOL;
                $status = new Status($svnNumber, $staging);
                exit(printStatus($status));
        }
    } else if (in_array($svnNumber->getAction(), array("st", "status"))) {
        $status = new Status($svnNumber, $staging);
        printStatus($status);
    } else if (in_array($svnNumber->getAction(), array("di", "diff"))) {
        $diff = new Diff($svnNumber);
        if ($svnNumber->hasRequestedNumbers()) {
            $status = new Status($svnNumber, $staging);
            $allLinesInformation = $status->getLineInformationFromFileNumbers($svnNumber->getRequestedNumbers());
            $filePaths = array_map(function(Line $line){
                return $line->getFilePath();
            }, $allLinesInformation);
            exit($diff->getOutputForFilePaths($filePaths));
        } else {
            exit($diff->getOutputAll());
        }
    } else if (in_array($svnNumber->getAction(), array(
            "ci",
            "commit",
        ))) {
        /**
         * Apply same action to multiple files (bulk). E.g.:
         *      # svn commit foo.txt bar.txt -m "Two files committed"
         */
        if ($svnNumber->hasRequestedNumbers()) {
            $status = new Status($svnNumber, $staging);
            $filePaths = array_map(function(Line $line){
                return escapeshellarg($line->getFilePath());
            }, $status->getLineInformationFromFileNumbers($svnNumber->getRequestedNumbers()));
            $svnNumber->exec(sprintf(
                "svn %s %s %s",
                $svnNumber->getAction(),
                implode(" ", $filePaths),
                $svnNumber->getAdditionalArgsStr()
            ));
            exit;
        }
    } else if (in_array($svnNumber->getAction(), array(
            "add",
            "ann",
            "annotate",
            "blame",
            "del",
            "delete",
            "praise",
            "remove",
            "resolve",
            "revert",
            "rm",
        ))) {
        /**
         * Apply same action to multiple files individually through a loop. E.g.:
         *      # svn revert foo.txt
         *      # svn revert bar.txt
         */
        if ($svnNumber->hasRequestedNumbers()) {
            $status = new Status($svnNumber, $staging);
            $allLinesInformations = $status->getLineInformationFromFileNumbers($svnNumber->getRequestedNumbers());
            foreach ($allLinesInformations as $number => $line) {
                $svnNumber->exec(sprintf(
                    "svn %s %s %s",
                    $svnNumber->getAction(),
                    escapeshellarg($line->getFilePath()),
                    $svnNumber->getAdditionalArgsStr()
                ));
            }
            exit;
        }
    }

    $output = $svnNumber->exec(sprintf(
        "svn %s %s",
        $svnNumber->getAction(),
        $svnNumber->getAdditionalArgsStr()
    ));
    exit(implode(PHP_EOL, $output));
} catch (\Exception $e) {
    $message = implode(PHP_EOL, array_map(function($v){
        return str_repeat(" ", 4) . $v;
    }, preg_split("/\n|\r\n?/", $e->getMessage(), -1, PREG_SPLIT_NO_EMPTY)));
    exit("svn-number Exception: " . PHP_EOL . $message);
}
