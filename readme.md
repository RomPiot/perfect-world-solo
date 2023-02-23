Website in /opt/lampp/htdocs

Patch in /opt/lampp/htdocs/patch

Game in /PWServer/146

To create a new patch :

1. move files in \tools\patcher\ec_patch
2. drag & drop the ec_patch folder on patcher.exe
3. wait the generation of ec_patch_0-0.xup
4. cut & paste & rename ec_patch_0-0.xup to /opt/lampp/htdocs/patch
5. rename ec_patch_x-x.xup for what you want
6. stop the server on admin website
7. add new version in \146\website\patch\versions.sw equal to your new file .xup
8. send versions.sw & .xup file on server in /opt/lampp/htdocs/patch
9. send edited files on server /PWServer/146/gamed/...
10. Rerun the server
11. Run the client patcher & update
