@echo off
echo Select test suite.
echo 1 - All.
echo 2 - Init.
echo 3 - Base.
echo 4 - Settings.
echo 5 - Gui.
echo 6 - Finish.
echo 7 - App.
echo 8 - Integrations.
echo 9 - NoGui.
set /p testMode=Enter a number:

echo.
IF %testMode%==1 goto All
IF %testMode%==2 goto Init
IF %testMode%==3 goto Base
IF %testMode%==4 goto Settings
IF %testMode%==5 goto Gui
IF %testMode%==6 goto Finish
IF %testMode%==7 goto App
IF %testMode%==8 goto Integrations
IF %testMode%==9 goto NoGui

EXIT /B

:All
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose
echo.
pause
exit

:Init
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite Init
echo.
pause
exit

:Base
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite Base
echo.
pause
exit

:Settings
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite Settings
echo.
pause
exit

:App
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite App
echo.
pause
exit


:Gui
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite Gui
echo.
pause
exit

:Integrations
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite Integrations
echo.
pause
exit

:Finish
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite Finish
echo.
pause

:NoGui
php.exe ..\vendor\phpunit\phpunit\phpunit --debug --stderr --verbose --testsuite NoGUI
echo.
pause
exit
