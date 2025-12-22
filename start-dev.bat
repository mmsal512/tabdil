@echo off
title Tabdil Development Server

echo ========================================
echo    Starting Tabdil Development Environment
echo ========================================
echo.

echo [1/3] Starting Laravel Server...
start "Laravel Server" cmd /k "cd /d %~dp0 && php artisan serve"

timeout /t 2 /nobreak > nul

echo [2/3] Starting Vite (npm run dev)...
start "Vite Dev Server" cmd /k "cd /d %~dp0 && npm run dev"

timeout /t 2 /nobreak > nul

echo [3/3] Starting Laravel Scheduler...
start "Laravel Scheduler" cmd /k "cd /d %~dp0 && php artisan schedule:work"

echo.
echo ========================================
echo    All services started successfully!
echo ========================================
echo.
echo    Laravel Server:    http://localhost:8000
echo    Vite Dev Server:   Running in separate window
echo    Scheduler:         Running every 5 minutes
echo.
echo    To stop all services, close all opened windows.
echo ========================================
echo.
pause
