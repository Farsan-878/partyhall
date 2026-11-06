Add-Type -AssemblyName PresentationCore

$inDir = "c:\xampp\htdocs\partyhall.1\assets\images"
$thumbDir = "c:\xampp\htdocs\partyhall.1\assets\images\thumbs"
if (!(Test-Path $thumbDir)) { New-Item -ItemType Directory -Path $thumbDir -Force | Out-Null }

$files = Get-ChildItem -Path $inDir -Filter "*.jpg"

foreach ($f in $files) {
    $dest = Join-Path $thumbDir $f.Name
    try {
        $uri = New-Object System.Uri($f.FullName)
        $decoder = [System.Windows.Media.Imaging.BitmapDecoder]::Create($uri, [System.Windows.Media.Imaging.BitmapCreateOptions]::None, [System.Windows.Media.Imaging.BitmapCacheOption]::OnLoad)
        $frame = $decoder.Frames[0]

        $origW = $frame.PixelWidth
        $origH = $frame.PixelHeight
        $maxDimension = 900.0

        $scale = 1.0
        if ($origW -gt $maxDimension -or $origH -gt $maxDimension) {
            if ($origW -gt $origH) {
                $scale = $maxDimension / $origW
            } else {
                $scale = $maxDimension / $origH
            }
        }

        $newW = [int]($origW * $scale)
        $newH = [int]($origH * $scale)

        $transformed = New-Object System.Windows.Media.Imaging.TransformedBitmap($frame, (New-Object System.Windows.Media.ScaleTransform($scale, $scale)))

        $encoder = New-Object System.Windows.Media.Imaging.JpegBitmapEncoder
        $encoder.QualityLevel = 82
        $encoder.Frames.Add([System.Windows.Media.Imaging.BitmapFrame]::Create($transformed))

        $fs = [System.IO.File]::Create($dest)
        $encoder.Save($fs)
        $fs.Close()
        Write-Host "Thumb generated: $($f.Name) (${newW}x${newH})"
    } catch {
        Write-Host "Error thumb for $($f.Name): $($_.Exception.Message)"
    }
}

