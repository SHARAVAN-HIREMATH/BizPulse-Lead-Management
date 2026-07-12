@echo off
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -proot < "d:\Code\BizPulse - Service ^& Lead Manager\database\bizpulse.sql"
echo Import exit code: %errorlevel%
