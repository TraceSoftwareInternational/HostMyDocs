import { Language } from './Language';
import { Version }  from './Version';
import { Project }  from './Project';

describe('Models : Project', () => {
    let languageName: string = 'TypeScript';
    let archivePath: string = '/archive.zip';
    let indexPath: string = '/docs/index.html';

    let versionNumber = '1.1.1';

    let projectName = 'Project Live';

    let project: Project;

    beforeEach(() => {
        let language = new Language(languageName, indexPath, archivePath);
        let version = new Version(versionNumber);
        project = new Project(projectName);

        version.addLanguage(language);
        project.addVersion(version);
    })

    it('object should serialize and deserialize to JSON properly', () => {
        let json = JSON.stringify(project);
        let newProject = JSON.parse(json, Project.reviver);

        expect(project).toEqual(newProject);
        expect(project.name).toEqual(project.name);
    });
});
