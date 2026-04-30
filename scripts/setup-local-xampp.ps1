# CMPE 272 - Copy project into XAMPP htdocs and create db_config.php for local MySQL.
# Run in PowerShell: right-click -> Run with PowerShell, OR:
#   cd "...\CMPE-272-project\scripts"
#   powershell -ExecutionPolicy Bypass -File .\setup-local-xampp.ps1

$ErrorActionPreference = "Stop"

$ProjectRoot = Split-Path -Parent $PSScriptRoot
$XamppRoot   = "C:\xampp"
$Htdocs      = Join-Path $XamppRoot "htdocs"
$Dest        = Join-Path $Htdocs "CMPE-272-project"

Write-Host "Project source: $ProjectRoot"
Write-Host "XAMPP htdocs:   $Htdocs"

if (-not (Test-Path -LiteralPath $Htdocs)) {
    Write-Host "ERROR: XAMPP htdocs not found at $Htdocs"
    Write-Host "Install XAMPP or edit this script and set `$XamppRoot to your XAMPP folder."
    exit 1
}

Write-Host "`nCopying project to $Dest ..."
if (-not (Test-Path -LiteralPath $Dest)) {
    New-Item -ItemType Directory -Path $Dest -Force | Out-Null
}
# Mirror project (excludes nothing critical; .git included if present)
Copy-Item -Path (Join-Path $ProjectRoot "*") -Destination $Dest -Recurse -Force

$DbConfigPath = Join-Path $Dest "cookie-business\includes\db_config.php"
$DbConfigLines = @(
    '<?php',
    '// Auto-generated for LOCAL XAMPP - edit remote_apis when you have teammate URLs',
    'return array(',
    "    'host'   => '127.0.0.1',",
    "    'user'   => 'root',",
    "    'pass'   => '',",
    "    'dbname' => 'cmpe272_company_users',",
    "    'company_name' => 'Sweet Crumb Homemade Cookies',",
    "    'company_code' => 'A',",
    "    'remote_apis' => array(",
    '        // Optional: uncomment to test cURL to your own API on same PC:',
    "        // 'http://localhost/CMPE-272-project/cookie-business/api_users.php',",
    '    ),',
    ');',
    ''
)
$DbConfigContent = ($DbConfigLines -join "`r`n")

$IncludesDir = Join-Path $Dest "cookie-business\includes"
if (-not (Test-Path -LiteralPath $IncludesDir)) {
    Write-Host "ERROR: cookie-business\includes not found under copy. Check project structure."
    exit 1
}
Set-Content -LiteralPath $DbConfigPath -Value $DbConfigContent -Encoding UTF8
Write-Host "Wrote: $DbConfigPath (XAMPP default: root, no password)"

# Try to enable curl in XAMPP php.ini
$PhpIni = Join-Path $XamppRoot "php\php.ini"
if (Test-Path -LiteralPath $PhpIni) {
    $bak = $PhpIni + ".bak-" + (Get-Date -Format "yyyyMMddHHmmss")
    Copy-Item -LiteralPath $PhpIni -Destination $bak -Force
    $ini = Get-Content -LiteralPath $PhpIni -Raw
    if ($ini -match ';extension=curl') {
        $ini2 = $ini -replace ';extension=curl', 'extension=curl'
        Set-Content -LiteralPath $PhpIni -Value $ini2 -NoNewline -Encoding UTF8
        Write-Host "Enabled extension=curl in php.ini (backup: $bak)"
        Write-Host ">>> Restart Apache in XAMPP Control Panel <<<"
    } else {
        Write-Host "php.ini: extension=curl already enabled or pattern not found - check manually: $PhpIni"
    }
} else {
    Write-Host "php.ini not found at $PhpIni - enable curl manually if combined_users cURL fails."
}

Write-Host "`n=== NEXT STEPS (you must do these) ===" -ForegroundColor Cyan
Write-Host "1. XAMPP Control Panel: Start Apache AND MySQL (both green)."
Write-Host "2. Import database: open http://localhost/phpmyadmin -> Import -> file:"
Write-Host "   $Dest\cookie-business\sql\cmpe272_company_users.sql"
Write-Host "   OR in cmd: cd $XamppRoot\mysql\bin && mysql.exe -u root < `"$Dest\cookie-business\sql\cmpe272_company_users.sql`""
Write-Host "3. Open site: http://localhost/CMPE-272-project/cookie-business/"
Write-Host "4. Test API: http://localhost/CMPE-272-project/cookie-business/api_users.php"
Write-Host "5. Combined page: http://localhost/CMPE-272-project/cookie-business/combined_users.php"
Write-Host "`nStandalone PHP folder (php -S): C:\Users\018464615\php"
Write-Host "  Add that folder to User PATH if you want 'php' in terminal, OR use:"
Write-Host "  & 'C:\Users\018464615\php\php.exe' -S localhost:8080"
Write-Host "  (from cookie-business folder). For this class, XAMPP + URLs above is enough."
