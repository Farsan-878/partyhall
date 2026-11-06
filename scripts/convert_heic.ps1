Add-Type -AssemblyName PresentationCore

$files = Get-ChildItem -Path "c:\xampp\htdocs\partyhall.1" -Filter "*.HEIC"
$outDir = "c:\xampp\htdocs\partyhall.1\assets\images"
if (!(Test-Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir -Force | Out-Null
}

foreach ($f in $files) {
    $baseName = $f.BaseName
    $dest = Join-Path $outDir ($baseName + ".jpg")
    try {
        $uri = New-Object System.Uri($f.FullName)
        $decoder = [System.Windows.Media.Imaging.BitmapDecoder]::Create($uri, [System.Windows.Media.Imaging.BitmapCreateOptions]::None, [System.Windows.Media.Imaging.BitmapCacheOption]::OnLoad)
        $frame = $decoder.Frames[0]
        
        $encoder = New-Object System.Windows.Media.Imaging.JpegBitmapEncoder
        $encoder.QualityLevel = 90
        $encoder.Frames.Add($frame)
        
        $fs = [System.IO.File]::Create($dest)
        $encoder.Save($fs)
        $fs.Close()
        Write-Host "Converted: $($f.Name) -> $($dest) ($($frame.PixelWidth)x$($frame.PixelHeight))"
    } catch {
        Write-Host "Error converting $($f.Name): $($_.Exception.Message)"
    }
}

