import { Injectable }     from '@angular/core';
import { Http, Response } from '@angular/http';

import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';

import { Project } from '../models/Project';

/**
 * Fetching all projects from the BackEnd and transform it into objects.
 */
@Injectable()
export class ProjectsService {
    /**
     * Url where we will request projects
     */
    private getProjectsUrl: string = '/BackEnd/listProjects';

    constructor(private http: Http) {}

    /**
     * Requesting the BackEnd to provide an array of Project to any component
     */
    public getProjects(): Observable<Project[]> {
        return this.http.get(this.getProjectsUrl)
            .map(this.hydrateModel)
            .catch(this.handleError);
    }

    /**
     * Creating an array of Project from JSON response
     */
    private hydrateModel(response: Response) : Project[] {
        let jsonBody = response.json();
        let projects: Project[] = [];

        for (let project of jsonBody) {
            projects.push(Project.fromJSON(project));
        }

        return projects;
    }

    /**
     * Retrieving error message
     */
    private handleError(response: any) {
        let errMsg: string;

        if (response instanceof Response) {
            const body = response.json() || '';
            const err = body.response || JSON.stringify(body);
            errMsg = `${response.status} - ${response.statusText || ''} ${err}`;
        } else {
            errMsg = response.message ? response.message : response.toString();
        }

        return Observable.throw(errMsg);
    }
}
