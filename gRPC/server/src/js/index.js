const server = require('./grpcServer');

const argv = require('yargs')
    .usage('$0 [options]', {
        'p': {
            alias: 'port',
            description: 'port wherez the gRPC server will listen',
            demandOption: 'no port provided'
        },
        'd': {
            alias: 'directory',
            description: 'full path where the archives will be extracted and saved',
            demandOption: 'no path specified',
            normalize: true
        }
    })
    .help('h')
    .alias('h', 'help')
    .argv;

server.start(argv.p, argv.d)
