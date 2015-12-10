#!/c/xampp/php/php
<?php
use Kafoso\SvnNumber;

$loader = require(__DIR__ . "/lib/vendor/autoload.php");
$loader->addClassMap(require(__DIR__ . "/lib/autoload_classmap.php"));

$svnNumber = new SvnNumber($argv);
if (false == $svnNumber->hasCommand()) {
    $svnNumber->exec("svn"); // To show help hints
    exit;
}

if (in_array($svnNumber->getCommand(), ["st", "status"])) {
    $status = $svnNumber->getStatus();
    if ($svnNumber->hasRequestedNumber()) {
        exit($status->getOutput($svnNumber->getRequestedNumber()));
    } else {
        exit($status->getOutput(null));
    }
} else if (in_array($svnNumber->getCommand(), ["di", "diff"])) {
    $diff = $svnNumber->getDiff();
    if ($svnNumber->hasRequestedNumber()) {
        $status = $svnNumber->getStatus();
        $filePath = $status->getReferencedFileFromNumber($svnNumber->getRequestedNumber());
        exit($status->getOutputForFile($filePath));
    } else {
        exit($diff->getOutpuAll());
    }
}

$svnNumber->exec(sprintf(
    "svn %s %s",
    $svnNumber->getCommand(),
    implode(" ", $svnNumber->getAdditionalArgs())
));
exit;
