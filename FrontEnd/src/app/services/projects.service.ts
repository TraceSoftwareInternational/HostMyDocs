import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpResponse } from '@angular/common/http';
import { Project } from '../models/Project';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ProjectsService {

    /**
       * Url where we will request projects
       */
    private getProjectsUrl = '/BackEnd/listProjects';

    public projects: Project[];

    constructor(private http: HttpClient) { }

    /**
     * Requesting the BackEnd to provide an array of Project to any component
     */
    public getProjects(): Observable<HttpResponse<Project[]>> {
        return this.http.get<Project[]>(this.getProjectsUrl, {observe: 'response'});
    }
}
