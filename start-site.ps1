param([int]$Port = 8000, [switch]$NoBrowser, [switch]$PrepareOnly)
$ErrorActionPreference = 'Stop'
$appPath = Join-Path $PSScriptRoot 'semester2\laravel'
if ($Port -lt 1024 -or $Port -gt 65535) { throw 'Choose a port from 1024 to 65535.' }

# Use PHP and Composer from PATH, or the existing local tools beside web-lab3.
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
if (-not $phpCommand) {
    $phpCandidates = @('C:\xampp\php\php.exe', (Join-Path $env:USERPROFILE 'Desktop\web-lab3\.local-tools\php\php.exe'))
    $phpPath = $phpCandidates | Where-Object { Test-Path -LiteralPath $_ } | Select-Object -First 1
    if (-not $phpPath) { throw 'Install PHP 8.2+ or add PHP to PATH.' }
    $env:PATH = (Split-Path $phpPath) + ';' + $env:PATH
}
function Invoke-Php {
    param([string[]]$Arguments)
    & php @Arguments
    if ($LASTEXITCODE -ne 0) { throw ('PHP command failed: ' + ($Arguments -join ' ')) }
}
Push-Location $appPath
try {
    Invoke-Php -Arguments @('-r', "exit(PHP_VERSION_ID >= 80200 && extension_loaded('pdo_sqlite') ? 0 : 1);")
    if (-not (Test-Path -LiteralPath 'vendor\autoload.php')) {
        $composerCommand = Get-Command composer -ErrorAction SilentlyContinue
        if ($composerCommand) {
            & composer install --no-interaction --prefer-dist --no-progress
        } else {
            $composerPhar = Join-Path $env:USERPROFILE 'Desktop\web-lab3\.local-tools\bin\composer.phar'
            if (-not (Test-Path -LiteralPath $composerPhar)) { throw 'Install Composer or add it to PATH.' }
            & php $composerPhar install --no-interaction --prefer-dist --no-progress
        }
        if ($LASTEXITCODE -ne 0) { throw 'Composer could not install dependencies. Check internet access and run this script again.' }
    }
    if (-not (Test-Path -LiteralPath '.env')) {
        $databasePath = (Join-Path $appPath 'database\database.sqlite').Replace('\', '/')
        $environment = Get-Content -LiteralPath '.env.example' -Raw -Encoding UTF8
        $environment = $environment.Replace('DB_CONNECTION=mysql', 'DB_CONNECTION=sqlite').Replace('DB_DATABASE=lunar_line', ('DB_DATABASE="' + $databasePath + '"'))
        [IO.File]::WriteAllText((Join-Path $appPath '.env'), $environment, [Text.UTF8Encoding]::new($false))
        New-Item -ItemType File -Path 'database\database.sqlite' -Force | Out-Null
    }
    $env:APP_URL = 'http://127.0.0.1:' + $Port
    Invoke-Php -Arguments @('artisan', 'config:clear')
    if ((Get-Content -LiteralPath '.env' -Encoding UTF8) -match '^APP_KEY=\s*$') { Invoke-Php -Arguments @('artisan', 'key:generate') }
    Invoke-Php -Arguments @('artisan', 'migrate', '--force')
    # The seeder resets availability: only seed an empty catalogue.
    Invoke-Php -Arguments @('artisan', 'app:seed-catalog-if-empty')
    if ($PrepareOnly) { return }
    Write-Host ('Site: ' + $env:APP_URL)
    Write-Host 'Orders are saved locally. Payments and email are not connected.'
    Write-Host 'Press Ctrl+C to stop. Run start-site.ps1 again to restart.'
    if (-not $NoBrowser) { Start-Process $env:APP_URL }
    Invoke-Php -Arguments @('artisan', 'serve', '--host=127.0.0.1', ('--port=' + $Port))
} finally { Pop-Location }
