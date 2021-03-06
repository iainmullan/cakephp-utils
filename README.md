# CakePHP Utils

## DebugShell

Quickly set the app Debug level in your `app/Config/core.php` file

    cake util.debug <debug level>


## CounterShell

A Console task for CakePHP to update all counterCache values. Useful during development, after a bulk data import, or when adding a new counterCache column for existing data.

In theory could be adapted to suit any type of beforeSave logic.

    # cake util.counter

## ImportShell

CakePHP Shell for importing CSV data

CSV files should be stored in `app/data/<table name>.csv`

First row should contain field names

    # cake util.import users
    
... would load data from app/data/users.csv


## RuckShell 

A Console task wrapping around Ruckusing commands, for convenience. The name of the task is shortened to `ruck` for command line brevity!

Examples:

    # cake util.ruck setup
    # cake util.ruck version
    # cake util.ruck generate
    # cake util.ruck migrate <version number>

There is also a special `config` task which copies DB config details from your cake app into the ruckusing DB config

    # cake util.ruck config <data source name>


