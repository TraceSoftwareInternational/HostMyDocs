const checker = require('../utility/checker');
const fs = require('fs');
const mmm = require('mmmagic');
const Magic = mmm.Magic;
const semver = require('semver');
const grpcClient = require('../grpcClient');

/**
 * @param argv processed array from yargs
 */
exports.process = (argv) => {
    if (checker.checkArgv(argv) === false) {
        process.exit(1);
    }

    this.errorMessage = null;

    /**
     * Representation of an AddProjectRequest message
     */
    this.request = {
        projectName: argv.n,
        versionNumber: argv.v,
        languageName: argv.l
    }

    this.archivePath = argv.f;
    this.server = argv.s;
    this.port = argv.p;

    this.checkParams();

    if (this.errorMessage !== null) {
        console.error(this.errorMessage);
        process.exit(1);
    }

    this.processZipFile();

    this.client = grpcClient.client(this.server, this.port);

    this.sendRequest();
}

/**
 * Checking that all parameters are present and valid
 */
this.checkParams = () => {
    if (this.request.projectName === '') {
        this.errorMessage = 'name is not defined';
    }

    if (this.request.projectName.includes('/')) {
        this.errorMessage = 'name cannot contains slashes';
    }

    if(semver.valid(this.request.versionNumber) === null) {
        this.errorMessage = 'version is invalid'
    }

    if (this.request.languageName === '') {
        this.errorMessage = 'language is not defined';
    }

    if (this.request.languageName.includes('/')) {
        this.errorMessage = 'language cannot contains slashes';
    }

    if (this.archivePath.endsWith('.zip') === false) {
        this.errorMessage = 'provided file is not a ZIP archive';
    }

    return;
}

/**
 * Convert the local ZIP file to binary to be able to send it
 */
this.processZipFile = () => {
    console.time('zip processing');
    this.request.zipFile = fs.readFileSync(this.archivePath);
    console.timeEnd('zip processing');

    return;
}

/**
 * Send this.request to server via gRPC
 */
this.sendRequest = () => {

    console.time('sending project');
    this.client.addProject(this.request, (error, response) => {
        console.log('resp', response);
        console.timeEnd('sending project');
    });
}
