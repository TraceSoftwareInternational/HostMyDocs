const AddProjectController = require('../controllers/AddProject');

/**
 * Describing the add command for yargs
 */
exports.command = 'add [options]';
exports.description = 'add a project to the server';
exports.builder = (yargs) => {
    yargs.option('n', {
        alias: 'name',
        type: 'string',
        description: 'name of the project',
        demandOption: 'no name found'
    })
    .option('v', {
        alias: 'version',
        type: 'string',
        description: 'version number of the project to add',
        demandOption: 'no version found'
    })
    .option('l', {
        alias: 'language',
        type: 'string',
        description: 'language of the project',
        demandOption: true
    })
    .option('f', {
        alias: 'file',
        type: 'string',
        description: 'path to the zip archive to upload',
        normalize: true,
        demandOption: true
    })
    .demandOption(['n', 'v', 'l', 'f'])
    .help('h')
    .alias('h', 'help')
}
/**
 * Function that be called with all parameters from CLI
 */
exports.handler = (argv) => {
    AddProjectController.process(argv);
}
