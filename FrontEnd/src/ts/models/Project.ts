/**
 * @module Models
 */

import { Version } from './Version';

import { JSONProject } from './JsonInterfaces';

/**
 * Object holding several Versions object
 */
export class Project {
    /**
     * All available version for the current project
     */
    public versions: Version[]

    /**
     * Should the project seen as expanded in view ?
     */
    public isExpanded: boolean

    constructor(public name: string) {
        this.versions = [];
        this.isExpanded = false;
    }

    /**
     * Change the expanded state of the current Version
     */
    public toggle(event: MouseEvent) {
        this.isExpanded = ! this.isExpanded
        event.stopPropagation()
    }

    /**
     * Adding a single Version object to the current Project
     */
    public addVersion(version: Version) {
        this.versions.push(version);
    }

    /**
     * Convert all versions for the current rsion to JSON
     */
    private getJSONVersions(): Object[] {
        let versions: Object[] = [];

        for (let version of this.versions) {
            versions.push(version.toJSON());
        }

        return versions;
    }

    /**
     * Convert this object to a JSON object
     */
    public toJSON() : Object {
        return Object.assign({}, this, {
            versions: this.getJSONVersions()
        });
    }

    /**
     * Create a Version object from a JSON object
     */
    static fromJSON(json: JSONProject|string) : Project {
        if (typeof json === 'string') {
            return JSON.parse(json, Project.reviver);
        } else {
            let project = Object.create(Project.prototype)
            return Object.assign(project, json, {
                versions: Version.arrayFromJSON(json.versions)
            });
        }
    }

    /**
     * reviver can be passed as the second parameter to JSON.parse
     * to automatically call Version.fromJSON on the resulting value.
     */
    static reviver(key: string, value: any) : any {
        return key === "" ? Project.fromJSON(value) : value;
    }
}
