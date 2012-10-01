<?php
/**
 *	CakePHP Shell for importing CSV data
 *
 *	CSV files should be names <table name>.csv and stored in app/data
 *	First row should contain field names
 *
 * Copyright (c) Iain Mullan 2012 www.iainmullan.com
 * @author Iain Mullan
 * @created September 2012
 *
 */
class ImportShell extends AppShell {

	function main() {

		if (empty($this->args)) {
			$this->out("usage: cake import <table name>");
			exit(1);
		}

		$table = $this->args[0];

		ini_set('auto_detect_line_endings', true);

		$filename = APP.'data/'.$table.'.csv';

		if (!is_file($filename)) {
			$this->out("Data file not found");
			exit(1);
		}

		App::uses('ConnectionManager', 'Model');
		$db = ConnectionManager::getDataSource('default');
		$tables = $db->listSources();
		if (array_search($table, $tables) === false) {
			$this->out("Table $table does not exist in your DB");
			exit(1);
		}

		$modelName = Inflector::classify($table);

		$this->out("Model: $modelName");

		$model = ClassRegistry::init($modelName);

		$this->out($filename);

		$fh = fopen($filename, 'r');

		$fields = fgetcsv($fh);

		if (!$fields) {
			$this->out("Data file is empty");
			exit(1);
		}

		$this->out('Fields: '.implode(",",$fields));

		$saved = 0;
		$tried = 0;
		while($line = fgetcsv($fh)) {

			$model->create();

			$data = array_combine($fields, $line);

			if ($model->save($data)) {
				$saved++;
			}

			$tried++;
		}

		$this->out("Imported $saved of $tried records");
	}

}
?>