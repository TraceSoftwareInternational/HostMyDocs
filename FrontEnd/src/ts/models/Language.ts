import { JSONLanguage }    from './JsonInterfaces';

export class Language {
    constructor(
        public name: string,
        public indexPath: string,
        public archivePath: string
    ){}

    /**
     * @param JSONLanguage a JSON array 
     */
    static arrayFromJSON(jsonLanguages: JSONLanguage[]) : Language[] {
        let languages: Language[] = [];
        
        for(let language of jsonLanguages) {
            languages.push(Language.fromJSON(language));
        }

        return languages;
    }

    /**
     * Convert this object to a JSON object
     */
    public toJSON() : Object {
        return Object.assign({}, this);
    }

    /**
     * Create a Language object from a JSON object
     */
    static fromJSON(json: JSONLanguage|string) : Language {
        if (typeof json === 'string') {
            return JSON.parse(json, Language.reviver)
        } else {
            let language = Object.create(Language.prototype)
            return Object.assign(language, json);
        }
    }

    /**
     * reviver can be passed as the second parameter to JSON.parse
     * to automatically call Language.fromJSON on the resulting value.
     */
    static reviver(key: string, value: any) : any {
        return key === "" ? Language.fromJSON(value) : value;
    }
}
