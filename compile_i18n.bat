@echo off

cd app\locale\en_US\LC_MESSAGES
msgfmt validator.po -o validator.mo
cd ..\..\..\..

cd app\locale\es_AR\LC_MESSAGES
msgfmt validator.po -o validator.mo
cd ..\..\..\..

