@echo off
title SI Perwalian Mahasiswa STMIK Bandung
cd /d "%~dp0"

echo ============================================================
echo   SI PERWALIAN MAHASAISWA STMIK BANDUNG
echo   Laravel 11 + PostgreSQL + PHP 8.4
echo ============================================================
echo.

echo [1/2] Menyiapkan PostgreSQL...
"C:\laragon\bin\postgresql\pgsql\bin\pg_isready.exe" -h 127.0.0.1 -p 5432 >nul 2>&1
if errorlevel 1 (
    "C:\laragon\bin\postgresql\pgsql\bin\pg_ctl.exe" -D "C:\laragon\data\postgresql" -l "C:\laragon\data\logs\postgres.log" -w start
    echo       PostgreSQL berhasil dijalankan.
) else (
    echo       PostgreSQL sudah berjalan.
)

echo.
echo [2/2] Menjalankan aplikasi Laravel...
echo.
echo   Buka aplikasi di:  http://127.0.0.1:8000
echo   Tekan Ctrl+C untuk menghentikan aplikasi.
echo.

start /b cmd /c "timeout /t 3 /nobreak >nul & start http://127.0.0.1:8000"

"C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64\php.exe" artisan serve --host=127.0.0.1 --port=8000

echo.
echo   Aplikasi dihentikan.
pause
