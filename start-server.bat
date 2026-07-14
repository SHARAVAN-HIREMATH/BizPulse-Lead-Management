@echo off
REM ============================================================
REM  BizPulse — Local Development Server Launcher
REM  
REM  Usage: Double-click this file or run it from a terminal.
REM
REM  This starts the PHP built-in server with router.php so that:
REM   - Custom 404 page works for invalid URLs
REM   - All PHP pages are served correctly
REM
REM  The server runs on http://localhost:8080
REM
REM  IMPORTANT: Requires XAMPP PHP at C:\xampp\php\php.exe
REM  If PHP is elsewhere, update the path below.
REM ============================================================

echo.
echo  ===================================
echo   BizPulse ^| Starting Local Server
echo  ===================================
echo.
echo  URL: http://localhost:8080
echo  Press Ctrl+C to stop the server.
echo.

C:\xampp\php\php.exe -S localhost:8080 router.php

pause
