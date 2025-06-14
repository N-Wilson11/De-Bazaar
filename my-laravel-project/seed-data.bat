@echo off
echo Seeding the database with realistic data...
php artisan app:seed-realistic-data --fresh
echo.
echo Done!
pause
