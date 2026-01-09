@echo off
echo Reiniciando servicios de WAMP...
net stop wampapache64
net stop wampmysqld64
timeout /t 2
net start wampapache64
net start wampmysqld64
echo.
echo Servicios reiniciados. Presiona cualquier tecla para cerrar...
pause
