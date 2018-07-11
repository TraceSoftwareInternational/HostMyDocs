import { JSONProjectInfo } from './JsonInterfaces';
import { Language } from './Language'
import { Params } from '@angular/router';
import { Project } from './Project'
import { Version } from './Version'

/**
 * Object used in between-component communication
 */
export class ProjectInfo {
    private indexFile: string
    private archiveFile: string
    private currentPage: string

    constructor(
        private project: string,
        private version: string,
        private language: string
    ) { }

    /**
     * Verifies validity of the current object
     */
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

    /**
     * Return current project name
     */
    public getProject() : string {
        return this.project;
    }

    /**
     * Return current version
     */
    public getVersion() : string {
        return this.version;
    }

    /**
     * Return current language name
     */
    public getLanguage() : string {
        return this.language;
    }

    /**
     * Return relative path to the current project archives
     */
    public getArchiveFile() : string {
        return this.archiveFile;
    }

    /**
     * Return relative path to the current project index.html file
     */
    public getIndexFile() : string {
        return this.indexFile;
    }

    /**
     * Return current page
     * (if a user access the app with aparametrized URL)
     */
    public getCurrentPage() : string {
        return this.currentPage;
    }

    /**
     * Set an archive file path
     * @param path string
     */
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
     * Return a representation of this object in matrix notation
     */
    public getMatrixNotation() : string {
        let str = `;project=${encodeURIComponent(this.project)};` +
            `version=${encodeURIComponent(this.version)};` +
            `language=${encodeURIComponent(this.language)};` +
            `currentPage=${encodeURIComponent(this.getBestURL())};`;

        return str;
    }

    /**
     * Return the currentPage if it exists or the indexFile
     */
    public getBestURL() : string {
        if (this.currentPage !== undefined) {
            return this.currentPage;
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

            let newProjectInfo = Object.assign(projectInfo, json, {
                currentPage: decodeURIComponent(json.currentPage),
                language: decodeURIComponent(json.language),
                version: decodeURIComponent(json.version)
            });

            if (newProjectInfo.isValid()) {
                return newProjectInfo;
            }
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
