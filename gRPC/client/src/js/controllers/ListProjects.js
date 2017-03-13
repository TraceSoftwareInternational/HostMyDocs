const checker = require('../utility/checker');
const grpcClient = require('../grpcClient');
const treeify = require('treeify');

/**
 * @param argv processed array from yargs
 */
exports.process = (argv) => {
    if (checker.checkArgv(argv) === false) {
        process.exit(1);
    }

    this.server = argv.s;
    this.port = argv.p;

    this.client = grpcClient.client(this.server, this.port);

    console.time('list projects');
    this.client.getProjects({}, (error, response) => {
        console.timeEnd('list projects');

        for (project of response.projects) {
            console.log(project.name);
            for (version of project.versions) {
                console.log('  '+version.number);
                for (language of version.languages) {
                    console.log('    ' + language.name);
                }
            }
            console.log('\n');
        }
    });
}
