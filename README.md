# backend-laravel
laravel version 10

#Composer install error
Check file php.ini
Open ;extension=fileinfo
#Run check
Cmd:: php -m | findstr fileinfo
PowerShell:: php -m | Select-String fileinfo

