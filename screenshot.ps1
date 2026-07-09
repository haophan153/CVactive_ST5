param(
    [Parameter(Mandatory=$true)]
    [string]$Url,
    
    [Parameter(Mandatory=$true)]
    [string]$OutPath,
    
    [int]$Width = 1440,
    [int]$Height = 900,
    [int]$WaitMs = 1500
)

$edge = "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
$dir = Split-Path -Parent $OutPath
if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }

# Build a wrapper HTML that waits, then redirects to the target URL
# This gives time for JS/HTML to settle before screenshot
$wrapperHtml = @"
<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>body{margin:0;padding:0}</style></head>
<body><script>
  setTimeout(() => { window.location.replace('$Url'); }, 200);
</script></body></html>
"@
$wrapperPath = "C:\tmp\cvactive_test\_wrapper.html"
$wrapperHtml | Out-File -FilePath $wrapperPath -Encoding utf8

# Use --virtual-time-budget to allow time before screenshot
& $edge --headless=new --disable-gpu --no-sandbox `
  --window-size=$Width,$Height `
  --hide-scrollbars `
  --virtual-time-budget=$WaitMs `
  --screenshot=$OutPath `
  "file:///$wrapperPath" 2>&1 | Out-Null

# Cleanup
Remove-Item $wrapperPath -ErrorAction SilentlyContinue

if (Test-Path $OutPath) {
    $size = (Get-Item $OutPath).Length
    Write-Host "OK: $OutPath ($size bytes)"
} else {
    Write-Host "FAIL: $OutPath"
}