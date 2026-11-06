$path = "c:\xampp\htdocs\partyhall.1\IMG_1974.MOV"
$bytes = [System.IO.File]::ReadAllBytes($path)
# Check first 500 bytes for ftyp brand
$header = [System.Text.Encoding]::ASCII.GetString($bytes[0..500])
Write-Host "Header: $header"

