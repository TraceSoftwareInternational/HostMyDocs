/**
 * Checking that the argv returned by yargs contains the required -s and -p parameter
 * @return false in case of error or true
 */
exports.checkArgv = (argv) => {

    if(argv.s === undefined || argv.s === true) {
        console.error('server is not defined');
        return false;
    }

    if(argv.p === undefined || argv.p === true) {
        console.error('port is not defined');
        return false;
    }

    return true;
}
