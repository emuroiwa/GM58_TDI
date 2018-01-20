@echo off
FOR /F "delims=|" %%I IN ('dir "C:\Users\TDIServer\Documents\Navicat\MySQL\servers\sdft\northgate\*.psc" /B /O:D') DO SET NewestFile=%%I
copy "C:\Users\TDIServer\Documents\Navicat\MySQL\servers\sdft\northgate\%NewestFile%" C:\xampp\htdocs\northgate\scripts\dbBackup\backuptransit
rename C:\Users\TDIServer\Documents\backuptransit\*.sql northgatedatabase.sql  
copy C:\Users\TDIServer\Documents\backuptransit\northgatedatabase.sql C:\Users\TDIServer\Google Drive
del C:\Users\TDIServer\Documents\backuptransit\northgatedatabase.sql
