@echo off

pushd "%~dp0\.."

if not exist ".env" (
    echo Creating .env file from .env.example...
    copy .env.example .env
) else (
    echo .env file is already present.
)

if not exist ".\vendor\laravel\sail\runtimes\8.1\Dockerfile" (
    rem Reference: https://laravel.com/docs/10.x/sail#installing-composer-dependencies-for-existing-projects
    echo Installing Laravel Sail...
    docker run --rm ^
        -u "1000:1000" ^
        -v "%cd%:/var/www/html" ^
        -w /var/www/html ^
        laravelsail/php81-composer:latest ^
        composer install --ignore-platform-reqs
) else (
    echo Laravel Sail is already installed.
)

popd
