pipeline {
  options {
    buildDiscarder(logRotator(numToKeepStr: '10', artifactNumToKeepStr: '10'))
  }
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
            sh 'php -dmemory_limit=2G bin/magento s:s:d -f -j=2'
          }
        }

        stage('compile') {
          steps {
            sh 'php -dmemory_limit=2G bin/magento s:d:comp'
          }
        }

      }
    }
      
    stage('check diff') {
        steps {
            script {
                def publisher = LastChanges.getLastChangesPublisher "LAST_SUCCESSFUL_BUILD", "SIDE", "LINE", true, true, "", "", "", "", ""
                publisher.publishLastChanges()
                def changes = publisher.getLastChanges()
                for (commit in changes.getCommits()) {
                  def commitInfo = commit.getCommitInfo()
                  println(commitInfo)
                  println(commitInfo.getCommitId())
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
            echo "blueocean build URL is ${env.RUN_DISPLAY_URL}"
          }
        }

      }
    }

    stage('test') {
      steps {
        echo "build URL is ${env.BUILD_URL}"
        echo "blueocean build URL is ${env.RUN_DISPLAY_URL}"
        script {
          def userInput = input(message: 'Success or error ?',
            parameters: [[$class: 'ChoiceParameterDefinition',
            description:'describing choices', name:'nameChoice', choices: "Success\nError"]
          ])

          if( "${userInput}" == "Success"){
            currentBuild.result = 'SUCCESS'
          } else {
            currentBuild.result = 'FAILURE'
          }
        }
      }
    }

  }
}
