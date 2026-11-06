[Windows.Media.Transcoding.MediaTranscoder, Windows.Media.Transcoding, ContentType = WindowsRuntime] | Out-Null
[Windows.Media.MediaProperties.MediaEncodingProfile, Windows.Media.MediaProperties, ContentType = WindowsRuntime] | Out-Null
[Windows.Storage.StorageFile, Windows.Storage, ContentType = WindowsRuntime] | Out-Null

Function Convert-Video($srcPath, $dstPath) {
    Write-Host "Transcoding: $srcPath -> $dstPath"
    $asyncSrc = [Windows.Storage.StorageFile]::GetFileFromPathAsync($srcPath)
    while ($asyncSrc.Status -eq "Started") { Start-Sleep -Milliseconds 50 }
    $srcFile = $asyncSrc.GetResults()

    # Ensure dst file exists
    if (Test-Path $dstPath) { Remove-Item $dstPath -Force }
    $dstDir = [System.IO.Path]::GetDirectoryName($dstPath)
    if (!(Test-Path $dstDir)) { New-Item -ItemType Directory -Path $dstDir -Force | Out-Null }
    
    $asyncDstFolder = [Windows.Storage.StorageFolder]::GetFolderFromPathAsync($dstDir)
    while ($asyncDstFolder.Status -eq "Started") { Start-Sleep -Milliseconds 50 }
    $dstFolder = $asyncDstFolder.GetResults()

    $dstFileName = [System.IO.Path]::GetFileName($dstPath)
    $asyncDst = $dstFolder.CreateFileAsync($dstFileName, [Windows.Storage.CreationCollisionOption]::ReplaceExisting)
    while ($asyncDst.Status -eq "Started") { Start-Sleep -Milliseconds 50 }
    $dstFile = $asyncDst.GetResults()

    $transcoder = New-Object Windows.Media.Transcoding.MediaTranscoder
    $profile = [Windows.Media.MediaProperties.MediaEncodingProfile]::CreateMp4([Windows.Media.MediaProperties.VideoEncodingQuality]::HD720p)

    $asyncPrepare = $transcoder.PrepareFileTranscodeAsync($srcFile, $dstFile, $profile)
    while ($asyncPrepare.Status -eq "Started") { Start-Sleep -Milliseconds 50 }
    $prepareOp = $asyncPrepare.GetResults()

    if ($prepareOp.CanTranscode) {
        $asyncTranscode = $prepareOp.TranscodeAsync()
        while ($asyncTranscode.Status -eq "Started") {
            Start-Sleep -Milliseconds 200
        }
        if ($asyncTranscode.Status -eq "Completed") {
            Write-Host "Transcode complete: $dstPath"
        } else {
            Write-Host "Transcode failed or status: $($asyncTranscode.Status) $($asyncTranscode.ErrorCode)"
        }
    } else {
        Write-Host "Cannot transcode: $($prepareOp.FailureReason)"
    }
}

$vDir = "c:\xampp\htdocs\partyhall.1\assets\videos"
if (!(Test-Path $vDir)) { New-Item -ItemType Directory -Path $vDir -Force | Out-Null }

Convert-Video "c:\xampp\htdocs\partyhall.1\IMG_1974.MOV" "c:\xampp\htdocs\partyhall.1\assets\videos\IMG_1974.mp4"
Convert-Video "c:\xampp\htdocs\partyhall.1\IMG_1981.MOV" "c:\xampp\htdocs\partyhall.1\assets\videos\IMG_1981.mp4"

