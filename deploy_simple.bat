@echo off
echo 🚀 Déploiement automatique de COBIT Platform...

echo ✅ Ajout des fichiers...
git add .

echo ✅ Commit des changements...
git commit -m "Deploy to production"

echo ✅ Push vers GitHub...
git push origin main

echo 🎯 Votre code est maintenant sur GitHub !
echo 📱 Accédez à ces liens pour déployer automatiquement :

echo.
echo 🔗 HEROKU (Recommandé) :
echo https://heroku.com/deploy?template=https://github.com/Jiheddridi/ProjetKPMG

echo.
echo 🔗 RENDER :
echo https://render.com/deploy?repo=https://github.com/Jiheddridi/ProjetKPMG

echo.
echo 🔗 RAILWAY :
echo https://railway.app/new/template?template=https://github.com/Jiheddridi/ProjetKPMG

echo.
echo ✨ Cliquez sur un des liens ci-dessus pour déployer en 1 clic !
pause
