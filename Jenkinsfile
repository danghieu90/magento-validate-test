pipeline {
  options {
    buildDiscarder(logRotator(numToKeepStr: '30', artifactNumToKeepStr: '30'))
  }
  agent {
    docker {
      image 'jenkin_codecheck:latest'
    }

  }
  stages {
    stage('init') {
      steps {
        sh 'COMPOSER_MEMORY_LIMIT=-1 composer global require hirak/prestissimo'
        sh 'COMPOSER_MEMORY_LIMIT=-1 composer install'
        sh 'COMPOSER_MEMORY_LIMIT=-1 composer require --dev phpro/grumphp'
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
            sh "git diff-tree --no-commit-id --name-only -r ${env.GIT_COMMIT} >> /tmp/change.txt"
            script {
                def publisher = LastChanges.getLastChangesPublisher "LAST_SUCCESSFUL_BUILD", "SIDE", "LINE", true, true, "", "", "", "", ""
                publisher.publishLastChanges()
                def changes = publisher.getLastChanges()
                for (commit in changes.getCommits()) {
                  def commitInfo = commit.getCommitInfo()
                  def commitInfoId = commitInfo.getCommitId()
                  println(commitInfo)
                  sh "git diff-tree --no-commit-id --name-only -r ${commitInfoId} >> /tmp/change.txt"
                }
            }
            sh "sort /tmp/change.txt | uniq > /tmp/change.add.txt"
            sh "cat /tmp/change.add.txt"
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
