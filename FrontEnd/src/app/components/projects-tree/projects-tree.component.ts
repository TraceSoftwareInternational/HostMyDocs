import { Component, EventEmitter, Output, OnInit } from '@angular/core';

import { ProjectsService } from '../../services/projects.service';

import { Language } from '../../models/Language';
import { Version } from '../../models/Version';
import { Project } from '../../models/Project';
import { ProjectInfo } from '../../models/ProjectInfo';
import { HttpErrorResponse } from '@angular/common/http';

@Component({
  selector: 'tsi-projects-tree',
  templateUrl: './projects-tree.component.html',
  styleUrls: ['./projects-tree.component.sass']
})
export class ProjectsTreeComponent implements OnInit {
    /**
     * Event emitter to notify parent component of archivePath and indexPath from the selected language
     */
    @Output()
    projectSelection = new EventEmitter<ProjectInfo>();

    /**
     * Parameter for the tree filter pipe
     */
    filterText = '';

    /**
     * All projects sent by the server
     */
    projects: Array<Project> = [];

    constructor(private projectsService: ProjectsService) { }

    /**
     * Fetch projects from BackEnd at component initialization.
     */
    ngOnInit(): void {
        this.projectsService.getProjects().subscribe(
            (response) => {
                console.log('got', response.body);
                this.projects = response.body;
            },
            (err: HttpErrorResponse) => {
                if (err.error instanceof Error) {
                    // A client-side or network error occurred.
                    console.error('A client-side error occurred:', err.error.message);
                } else {
                    // Backend returns unsuccessful response codes such as 404, 500 etc.
                    console.error('Backend returned status code: ', err.status);
                    console.error('Response body:', err.error);
                }
            }
        );
    }

    /**
     * Given a project, return its highest version
     */
    getLastVersion(project: Project): string {
        return project.versions[project.versions.length - 1].number;
    }

    /**
     * Sending an event to the parent, to display documentation
     */
    notifyParent(event: MouseEvent, project: Project, version: Version, language: Language): void {
        console.log({event, project, version, language});

        event.stopPropagation();

        const state = new ProjectInfo(project.name, version.number, language.name);
        state.setArchiveFile(language.archivePath);
        state.setindexFile(language.indexPath);

        this.projectSelection.emit(state);
    }
}
