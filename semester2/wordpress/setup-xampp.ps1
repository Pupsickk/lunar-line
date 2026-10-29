param([string]$XamppRoot='C:\xampp')
$ErrorActionPreference='Stop'
$php=Join-Path $XamppRoot 'php\php.exe'
$htdocs=Join-Path $XamppRoot 'htdocs'
$target=Join-Path $htdocs 'lunar-wordpress'
if (!(Test-Path $php)) {throw 'PHP XAMPP не найден. Укажите -XamppRoot.'}
if (Test-Path $target) {throw "Папка $target уже существует. Существующий сайт не изменён."}
$temp=Join-Path ([IO.Path]::GetTempPath()) ('lunar-wp-'+[guid]::NewGuid())
New-Item -ItemType Directory $temp | Out-Null
Write-Host 'Загружается официальный дистрибутив WordPress. Существующие сайты не меняются.'
Invoke-WebRequest -Uri 'https://wordpress.org/latest.zip' -OutFile (Join-Path $temp 'wordpress.zip')
Expand-Archive -Path (Join-Path $temp 'wordpress.zip') -DestinationPath $temp
Move-Item -Path (Join-Path $temp 'wordpress') -Destination $target
Write-Host "Файлы готовы: $target"
Write-Host 'Включите Apache и MySQL. В phpMyAdmin импортируйте create-database.sql.'
Write-Host 'Откройте http://localhost/lunar-wordpress и пройдите мастер. Пароль администратора задайте самостоятельно.'
Start-Process 'http://localhost/lunar-wordpress'
