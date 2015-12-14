#!/c/xampp/php/php
<?php
use Kafoso\SvnNumber;
use Kafoso\SvnNumber\Bash\Command as BashCommand;
use Kafoso\SvnNumber\SvnAction\Status\Line;

require(readlink(dirname(__FILE__)) . "/lib/bootstrap.php");

try {
    $bashCommand = new BashCommand;
    $svnNumber = new SvnNumber($argv, $bashCommand);
    if (false == $svnNumber->hasAction()) {
        $svnNumber->exec("svn"); // To show help hints
        exit;
    }

    if (in_array($svnNumber->getAction(), array("st", "status"))) {
        $status = $svnNumber->getStatus();
        if ($svnNumber->hasRequestedNumbers()) {
            exit($status->getOutput($svnNumber->getRequestedNumbers()));
        } else {
            exit($status->getOutput(null));
        }
    } else if (in_array($svnNumber->getAction(), array("di", "diff"))) {
        $diff = $svnNumber->getDiff();
        if ($svnNumber->hasRequestedNumbers()) {
            $status = $svnNumber->getStatus();
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
            $status = $svnNumber->getStatus();
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
            $status = $svnNumber->getStatus();
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
