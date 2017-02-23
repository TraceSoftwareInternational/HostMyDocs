import { Project } from './Project'
import { Version } from './Version'
import { Language } from './Language'

import { JSONProjectInfo } from './JsonInterfaces';

import { Params } from '@angular/router';

export class ProjectInfo {
    private indexFile: string
    private archiveFile: string

    constructor(
        private project: string,
        private version: string,
        private language: string
    ) {}

    public isValid() : boolean {
        if(this.project === undefined) {
            return false;
        }

        if (this.version === undefined) {
            return false;
        }

        if (this.language === undefined) {
            return false;
        }

        return true;
    }

    public getProject() : string {
        return this.project;
    }

    public getVersion() : string {
        return this.version;
    }

    public getLanguage() : string {
        return this.language;
    }

    public getArchiveFile() : string {
        return this.archiveFile;
    }

    public getIndexFile() : string {
        return this.indexFile;
    }

    public setArchiveFile(path: string) {
        this.archiveFile = path;
    }

    public setindexFile(path: string) {
        this.indexFile = path;
    }

    /**
     * Create a ProjectInfo object from a JSON object
     */
    static fromJSON(json: JSONProjectInfo|string) : ProjectInfo {
        if (typeof json === 'string') {
            return JSON.parse(json, ProjectInfo.reviver)
        } else {
            let projectInfo = Object.create(ProjectInfo.prototype)
            return Object.assign(projectInfo, json);
        }
    }

    /**
     * reviver can be passed as the second parameter to JSON.parse
     * to automatically call ProjectInfo.fromJSON on the resulting value.
     */
    static reviver(key: string, value: any) : any {
        return key === "" ? ProjectInfo.fromJSON(value) : value;
    }
}
