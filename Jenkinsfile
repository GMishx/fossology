pipeline {
    agent any
    stages {
        stage('Preparation') {
            steps{
                // Install dependencies
                sh '''
                    sudo apt-get -qq update || true
                    sudo apt-get -qq -y install lsb-release sudo php-curl libpq-dev libdbd-sqlite3-perl libspreadsheet-writeexcel-perl debhelper || true
                    sudo utils/fo-installdeps -e -o -y || true
                    sudo apt-get -qq -y install postgresql postgresql-server-dev-all cppcheck php5-pgsql sqlite3 php5-sqlite libsqlite3-0 libsqlite3-dev || true
                    sudo apt-get install -qq -y debhelper libglib2.0-dev libmagic-dev libxml2-dev || true
                    sudo apt-get install -qq -y libtext-template-perl librpm-dev  rpm libpcre3-dev libssl-dev || true
                    sudo apt-get install -qq -y apache2 libapache2-mod-php5 php5-pgsql php-pear php5-cli || true
                    sudo apt-get install -qq -y binutils bzip2 cabextract cpio sleuthkit genisoimage poppler-utils || true
                    sudo apt-get install -qq -y rpm upx-ucl unrar-free unzip p7zip-full p7zip wget git-core subversion || true
                    sudo apt-get install -qq -y libpq-dev libcunit1-dev libcppunit-dev || true
                    sudo apt-get install -qq -y libboost-regex-dev libboost-program-options-dev || true
                    sudo apt-get install -y liblocal-lib-perl || true
                '''
                // To resolve ninka build dependency
				dir(path: 'io-captureoutput') {
					git(url: 'git@code.siemens.com:mirror/io-captureoutput.git', branch: 'master')
					//tag: 'release-1.1104'
					sh '''
						perl Makefile.PL
						make
						make test
						sudo make install
					'''
					deleteDir()
				}
                // Create and patch FOSSology cache
                sh 'sudo mkdir -p /var/local/cache/fossology && sudo chown $(whoami) /var/local/cache/fossology'
                dir(path: 'ninka') {
					git(url: 'git@code.siemens.com:mirror/ninka.git', branch: 'master')
					sh '''
						perl Makefile.PL
						make
						sudo make install
					'''
                }
				dir(path: 'src') {
                    // Install composer dependencies
                    sh 'wget https://linux.siemens.de/pub/tools/FOSSologyNG/php-vendor.tar && tar xvf php-vendor.tar && rm -rf php-vendor.tar'
                }
                sh '''
					sudo /etc/init.d/postgresql start
					sudo /etc/init.d/postgresql status
					sudo -u postgres psql -c 'CREATE USER fossy WITH PASSWORD 'fossy' CREATEDB' || true
					sudo -u postgres psql -c 'CREATE DATABASE fossology OWNER fossy' || true
				'''
			}
        }
		stage('Build') {
			steps {
				// Run the build pipeline
				// Make the project
				echo 'Running compile'
				sh 'make'
			}
		}
		stage('Test'){
			parallel {
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
						sh 'sudo src/vendor/bin/phpcpd src/cli/ src/copyright/ src/decider*/ src/lib/ src/monk/ src/nomos/ src/readmeoss/ src/spdx2/ src/www/'
					}
				}
				stage('PHPCS-test') {
					steps {
						echo 'Running PHPCS test'
						sh 'sudo src/vendor/bin/phpcs --standard=src/fossy-ruleset.xml src/lib/php/*/ src/www/ui/page/ src/www/ui/async/ src/spdx2 src/monk'
					}
				}
				stage('PhpUnit-test') {
					steps {
						echo 'Running PhpUnit test'
						sh 'make'
						sh 'wget https://linux.siemens.de/pub/tools/FOSSologyNG/SPDXTools-v2.1.0.zip'
						sh 'unzip SPDXTools-v2.1.0 && mv SPDXTools-v2.1.0/spdx-tools-2.1.0-jar-with-dependencies.jar src/spdx2/agent_tests/Functional/'
						sh 'rm -rf SPDXTools-v2.1.0*'
						sh 'sudo src/vendor/bin/phpunit -c src/phpunit.xml'
					}
				}
			}
		}
		stage('Package') {
			steps {
                echo 'Running package'
				sh 'make'
				sh 'dpkg-buildpackage -I'
				sh 'mkdir packages'
				sh 'mv ../*.deb packages/'

				// creating archive
				sh 'tar -czvf fossologyng-master-current.tar.gz packages/*.deb'

				// finally copying the artifacts
				sh 'mv fossologyng-$GIT_COMMIT.tar.gz ~/workspace/artifacts/'
				archiveArtifacts(allowEmptyArchive: true, artifacts: '~/workspace/packages', caseSensitive: false, fingerprint: true, onlyIfSuccessful: true)
				
				// adding host key to list of known hosts 
				// sh 'mkdir -p ~/.ssh'
				// sh 'ssh-keyscan -H $PACKAGE_SERVER_NAME > ~/.ssh/known_hosts'

				// super important: adding key to ssh agent, othewiese the private key file is not considered
				// sh '''
				//	echo $PACKAGE_SERVER_KEY > key
				//	chmod 600 key
				//	eval $(ssh-agent -s)
				//	ssh-add  <(echo "$PACKAGE_SERVER_KEY")
				//	ssh-add -L
				//'''

				// finally copying the artifacts
				// sh 'scp -i key fossologyng-$GIT_COMMIT.tar.gz fossy@$PACKAGE_SERVER_NAME:$PACKAGE_SERVER_PATH'
			}
		}
		stage('Deploy') {
			when {
				branch 'ng-master'
			}
			steps {
				echo 'Running deploy-dev'
				// download files (artifacts are not transferred between stages)
				// sh 'wget http://linux.siemens.de/pub/tools/FOSSologyNG/fossologyng-$GIT_COMMIT.tar.gz'
				// adding host key to list of known hosts 
				// sh 'mkdir -p ~/.ssh'
				// sh 'ssh-keyscan -H $DEVDEPLOY_SERVER_NAME > ~/.ssh/known_hosts'
				// super important: adding key to ssh agent, othewiese the private key file is not considered
				// sh 'echo $DEVDEPLOY_SERVER_KEY > key'
				// sh 'chmod 600 key'
				// sh 'eval $(ssh-agent -s)'
				// sh 'ssh-add  <(echo "$DEVDEPLOY_SERVER_KEY")'
				// sh 'ssh-add -L'
				// copying the artifacts
				// scp -i key ~/workspace/artifacts/fossologyng-$GIT_COMMIT.tar.gz ubuntu@$DEVDEPLOY_SERVER_NAME:/home/ubuntu
				// logging in and artifacts install
				// ssh -i key ubuntu@$DEVDEPLOY_SERVER_NAME "tar -x --overwrite -z -f fossologyng-$GIT_COMMIT.tar.gz"
				// ssh -i key ubuntu@$DEVDEPLOY_SERVER_NAME "rm -f packages/fossology-ninka*"
				// ssh -i key ubuntu@$DEVDEPLOY_SERVER_NAME "sudo dpkg -i packages/*.deb"
				// ssh -i key ubuntu@$DEVDEPLOY_SERVER_NAME "sudo apt-get -f install -y"
			}
		}
	}
}