const fs = require('fs');
const path = require('path');

/**
 * directory where projects are extracted
 */
let docsFolder = null;

/**
 * Where all the backups of uploaded projects are stored
 */
let archiveFolder = null;

/**
 * Array of Projects to return
 */
let storedProjects = [];

/**
 * Process a request
 *
 * @param storagePath where the server should read folders
 * @param call object that contains request information
 * @param callback function to be invoked to terminate client request.
 *     it has 2 parameters : error and response
 */
exports.process = (storagePath, call, callback) => {
    docsFolder = path.join(storagePath, 'docs');
    archiveFolder = path.join(storagePath, 'archives');

    listProjects();

    callback(null, { projects: storedProjects });

    storedProjects = [];
}

/**
 * Lists all projects located in targetFolder
 */
const listProjects = () => {
    const projectsOnDisk = fs.readdirSync(docsFolder);

    for (project of projectsOnDisk) {
        let tempProject = {
            name: project,
            versions: []
        };

        listVersions(tempProject, path.join(docsFolder, tempProject.name));

        storedProjects.push(tempProject);
    }
}

/**
 *
 * @param {Project} project where we will add available versions
 * @param {string} projectDir where the project is located
 */
const listVersions = (project, projectDir) => {
    const versionsOnDisk = fs.readdirSync(projectDir);

    for (version of versionsOnDisk) {
        let tempVersion = {
            number: version,
            languages: []
        };

        listLanguages(tempVersion, path.join(projectDir, tempVersion.number));

        project.versions.push(tempVersion);
    }
}

/**
 *
 * @param {Version} version where we will add available languages
 * @param {string} versionDir where the project is located
 */
const listLanguages = (version, versionDir) => {
    const languagesOnDisk = fs.readdirSync(versionDir);

    for (language of languagesOnDisk) {

        let archiveFileName = path.join(versionDir, language)
            .replace(docsFolder, '')
            .substr(1)
            .replace(/\//g, '-');

        archiveFileName += '.zip';

        let tempLanguage = {
            name: language,
            indexFilePath: path.join(versionDir, language, 'index.html'),
            archiveFilePath: path.join(archiveFolder, archiveFileName)
        };

        version.languages.push(tempLanguage);
    }
}
