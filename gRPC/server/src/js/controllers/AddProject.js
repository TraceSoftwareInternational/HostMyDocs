const exec = require('child_process').exec;
const AdmZip = require('adm-zip');
const fs = require('fs');

/**
 * AddProjectRequest message
 */
let request = null;

/**
 * directory where data will be extracted and saved
 */
let targetFolder = null;

/**
 * Name of the folder where zip files are archived
 */
const backupFolder = 'archive'

/**
 * Name of the folder where with unzip archives
 */
const docsFolder = 'docs';

/**
 * Process a request
 *
 * @param storagePath where the zip file should be extracted and archived
 * @param call object that contains request information
 * @param callback function to be invoked to terminate client request.
 *     it has 2 parameters : error and response
 */
exports.process = (storagePath, call, callback) => {

    const response = { success: true };

    targetFolder = storagePath;
    request = call.request;

    extractZip();
    backupZip();

    callback(null, response);

    console.log(`Added project : ${request.projectName}`)
    console.log(`\tversion  : ${request.versionNumber}`)
    console.log(`\tlanguage : ${request.languageName}`)
}

const extractZip = () => {
    let pathToCreate = [];

    pathToCreate.push(targetFolder);
    pathToCreate.push(docsFolder);
    pathToCreate.push(request.projectName);
    pathToCreate.push(request.versionNumber);
    pathToCreate.push(request.languageName);

    pathToCreate = pathToCreate.join('/');

    exec(`mkdir -p ${pathToCreate}`);

    const zip = new AdmZip(request.zipFile);
    const zipEntries = zip.getEntries();

    zipEntries.forEach((zipEntry) => {
        zip.extractEntryTo(zipEntry.entryName, pathToCreate, false, true);
    });
}

const backupZip = () => {
    let backupPath = [];

    backupPath.push(targetFolder);
    backupPath.push(backupFolder);

    backupPath = backupPath.join('/');

    exec(`mkdir -p ${backupPath}`);

    backupPath += `/${request.projectName}-${request.versionNumber}-${request.languageName}.zip`;

    fs.writeFileSync(backupPath, request.zipFile);
}
