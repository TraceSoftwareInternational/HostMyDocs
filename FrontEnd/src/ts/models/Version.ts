/**
 * @module Models
 */

import { Language } from './Language';

import { JSONLanguage, JSONVersion } from './JsonInterfaces';

export class Version {
    public languages: Language[]

    constructor(public number: string) {
        this.languages = [];
    }

    public addLanguage(language: Language) {
        this.languages.push(language);
    }

    static arrayFromJSON(jsonVersions: JSONVersion[]) : Version[] {
        let versions: Version[] = [];

        for(let version of jsonVersions) {
            versions.push(Version.fromJSON(version));
        }

        return versions;
    }

    /**
     * Convert all languages for the current Version to JSON
     */
    private getJSONLanguages(): Object[] {
        let languages: Object[] = [];

        for (let language of this.languages) {
            languages.push(language.toJSON());
        }

        return languages;
    }

    /**
     * Convert this object to a JSON object
     */
    public toJSON() : Object {
        return Object.assign({}, this, {
            languages: this.getJSONLanguages()
        });
    }

    /**
     * Create a Version object from a JSON object
     */
    static fromJSON(json: JSONVersion|string) : Version {
        if (typeof json === 'string') {
            return JSON.parse(json, Language.reviver);
        } else {
            let version = Object.create(Version.prototype)
            return Object.assign(version, json, {
                languages: Language.arrayFromJSON(json.languages)
            });
        }
    }

    /**
     * reviver can be passed as the second parameter to JSON.parse
     * to automatically call Version.fromJSON on the resulting value.
     */
    static reviver(key: string, value: any) : any {
        return key === "" ? Version.fromJSON(value) : value;
    }
}
