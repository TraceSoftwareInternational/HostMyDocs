import { Language } from './Language';
import { Project }  from './Project';
import { ProjectInfo } from './ProjectInfo';
import { Version }  from './Version';

describe('Models: ProjectInfo', () => {
    let languageName = 'TypeScript';
    let archivePath = '/archive.zip';
    let indexPath = '/docs/index.html';
    let currentPage = '/docs/index.html#link'
    let versionNumber = '1.1.1';
    let projectName = 'Project Live';
    let project: Project;
    let projectInfo: ProjectInfo;

    beforeEach(() => {
        let language = new Language(languageName, indexPath, archivePath);
        let version = new Version(versionNumber);
        project = new Project(projectName);

        version.addLanguage(language);
        project.addVersion(version);

        projectInfo = new ProjectInfo(project.name, version.number, language.name);
        projectInfo.setindexFile(language.indexPath);
        projectInfo.setArchiveFile(language.archivePath);
    })

    it('object is valid', () => {
        expect(projectInfo.isValid()).toBe(true);
    })

    it('should return indexFile if no currentPage set', () => {
        expect(projectInfo.getBestURL()).toBe(indexPath);
    })

    it('should return currentPage instead of indexFile if currentPage is set', () => {
        projectInfo.setCurrentPage(currentPage);

        expect(projectInfo.getBestURL()).toBe(currentPage);
    })
})
