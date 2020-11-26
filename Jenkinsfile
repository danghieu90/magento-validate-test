pipeline {
  agent {
    docker {
      image 'codecheck:latest'
    }

  }
  stages {
    stage('init') {
      steps {
        sh 'pwd && whoami && id -u && id -g'
        sh 'composer install'
      }
    }

    stage('deploy') {
      parallel {
        stage('deploy') {
          steps {
            sh 'php -dmemory_limit=3G bin/magento s:s:d'
          }
        }

        stage('compile') {
          steps {
            sh 'php -dmemory_limit=3G bin/magento s:d:comp'
          }
        }

      }
    }

    stage('finish') {
      parallel {
        stage('clear dir') {
          steps {
            cleanWs(cleanWhenSuccess: true, cleanWhenAborted: true, cleanWhenFailure: true, cleanWhenNotBuilt: true, cleanWhenUnstable: true, cleanupMatrixParent: true, deleteDirs: true, disableDeferredWipeout: true)
          }
        }

        stage('build url ') {
          steps {
            echo '"blueocean build URL is ${env.RUN_DISPLAY_URL}"'
          }
        }

      }
    }

  }
}
