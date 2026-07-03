# BREW & BEAN CO. - Complete System Rename Script

Write-Host "☕ Updating system to Brew & Bean Co. ..." -ForegroundColor Cyan
Write-Host ""

# 1. Update .env
Write-Host "📝 Updating .env..." -ForegroundColor Yellow
$envContent = Get-Content .env -Raw
$envContent = $envContent -replace 'APP_NAME=.*', 'APP_NAME="Brew & Bean Co."'
$envContent | Set-Content .env
Write-Host "✅ .env updated" -ForegroundColor Green

# 2. Update config/app.php
Write-Host "📝 Updating config/app.php..." -ForegroundColor Yellow
$configContent = Get-Content config/app.php -Raw
$configContent = $configContent -replace "'name' => env\('APP_NAME', '.*'\)", "'name' => env('APP_NAME', 'Brew & Bean Co.')"
$configContent | Set-Content config/app.php
Write-Host "✅ config/app.php updated" -ForegroundColor Green

# 3. Update all blade files
Write-Host "📝 Updating Blade templates..." -ForegroundColor Yellow
$count = 0
Get-ChildItem -Path resources/views -Recurse -File -Filter "*.blade.php" | ForEach-Object {
    $content = Get-Content $_.FullName -Raw -ErrorAction SilentlyContinue
    if ($content -match 'BeanTrackPro|Beans Track|BeanTrack') {
        $content = $content -replace 'BeanTrackPro', 'Brew & Bean Co.'
        $content = $content -replace 'Beans Track', 'Brew & Bean Co.'
        $content = $content -replace 'BeanTrack', 'Brew & Bean Co.'
        $content | Set-Content $_.FullName
        $count++
        Write-Host "   ✅ Updated: $($_.Name)" -ForegroundColor Green
    }
}
Write-Host "✅ $count Blade files updated" -ForegroundColor Green

# 4. Update CSS/JS files
Write-Host "📝 Updating CSS/JS assets..." -ForegroundColor Yellow
$count = 0
Get-ChildItem -Path public -Recurse -File -Include "*.css", "*.js" -ErrorAction SilentlyContinue | ForEach-Object {
    $content = Get-Content $_.FullName -Raw -ErrorAction SilentlyContinue
    if ($content -match 'BeanTrackPro|BeansTrack|BeanTrack') {
        $content = $content -replace 'BeanTrackPro', 'BrewBeanCo'
        $content = $content -replace 'BeansTrack', 'BrewBeanCo'
        $content = $content -replace 'BeanTrack', 'BrewBeanCo'
        $content | Set-Content $_.FullName
        $count++
        Write-Host "   ✅ Updated: $($_.Name)" -ForegroundColor Green
    }
}
Write-Host "✅ $count CSS/JS files updated" -ForegroundColor Green

# 5. Update PHP files (Controllers, Models, etc.)
Write-Host "📝 Updating PHP files..." -ForegroundColor Yellow
$count = 0
Get-ChildItem -Path app -Recurse -File -Filter "*.php" -ErrorAction SilentlyContinue | ForEach-Object {
    $content = Get-Content $_.FullName -Raw -ErrorAction SilentlyContinue
    if ($content -match 'BeanTrackPro|Beans Track|BeanTrack') {
        $content = $content -replace 'BeanTrackPro', 'Brew & Bean Co.'
        $content = $content -replace 'Beans Track', 'Brew & Bean Co.'
        $content = $content -replace 'BeanTrack', 'Brew & Bean Co.'
        $content | Set-Content $_.FullName
        $count++
        Write-Host "   ✅ Updated: $($_.Name)" -ForegroundColor Green
    }
}
Write-Host "✅ $count PHP files updated" -ForegroundColor Green

# 6. Clear all caches
Write-Host "🗑️ Clearing caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
Write-Host "✅ Caches cleared" -ForegroundColor Green

Write-Host ""
Write-Host "🎉 System renamed to Brew & Bean Co. successfully!" -ForegroundColor Cyan
Write-Host "🌐 Visit: http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host ""
Write-Host "☕ Brew & Bean Co. - Your Coffee Shop Management System" -ForegroundColor Magenta
