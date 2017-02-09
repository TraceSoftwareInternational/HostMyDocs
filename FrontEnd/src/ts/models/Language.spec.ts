import { Language } from './Language';

describe('Models : Language', () => {
    let languageName: string = 'TypeScript';
    let archivePath: string = '/archive.zip';
    let indexPath: string = '/docs/index.html';

    let language: Language;

    beforeEach(() => {
        language = new Language(languageName, indexPath, archivePath);
    })

    it('object should serialize and deserialize to JSON properly', () => {
        let json = JSON.stringify(language);
        let newLanguage = JSON.parse(json, Language.reviver);

        expect(language).toEqual(newLanguage);
        expect(language.name).toEqual(newLanguage.name);
    });
});
