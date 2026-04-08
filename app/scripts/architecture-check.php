<?php

declare(strict_types=1);

$scriptDir = __DIR__;
$command = match (PHP_OS_FAMILY) {
    'Windows' => buildWindowsCommand($scriptDir),
    default => buildUnixCommand($scriptDir),
};

passthru($command, $exitCode);
exit($exitCode);

function buildWindowsCommand(string $scriptDir): string
{
    $repoRoot = dirname($scriptDir, 2);
    if (is_dir($repoRoot . DIRECTORY_SEPARATOR . 'app')) {
        chdir($repoRoot);
    }

    return 'powershell -ExecutionPolicy Bypass -File ' . escapeshellarg($scriptDir . DIRECTORY_SEPARATOR . 'architecture-check.ps1');
}

function buildUnixCommand(string $scriptDir): string
{
    $appRoot = dirname($scriptDir);
    $workspaceRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'architecture-check-' . bin2hex(random_bytes(8));
    $appLink = $workspaceRoot . DIRECTORY_SEPARATOR . 'app';
    $tempScriptPath = $workspaceRoot . DIRECTORY_SEPARATOR . 'architecture-check.sh';
    $scriptPath = $scriptDir . DIRECTORY_SEPARATOR . 'architecture-check.sh';

    if (!mkdir($workspaceRoot, 0o777, true) && !is_dir($workspaceRoot)) {
        fwrite(STDERR, "Failed to prepare temporary architecture-check workspace\n");
        exit(1);
    }

    if (!symlink($appRoot, $appLink)) {
        fwrite(STDERR, "Failed to link temporary architecture-check workspace\n");
        exit(1);
    }

    $scriptContents = file_get_contents($scriptPath);
    if ($scriptContents === false) {
        fwrite(STDERR, "Failed to read architecture-check.sh\n");
        exit(1);
    }

    $normalizedScript = str_replace("\r\n", "\n", $scriptContents);

    if (file_put_contents($tempScriptPath, $normalizedScript) === false) {
        fwrite(STDERR, "Failed to prepare architecture-check.sh\n");
        exit(1);
    }

    chdir($workspaceRoot);

    register_shutdown_function(static function () use ($tempScriptPath, $appLink, $workspaceRoot): void {
        if (is_file($tempScriptPath)) {
            @unlink($tempScriptPath);
        }

        if (is_link($appLink)) {
            @unlink($appLink);
        }

        if (is_dir($workspaceRoot)) {
            @rmdir($workspaceRoot);
        }
    });

    return 'bash ' . escapeshellarg($tempScriptPath);
}
