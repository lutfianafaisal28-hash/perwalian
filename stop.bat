@echo off
title Stop - SI Perwalian Mahasiswa STMIK Bandung

echo Menghentikan PostgreSQL...
"C:\laragon\bin\postgresql\pgsql\bin\pg_ctl.exe" -D "C:\laragon\data\postgresql" stop

echo Selesai.
pause
