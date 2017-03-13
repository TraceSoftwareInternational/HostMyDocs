const ListProjectsController = require('../controllers/ListProjects');

/**
 * Describing the list command
 */
exports.command = 'list';
exports.description = 'list projects on the server';
/**
 * Function that be called with all parameters from CLI
 */
exports.handler = (argv) => {
    ListProjectsController.process(argv);
}
