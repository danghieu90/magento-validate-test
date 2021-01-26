@Library('codecheck')_

pipeline {
  options {
    buildDiscarder(logRotator(numToKeepStr: '30', artifactNumToKeepStr: '30'))
  }

  environment {
        PHP = 'php7.4'
    }
    
  agent {
    docker {
      image 'jenkin_codecheck:latest'
    }
  }
    
  stages {
    stage('init') {
      steps {
        setupTools()
        composerInstall()
      }
    }

    stage('deploy') {
      parallel {
        stage('deploy') {
          steps {
            sh '#php -dmemory_limit=2G bin/magento s:s:d -f -j=2'
          }
        }

        stage('compile') {
          steps {
            sh '#php -dmemory_limit=2G bin/magento s:d:comp'
          }
        }

      }
    }
      
    stage('check diff') {
        steps {
            script {
                validateCode()
            }
        }
    }

    stage('test') {
      steps {
         echo "build URL is ${env.BUILD_URL}"
         echo "blueocean build URL is ${env.RUN_DISPLAY_URL}"
        // script {
        //   def userInput = input(message: 'Success or error ?',
        //     parameters: [[$class: 'ChoiceParameterDefinition',
        //     description:'describing choices', name:'nameChoice', choices: "Success\nError"]
        //   ])

        //   if( "${userInput}" == "Success"){
        //     currentBuild.result = 'SUCCESS'
        //   } else {
        //     currentBuild.result = 'FAILURE'
        //   }
        // }
      }
    }
  }
  post {
        always {
            cleanWs(cleanWhenSuccess: true, cleanWhenAborted: true, cleanWhenFailure: true, cleanWhenNotBuilt: true, cleanWhenUnstable: true, cleanupMatrixParent: true, deleteDirs: true, disableDeferredWipeout: true)
            echo "build URL is ${env.BUILD_URL}"
            echo "blueocean build URL is ${env.RUN_DISPLAY_URL}"
        }
    }
}
