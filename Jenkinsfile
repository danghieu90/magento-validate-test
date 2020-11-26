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

    stage('test') {
      steps {
        echo "blueocean build URL is ${env.RUN_DISPLAY_URL}"

    }
  }

  stage('finish') {
    steps {
      cleanWs(cleanWhenSuccess: true, cleanWhenAborted: true, cleanWhenFailure: true, cleanWhenNotBuilt: true, cleanWhenUnstable: true, cleanupMatrixParent: true, deleteDirs: true, disableDeferredWipeout: true)
    }
  }

}
}