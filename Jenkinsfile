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
        sh 'composer install --prefer-dist && composer require --prefer-dist phpstan/phpstan-deprecation-rules:0.12.4 && composer require --prefer-dist bitexpert/phpstan-magento && git diff composer.json'
        echo 'init 2'
        sh '''
            set +x
            cp -rf /codecheck/grumphp.yml  grumphp.yml && cp -rf /codecheck/dev/* dev && cp -rf /codecheck/codecheck codecheck
            set -x
           '''
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
            sh "#cat /tmp/change.add.txt | ~/.composer/vendor/bin/grumphp run"
            sh 'bash codecheck file /tmp/change.add.txt'
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
