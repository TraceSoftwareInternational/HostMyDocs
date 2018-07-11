import { Language } from './Language';
import { Version }  from './Version';

import { JSONLanguage } from './JsonInterfaces';

describe('Models : Version', () => {
    let languageName: string = 'TypeScript';
    let archivePath: string = '/archive.zip';
    let indexPath: string = '/docs/index.html';

    let versionNumber = '1.1.1';

    let version: Version;

    beforeEach(() => {
        let language = new Language(languageName, indexPath, archivePath);

        version = new Version(versionNumber);
        version.addLanguage(language)
    })

    it('object should serialize and deserialize to JSON properly', () => {
        let json = JSON.stringify(version)
        let newVersion = JSON.parse(json, Version.reviver)

        expect(version).toEqual(newVersion)
        expect(version.number).toEqual(newVersion.number)
    });
});
