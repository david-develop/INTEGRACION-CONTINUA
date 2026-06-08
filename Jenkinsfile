pipeline {
    agent any

    options {
        //Un build colgado libera el agente tras 30 minutos.
        timeout(time: 30, unit: 'MINUTES')
        // Evita que dos builds simultaneos se pisen Docker Compose.
        disableConcurrentBuilds()
    }

    triggers {
        // Dispara el pipeline automaticamente con el webhook de GitHub.
        // Requiere el plugin "GitHub" y el webhook configurado en
        // GitHub -> Settings -> Webhooks -> http://<jenkins>:8081/github-webhook/
        githubPush()
    }

    stages {

        stage('Clonar repositorio') {
            steps {
                echo 'Clonando repositorio de Ganaderia Livestock...'
                checkout scm
            }
        }

        stage('Pruebas (PHPUnit)') {
            steps {
                echo 'Ejecutando pruebas unitarias con PHPUnit...'
                // PHP y Composer estan instalados en la imagen de Jenkins
                // (ver Dockerfile.jenkins), por eso se ejecutan directamente.
                sh '''
                    composer install --no-interaction --no-progress
                    vendor/bin/phpunit --log-junit build/junit.xml
                '''
            }
        }

        stage('Construir imagen') {
            steps {
                echo 'Construyendo la imagen de la aplicacion web...'
                // Solo la imagen "web"; no se reconstruye el propio Jenkins.
                sh 'docker compose build web'
            }
        }

        stage('Desplegar') {
            steps {
                echo 'Levantando los contenedores web y db...'
                // Solo web y db, para no recrear el contenedor de Jenkins.
                sh 'docker compose up -d db'
                echo 'Esperando a que MySQL acepte conexiones...'
                sh '''
                    for i in $(seq 1 20); do
                        if docker compose exec -T db mysqladmin ping -h localhost -uroot -proot_password --silent; then
                            echo "MySQL listo."
                            break
                        fi
                        echo "Esperando MySQL... intento $i"
                        sleep 3
                    done
                '''
                echo 'Sembrando la base de datos (via stdin, compatible con Docker Desktop)...'
                // El bind-mount de sql/IT.sql no funciona bajo "Docker fuera de Docker";
                // por eso se carga el script por stdin a traves del cliente docker.
                sh 'docker compose exec -T db mysql -uroot -proot_password IT < sql/IT.sql || true'
                sh 'docker compose up -d web'
            }
        }

        stage('Smoke test') {
            steps {
                echo 'Verificando que la aplicacion responde...'
                // IMPORTANTE: el curl corre DENTRO del contenedor de Jenkins, donde
                // "localhost" es el propio Jenkins. Se consulta el servicio "web" por
                // su nombre en la red de Docker (puerto 80 interno), no localhost:8080.
                sh '''
                    for i in $(seq 1 10); do
                        if curl -fsS http://web:80/ > /dev/null; then
                            echo "Aplicacion respondiendo correctamente."
                            exit 0
                        fi
                        echo "Esperando a la aplicacion... intento $i"
                        sleep 5
                    done
                    echo "La aplicacion no respondio a tiempo."
                    exit 1
                '''
            }
        }

        stage('Reporte de integracion') {
            steps {
                echo 'Generando reporte de integracion continua...'
                echo 'Proyecto: Ganaderia Livestock'
                echo 'Repositorio: https://github.com/david-develop/INTEGRACION-CONTINUA'
                echo 'Estado: Codigo probado, construido y desplegado.'
            }
        }
    }

    post {
        // Conserva el reporte de pruebas y publica los resultados JUnit en cada build.
        always {
            junit allowEmptyResults: true, testResults: 'build/junit.xml'
            archiveArtifacts artifacts: 'build/junit.xml', allowEmptyArchive: true, fingerprint: true
        }
        success {
            echo 'Pipeline ejecutado exitosamente. Ganaderia Livestock desplegado.'
        }
        failure {
            echo 'El pipeline fallo. Revisar logs.'
        }
    }
}
