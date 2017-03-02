import { JSONProjectInfo } from './JsonInterfaces';
import { Language } from './Language'
import { Params } from '@angular/router';
import { Project } from './Project'
import { Version } from './Version'

export class ProjectInfo {
    private indexFile: string
    private archiveFile: string
    private currentPage: string

    constructor(
        private project: string,
        private version: string,
        private language: string
    ) { }

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

        if(this.indexFile === undefined || this.currentPage === undefined) {
            return true;
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

    public getCurrentPage() : string {
        return this.currentPage;
    }

    public setArchiveFile(path: string) {
        this.archiveFile = path;
    }

    public setindexFile(path: string) {
        this.indexFile = path;
    }

    public setCurrentPage(path: string) {
        this.currentPage = path;
    }

    /**
     * return a representation of this object in matrix notation
     */
    public getMatrixNotation() : string {
            let str = `;project=${this.project};` +
            `version=${this.version};` +
            `language=${this.language};` +
            `currentPage=${encodeURIComponent(this.getBestURL())};`;

            return str;
    }

    /**
     * Return the currentPage if it exists or the indexFile
     */
    public getBestURL() : string {
        if (this.currentPage !== undefined) {
            return decodeURIComponent(this.currentPage);
        }

        return this.indexFile;
    }

    /**
     * Create a ProjectInfo object from a JSON object
     */
    static fromJSON(json: JSONProjectInfo|string) : ProjectInfo {
        if (typeof json === 'string') {
            return JSON.parse(json, ProjectInfo.reviver)
        } else {
            let projectInfo = Object.create(ProjectInfo.prototype)
            return Object.assign(projectInfo, json, {
                currentPage: () => {
                    if (json.currentPage !== undefined) {
                        decodeURIComponent(json.currentPage)
                    }
                }
            });
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
