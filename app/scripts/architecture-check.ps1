# Architecture enforcement checks (PowerShell version)
# Run from any location — all paths anchored to $PSScriptRoot.

$ErrorCount = 0
$WarningCount = 0

# Anchor paths to the script's own location so the checks work regardless of cwd.
# Scripts live at <repoRoot>/app/scripts — appRoot is one level up, repoRoot two levels up.
$scriptDir         = $PSScriptRoot
$appRoot           = Split-Path -Parent $scriptDir
$appSrcDir         = Join-Path $appRoot "src"
$repositoriesDir   = Join-Path $appSrcDir "Repositories"
$controllersDir    = Join-Path $appSrcDir "Controllers"
$viewModelsDir     = Join-Path $appSrcDir "ViewModels"
$servicesDir       = Join-Path $appSrcDir "Services"
$viewsDir          = Join-Path $appSrcDir "Views"
$exceptionsDir     = Join-Path $appSrcDir "Exceptions"
$infrastructureDir = Join-Path $appSrcDir "Infrastructure"

$repositoriesGlob  = Join-Path $repositoriesDir "*.php"
$controllersGlob   = Join-Path $controllersDir "*.php"
$servicesGlob      = Join-Path $servicesDir "*.php"

Write-Host "=== Architecture Checks ===" -ForegroundColor Cyan

# ─────────────────────────────────────────────────────────────
# LAYER BOUNDARY CHECKS
# ─────────────────────────────────────────────────────────────

Write-Host "`n── Layer Boundaries ──" -ForegroundColor White

# 1. Repositories must not import ViewModels
Write-Host "Checking: Repositories must not import ViewModels..."
$repoVm = Select-String -Path $repositoriesGlob -Pattern "use App\\ViewModels" -ErrorAction SilentlyContinue
if ($repoVm) {
    Write-Host "❌ FAIL: Repository imports ViewModel" -ForegroundColor Red
    $repoVm | ForEach-Object { Write-Host $_.Line }
    $ErrorCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# 2. Controllers must not import Repositories
Write-Host "Checking: Controllers must not import Repositories..."
$controllerRepo = Select-String -Path $controllersGlob -Pattern "use App\\Repositories\\" -ErrorAction SilentlyContinue
if ($controllerRepo) {
    Write-Host "❌ FAIL: Controller imports Repository directly:" -ForegroundColor Red
    $controllerRepo | ForEach-Object { Write-Host "$($_.Filename):$($_.LineNumber): $($_.Line)" }
    $ErrorCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# 3. Controllers must not have $this->*Repository property access
Write-Host "Checking: Controllers must not access `$this->*Repository..."
$repoAccess = Select-String -Path $controllersGlob -Pattern '\$this->[a-zA-Z]*Repository' -ErrorAction SilentlyContinue
if ($repoAccess) {
    Write-Host "❌ FAIL: Controller accesses Repository property:" -ForegroundColor Red
    $repoAccess | ForEach-Object { Write-Host "$($_.Filename):$($_.LineNumber): $($_.Line)" }
    $ErrorCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# 4. ViewModels must not import Repositories or Services
Write-Host "Checking: ViewModels must not import Repositories/Services..."
$vmImports = Get-ChildItem -Path $viewModelsDir -Filter "*.php" -Recurse -ErrorAction SilentlyContinue |
    Select-String -Pattern "use App\\(Repositories|Services)" -ErrorAction SilentlyContinue
if ($vmImports) {
    Write-Host "❌ FAIL: ViewModel imports Repository or Service" -ForegroundColor Red
    $vmImports | ForEach-Object { Write-Host $_.Line }
    $ErrorCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# 5. No raw SQL in Services
Write-Host "Checking: Services must not contain raw SQL..."
$sqlInServices = Select-String -Path $servicesGlob -Pattern '->prepare\(|->query\(' -ErrorAction SilentlyContinue
if ($sqlInServices) {
    Write-Host "❌ FAIL: Service contains raw SQL:" -ForegroundColor Red
    $sqlInServices | ForEach-Object { Write-Host "$($_.Filename):$($_.LineNumber): $($_.Line)" }
    $ErrorCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# ─────────────────────────────────────────────────────────────
# ENUM MAGIC STRING CHECKS
# ─────────────────────────────────────────────────────────────

Write-Host "`n── Enum Usage ──" -ForegroundColor White

# 6. No hardcoded role/tier IDs
Write-Host "Checking: No hardcoded ID constants for enums..."
$hardcodedIds = Select-String -Path $servicesGlob -Pattern "const.*_ROLE_ID|const.*PRICE_TIER_|const.*CUSTOMER.*= [0-9]" -ErrorAction SilentlyContinue
if ($hardcodedIds) {
    Write-Host "⚠️ WARNING: Hardcoded enum IDs found (create backed enum):" -ForegroundColor Yellow
    $hardcodedIds | ForEach-Object { Write-Host "$($_.Filename):$($_.LineNumber): $($_.Line)" }
    $WarningCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# ─────────────────────────────────────────────────────────────
# EXCEPTION HANDLING CHECKS
# ─────────────────────────────────────────────────────────────

Write-Host "`n── Exception Handling ──" -ForegroundColor White

# 7. NotFoundException should exist
Write-Host "Checking: NotFoundException exists..."
if (Test-Path (Join-Path $exceptionsDir "NotFoundException.php")) {
    Write-Host "✅ PASS (NotFoundException.php exists)" -ForegroundColor Green
} else {
    Write-Host "⚠️ WARNING: NotFoundException.php not found" -ForegroundColor Yellow
    $WarningCount++
}

# ─────────────────────────────────────────────────────────────
# INFRASTRUCTURE CHECKS
# ─────────────────────────────────────────────────────────────

Write-Host "`n── Infrastructure ──" -ForegroundColor White

# 8. PathResolver should exist
Write-Host "Checking: PathResolver exists..."
if (Test-Path (Join-Path $infrastructureDir "PathResolver.php")) {
    Write-Host "✅ PASS (PathResolver.php exists)" -ForegroundColor Green
} else {
    Write-Host "⚠️ WARNING: PathResolver.php not found" -ForegroundColor Yellow
    $WarningCount++
}

# 9. Path resolution should be in Infrastructure, not Services
Write-Host "Checking: Path resolution centralized in Infrastructure..."
$pathInServices = Select-String -Path $servicesGlob -Pattern "is_dir\('/app/public'\)|realpath\(__DIR__.*public" -ErrorAction SilentlyContinue
if ($pathInServices) {
    Write-Host "⚠️ WARNING: Path resolution in Services (move to Infrastructure/PathResolver):" -ForegroundColor Yellow
    $pathInServices | ForEach-Object { Write-Host "$($_.Filename):$($_.LineNumber): $($_.Line)" }
    $WarningCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# ─────────────────────────────────────────────────────────────
# CONTROLLER SIZE CHECK
# ─────────────────────────────────────────────────────────────

Write-Host "`n── Controller Size ──" -ForegroundColor White

# 10. CmsDashboardController should be under 200 lines
Write-Host "Checking: CmsDashboardController size..."
$controllerPath = Join-Path $controllersDir "CmsDashboardController.php"
if (Test-Path $controllerPath) {
    $lines = (Get-Content $controllerPath).Count
    if ($lines -gt 200) {
        Write-Host "⚠️ WARNING: CmsDashboardController has $lines lines (target: <200)" -ForegroundColor Yellow
        $WarningCount++
    } else {
        Write-Host "✅ PASS ($lines lines)" -ForegroundColor Green
    }
} else {
    Write-Host "Skipped: File not found" -ForegroundColor Gray
}

# ─────────────────────────────────────────────────────────────
# MODEL USAGE ENFORCEMENT
# ─────────────────────────────────────────────────────────────

Write-Host "`n── Model Usage ──" -ForegroundColor White

# 11. Repository entity methods returning array must be explicitly typed
#     Valid forms (per convention):
#       - @return Foo[]
#       - @return Foo[]|null
#       - @return ?Foo[]
#       - @return array<int, Foo>
#       - @return array<string, Foo>
#       - @return list<Foo>
#       - @return \App\Models\Foo[]
#     Raw `array` return type with no typed PHPDoc → violation.
Write-Host "Checking: Repository entity methods return Models..."

# Typed-collection PHPDoc: @return followed by either Type[] or Type<...> (with any generic payload).
# Matches optional leading ?, optional leading \ namespace prefix, and optional trailing |null union.
$typedReturnPattern = '@return\s+(?:null\s*\|\s*)?\??\\?[A-Za-z_][\w\\]*(?:\[\]|<[^>]+>)(?:\s*\|\s*null)?'

$repoFiles  = Get-ChildItem -Path $repositoriesGlob -ErrorAction SilentlyContinue
$violations = @()

foreach ($file in $repoFiles) {
    $content  = Get-Content $file.FullName -Raw
    $fileName = $file.Name

    # Match public function declarations with array return types (array | ?array).
    $methodPattern = 'public function (\w+)\s*\([^)]*\)\s*:\s*\??\s*array'
    $regexMatches  = [regex]::Matches($content, $methodPattern)

    foreach ($match in $regexMatches) {
        $methodName = $match.Groups[1].Value
        $checkKey   = "$fileName::$methodName"

        # Only entity-like accessors are under this rule
        if ($methodName -notmatch '^(find|get)') { continue }

        # Look for the PHPDoc block immediately preceding this method.
        # "Immediately" = only whitespace/newlines between the docblock's closing */ and the method.
        $before       = $content.Substring(0, $match.Index)
        $lastClose    = $before.LastIndexOf('*/')
        $hasTypedReturn = $false
        if ($lastClose -ne -1) {
            $lastOpen = $before.LastIndexOf('/**', $lastClose)
            if ($lastOpen -ne -1) {
                $gap = $before.Substring($lastClose + 2)
                if ($gap -match '^\s*$') {
                    $docBlock = $before.Substring($lastOpen, ($lastClose + 2) - $lastOpen)
                    if ($docBlock -match $typedReturnPattern) {
                        $hasTypedReturn = $true
                    }
                }
            }
        }

        if (-not $hasTypedReturn) {
            $violations += "$checkKey() returns array without typed-collection PHPDoc (e.g. @return Foo[] or @return array<int, Foo>)"
        }
    }
}

if ($violations.Count -gt 0) {
    Write-Host "❌ FAIL: Repository entity methods return array without typed PHPDoc:" -ForegroundColor Red
    $violations | ForEach-Object { Write-Host "  $_" }
    $ErrorCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# 12. Views must not contain date()/strtotime()
Write-Host "Checking: Views do not contain date()/strtotime()..."
$viewDateFormatting = Get-ChildItem -Path $viewsDir -Filter "*.php" -Recurse -ErrorAction SilentlyContinue |
    Select-String -Pattern "\bdate\s*\(|\bstrtotime\s*\(" -ErrorAction SilentlyContinue
if ($viewDateFormatting) {
    Write-Host "⚠️ WARNING: View contains date formatting (move to ViewModel):" -ForegroundColor Yellow
    $viewDateFormatting | ForEach-Object { Write-Host "  $($_.Filename):$($_.LineNumber): $($_.Line.Trim())" }
    $WarningCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# 13. Views must not perform arithmetic on capacity/seat properties
Write-Host "Checking: Views do not perform seat/capacity calculations..."
$viewArithmetic = Get-ChildItem -Path $viewsDir -Filter "*.php" -Recurse -ErrorAction SilentlyContinue |
    Select-String -Pattern "(capacity|sold|seats|Capacity|Sold|Seats)\w*\s*[-+*/]|[-+*/]\s*\`$?\w*(capacity|sold|seats|Capacity|Sold|Seats)" -ErrorAction SilentlyContinue
if ($viewArithmetic) {
    Write-Host "⚠️ WARNING: View performs arithmetic (move to ViewModel):" -ForegroundColor Yellow
    $viewArithmetic | ForEach-Object { Write-Host "  $($_.Filename):$($_.LineNumber): $($_.Line.Trim())" }
    $WarningCount++
} else {
    Write-Host "✅ PASS" -ForegroundColor Green
}

# ─────────────────────────────────────────────────────────────
# SUMMARY
# ─────────────────────────────────────────────────────────────

Write-Host "`n═══════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "                      SUMMARY" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Cyan

if ($ErrorCount -eq 0 -and $WarningCount -eq 0) {
    Write-Host "✅ All checks passed" -ForegroundColor Green
    exit 0
} elseif ($ErrorCount -eq 0) {
    Write-Host "⚠️ $WarningCount warning(s) - review recommended" -ForegroundColor Yellow
    exit 0
} else {
    Write-Host "❌ $ErrorCount error(s), $WarningCount warning(s)" -ForegroundColor Red
    Write-Host "   Errors block merge. Warnings require review."
    exit 1
}

