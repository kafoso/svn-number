#!/c/xampp/php/php
<?php
use Kafoso\SvnNumber;

define("BASE_DIR", readlink(dirname(__FILE__)));

$loader = require(BASE_DIR . "/lib/vendor/autoload.php");
$loader->addClassMap(require(BASE_DIR . "/lib/autoload_classmap.php"));


$svnNumber = new SvnNumber($argv);
if (false == $svnNumber->hasCommand()) {
    $svnNumber->exec("svn"); // To show help hints
    exit;
}

if (in_array($svnNumber->getCommand(), array("st", "status"))) {
    $status = $svnNumber->getStatus();
    if ($svnNumber->hasRequestedNumber()) {
        exit($status->getOutput($svnNumber->getRequestedNumber()));
    } else {
        exit($status->getOutput(null));
    }
} else if (in_array($svnNumber->getCommand(), array("di", "diff"))) {
    $diff = $svnNumber->getDiff();
    if ($svnNumber->hasRequestedNumber()) {
        $status = $svnNumber->getStatus();
        $lineInformation = $status->getLineInformationFromFileNumber($svnNumber->getRequestedNumber());
        if ($lineInformation) {
            exit($diff->getOutputForFile($lineInformation));
        } else {
            exit("No file found for number: " . $svnNumber->getRequestedNumber());
        }
    } else {
        exit($diff->getOutputAll());
    }
}

$svnNumber->exec(sprintf(
    "svn %s %s",
    $svnNumber->getCommand(),
    $svnNumber->getAdditionalArgsStr()
));
exit;
