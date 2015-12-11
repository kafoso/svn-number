#!/c/xampp/php/php
<?php
use Kafoso\SvnNumber;

define("BASE_DIR", readlink(dirname(__FILE__)));

$loader = require(BASE_DIR . "/lib/vendor/autoload.php");
$loader->addClassMap(require(BASE_DIR . "/lib/autoload_classmap.php"));

try {
    $svnNumber = new SvnNumber($argv);
    if (false == $svnNumber->hasCommand()) {
        $svnNumber->exec("svn"); // To show help hints
        exit;
    }

    if (in_array($svnNumber->getCommand(), array("st", "status"))) {
        $status = $svnNumber->getStatus();
        if ($svnNumber->hasRequestedNumbers()) {
            exit($status->getOutput($svnNumber->getRequestedNumbers()));
        } else {
            exit($status->getOutput(null));
        }
    } else if (in_array($svnNumber->getCommand(), array("di", "diff"))) {
        $diff = $svnNumber->getDiff();
        if ($svnNumber->hasRequestedNumbers()) {
            $status = $svnNumber->getStatus();
            $allLinesInformation = $status->getLineInformationFromFileNumbers($svnNumber->getRequestedNumbers());
            $filePaths = array_map(function($v){
                return $v["filePath"];
            }, $allLinesInformation);
            exit($diff->getOutputForFilePaths($filePaths));
        } else {
            exit($diff->getOutputAll());
        }
    } else if (in_array($svnNumber->getCommand(), array("revert", "resolve"))) {
        if ($svnNumber->hasRequestedNumbers()) {
            $status = $svnNumber->getStatus();
            $allLinesInformations = $status->getLineInformationFromFileNumbers($svnNumber->getRequestedNumbers());
            foreach ($allLinesInformations as $number => $lineInformation) {
                $svnNumber->exec(sprintf(
                    "svn revert %s %s",
                    $lineInformation["filePath"],
                    $svnNumber->getAdditionalArgsStr()
                ));
            }
            exit;
        }
    }

    $svnNumber->exec(sprintf(
        "svn %s %s",
        $svnNumber->getCommand(),
        $svnNumber->getAdditionalArgsStr()
    ));
    exit;
} catch (\Exception $e) {
    $message = implode(PHP_EOL, array_map(function($v){
        return str_repeat(" ", 4) . $v;
    }, preg_split("/\n|\r\n?/", $e->getMessage(), -1, PREG_SPLIT_NO_EMPTY)));
    exit("svn-number Exception: " . PHP_EOL . $message);
}
