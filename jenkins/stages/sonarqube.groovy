// jenkins/stages/sonarqube.groovy

// Vérifie si le token SonarQube est bien disponible
if (!env.SONAR_TOKEN) {
    error "❌ SONAR_TOKEN n'est pas défini dans les credentials Jenkins."
}

echo "🔍 Lancement de l'analyse SonarQube pour le projet TickLab..."

sh """
# Exécute SonarScanner
sonar-scanner \
  -Dsonar.projectKey=ticklab \
  -Dsonar.projectName=TickLab \
  -Dsonar.host.url=https://192.168.100.101/sonarqube \
  -Dsonar.login=${env.SONAR_TOKEN} \
  -Dsonar.sources=app \
  -Dsonar.tests=tests \
  -Dsonar.php.coverage.reportPaths=coverage.xml
"""
echo "✅ Analyse SonarQube terminée. Résultats disponibles sur https://192.168.100.101/sonarqube"
