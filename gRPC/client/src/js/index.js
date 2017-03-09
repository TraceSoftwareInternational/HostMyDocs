#!/usr/bin/env node

const argv = require('yargs')
    .usage('$0 [options] <add|list>', {
        's': {
            alias: 'server',
            description: 'address of the server to query',
            demandOption: 'no server address provided',
            global: true
        },
        'p': {
            alias: 'port',
            description: 'port of the server',
            demandOption: 'no port provided',
            global: true
        }
    })
    .demandOption(['s', 'p'])
    .commandDir('commands')
    .demandCommand(1, 'no command provided')
    .help('h')
    .alias('h', 'help')
    .argv;
