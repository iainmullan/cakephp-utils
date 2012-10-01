<?php
/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * A Console task wrapping around Ruckusing commands, for convenience. The name of the task is shortened to 'ruck' for
 * command line brevity!
 *
 * Examples:
 * 	> cake ruck setup
 * 	> cake ruck version
 * 	> cake ruck generate
 * 	> cake ruck migrate <version number>
 *
 * There is also a special task called 'config' which copies DB config details
 * from your cake app into the ruckusing DB config
 *
 * 	> cake ruck config
 *
 * Copyright (c) Iain Mullan 2010 www.ebotunes.com
 * @see http://code.google.com/p/ruckusing/
 * @author Iain Mullan
 * @created 13th January 2010
 *
 */

define('RUCK_DIR', APP.'Vendor/ruckusing');

class RuckShell extends AppShell {

	var $backupConfig = false;

	public function main() {
		if (!is_dir(RUCK_DIR)) {
			$this->out("Ruckusing is NOT installed");
		} else {
			$this->out("Ruckusing is installed in: ".RUCK_DIR);
		}
		$this->out();
	}

	/**
	 * Task methods
	 */

	function setup() {
		$this->_main('db', 'setup');
	}

	function version() {
		$this->_main('db', 'version');
	}

	function add() {
		$this->generate();
	}

	function generate() {
		if (empty($this->args)) {
			$this->out("\nUsage: cake ruck generate <migration name>\n");
			exit();
		}
		$name = $this->args[0];
		$command = 'php generate.php '.$name;
		$this->_exec($command);
	}

	function migrate() {
		if (empty($this->args)) {
			$args = '';
		} else {
			$args = 'VERSION='.$this->args[0];
		}
		$this->_main('db', 'migrate', $args);
	}

	/**
	 * Invoke this task with no arguments to output the current Ruck Db config.
	 * Alternatively give the name of a CakeDB config, to copy those details to the Ruck db config.
	 */
	function config() {

		if (empty($this->args)) {
			$this->_readConfig();
		} else {
			$config = $this->args[0];
			$this->_writeConfig($config);
			$this->_readConfig();
		}

	}

	private function _writeConfig($config) {

		// Copy the Cake app's db config details to the ruckusing db config
		$this->out('Copying $'.$config.' db config');

		// filename APP/vendors/ruckusing/config/database.inc.php
		chdir(RUCK_DIR.'/config');

		// backup the old version first
		if ($this->backupConfig) {
			rename('database.inc.php', 'database.inc.php.'.time());
		}


		// get the cake details
        App::import('Model', 'ConnectionManager');

		$configs = ConnectionManager::enumConnectionObjects();


//        $ds = ConnectionManager::getDataSource($config);
        $dsc = $configs[$config];

		// generate the contents of the whole file
		$contents = "<?php
			\$ruckusing_db_config = array(
				'development' => array(
					'type'      => 'mysql',
					'host'      => '{$dsc['host']}',
					'port'      => '',
					'database'  => '{$dsc['database']}',
					'user'      => '{$dsc['login']}',
					'password'  => '{$dsc['password']}'
				)
			);
		?>\n";

		$file = fopen('database.inc.php', 'w+');
		fwrite($file, $contents);
		fclose($file);
	}

	private function _readConfig() {
		chdir(RUCK_DIR.'/config');
		$file = file('database.inc.php');
		$this->out($file);
	}

	private function _exec($command) {
		chdir(RUCK_DIR);
		$output = '';
		exec($command, $output);
		$this->out($output);
	}

	private function _main($namespace, $task, $args = '') {
		$args = $namespace.':'.$task.' '.$args;
		$command = 'php main.php '.$args;
		$this->_exec($command);
	}

}
?>
