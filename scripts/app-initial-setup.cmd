@echo off

pushd "%~dp0\.."

echo Generating new application key...
php artisan key:generate

echo Preparing a fresh database...
php artisan migrate:fresh --seed --seeder=PermissionsSeeder

echo Initial setup complete.

popd
