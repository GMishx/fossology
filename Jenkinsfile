pipeline {
  agent any
  stages {
    stage('Build') {
      agent any
      steps {
        sh '''sudo apt-get -qq update || true &&
sudo apt-get -qq -y install lsb-release sudo php-curl libpq-dev libdbd-sqlite3-perl libspreadsheet-writeexcel-perl debhelper || true &&
            sudo utils/fo-installdeps -e -o -y || true &&
            sudo apt-get -qq -y install postgresql postgresql-server-dev-all cppcheck php5-pgsql sqlite3 php5-sqlite libsqlite3-0 libsqlite3-dev || true &&
            sudo apt-get install -qq -y debhelper libglib2.0-dev libmagic-dev libxml2-dev || true &&
            sudo apt-get install -qq -y libtext-template-perl librpm-dev  rpm libpcre3-dev libssl-dev || true &&
            sudo apt-get install -qq -y apache2 libapache2-mod-php5 php5-pgsql php-pear php5-cli || true &&
            sudo apt-get install -qq -y binutils bzip2 cabextract cpio sleuthkit genisoimage poppler-utils || true &&
            sudo apt-get install -qq -y rpm upx-ucl unrar-free unzip p7zip-full p7zip wget git-core subversion || true &&
            sudo apt-get install -qq -y libpq-dev libcunit1-dev libcppunit-dev || true &&
            sudo apt-get install -qq -y libboost-regex-dev libboost-program-options-dev || true &&
            sudo apt-get install -y liblocal-lib-perl || true'''
        dir(path: 'io-captureoutput') {
          git(url: 'git@code.siemens.com:mirror/io-captureoutput.git', branch: 'master', credentialsId: 'gaurav')
          sh '''perl Makefile.PL &&
                make &&
                make test &&
                sudo make install'''
          deleteDir()
        }
        
        sh 'sudo mkdir -p /var/local/cache/fossology && sudo chown $(whoami) /var/local/cache/fossology'
        dir(path: 'ninka') {
          git(url: 'git@code.siemens.com:mirror/ninka.git', branch: 'master', credentialsId: 'gaurav')
          sh 'perl Makefile.PL && make && sudo make install'
        }
        
        echo 'Running compile'
        sh 'make'
      }
    }
    stage('Testing') {
      parallel {
        stage('Testing') {
          steps {
            dir(path: 'src') {
              sh 'wget https://linux.siemens.de/pub/tools/FOSSologyNG/php-vendor.tar && tar xvf php-vendor.tar && rm -rf php-vendor.tar'
            }
            
            sh '''sudo /etc/init.d/postgresql start &&
            sudo /etc/init.d/postgresql status &&
            sudo -u postgres psql -c "CREATE USER fossy WITH PASSWORD \'fossy\' CREATEDB" || true &&
            sudo -u postgres psql -c "CREATE DATABASE fossology OWNER fossy" || true &&
            sudo apt-get -qq -y install cppcheck'''
          }
        }
        stage('C-test') {
          steps {
            echo 'Running C test'
            sh 'make all test-lib test-monk test-nomos'
            sh 'cppcheck -q -isrc/nomos/agent_tests/testdata/NomosTestfiles/ -isrc/testing/dataFiles/ src/'
          }
        }
        stage('PHPCPD-test') {
          steps {
            echo 'Running PHPCPD test'
            sh 'src/vendor/bin/phpcpd src/cli/ src/copyright/ src/decider*/ src/lib/ src/monk/ src/nomos/ src/readmeoss/ src/spdx2/ src/www/'
          }
        }
        stage('PHPCS-test') {
          steps {
            echo 'Running PHPCS test'
            sh 'src/vendor/bin/phpcs --standard=src/fossy-ruleset.xml src/lib/php/*/ src/www/ui/page/ src/www/ui/async/ src/spdx2 src/monk'
          }
        }
        stage('PhpUnit-test') {
          steps {
            echo 'Running PhpUnit test'
            sh 'make'
            sh '''wget https://linux.siemens.de/pub/tools/FOSSologyNG/SPDXTools-v2.1.0.zip &&
                    unzip SPDXTools-v2.1.0 &&
                    mv SPDXTools-v2.1.0/spdx-tools-2.1.0-jar-with-dependencies.jar src/spdx2/agent_tests/Functional/ &&
                    rm -rf SPDXTools-v2.1.0*'''
            sh 'src/vendor/bin/phpunit -c src/phpunit.xml'
          }
        }
      }
    }
    stage('Deployment') {
      steps {
        echo 'Deploy'
      }
    }
  }
}