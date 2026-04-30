$candidates = @(
    'C:\xampp',
    'D:\xampp',
    'E:\xampp',
    'C:\Program Files\XAMPP',
    'C:\Program Files (x86)\XAMPP'
)
Write-Host "Checking common XAMPP locations...`n"
$found = $false
foreach ($p in $candidates) {
    if (Test-Path -LiteralPath $p) {
        $found = $true
        $ctrl = Join-Path $p 'xampp-control.exe'
        $ht = Join-Path $p 'htdocs'
        Write-Host "FOUND: $p"
        Write-Host "  xampp-control.exe exists: $(Test-Path -LiteralPath $ctrl)"
        Write-Host "  htdocs exists: $(Test-Path -LiteralPath $ht)"
        Write-Host ""
    }
}
if (-not $found) {
    Write-Host "No XAMPP in the usual folders (C:\xampp, D:\xampp, Program Files)."
    Write-Host "Search manually: open File Explorer and search This PC for: xampp-control.exe"
}
